<?php

declare(strict_types=1);

namespace App\State\Processor\Web;


/// --- Main namespaces --- ///
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Override;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;


/// --- Internal namespaces --- ///
use App\Dto\Input\Web\UserCreateDto;
use App\Dto\Input\Web\UserUpdateDto;
use App\Dto\Main\Web\UserResourceDto;
use App\Entity\Web\RoleEntity;
use App\Entity\Web\UserEntity;
use App\Interface\Web\UserDuplicateValidatorInterface;
use App\Interface\Web\UserEmptyPayloadValidatorInterface;
use App\Interface\Web\UserForeignKeyValidatorInterface;
use App\Repository\Web\RoleRepository;
use App\Repository\Web\UserRepository;


/**
 * @implements ProcessorInterface<UserCreateDto|UserUpdateDto|mixed, UserResourceDto|null>
 */
final readonly class UserProcessor implements ProcessorInterface
{
	public function __construct(
		private UserRepository					$repository,
		private RoleRepository					$roleRepository,
		private ObjectMapperInterface			$mapper,
		private RequestStack					$requestStack,
		private UserDuplicateValidatorInterface	$duplicateValidator,
		private UserForeignKeyValidatorInterface	$foreignKeyValidator,
		private UserEmptyPayloadValidatorInterface	$emptyPayloadValidator,
	) {}

	private function getDecodedPayload(): array
	{
		$request = $this->requestStack->getCurrentRequest();
		$decoded
			= $request
			? json_decode(
				json: $request->getContent(),
				associative: true,
			)
			: null;

		return is_array(value: $decoded) ? $decoded : [];
	}

	/**
	 * Resolves the incoming foreign key into an existing Role entity.
	 *
	 * NOTE:
	 * The FK validator normally rejects an unknown role first (404); this lookup
	 * keeps the processor safe for direct callers and owns the 400 for an
	 * explicit NULL, which a NOT NULL relation can never accept.
	 */
	private function resolveRole(?int $roleId): RoleEntity
	{
		if ($roleId === null) {
			throw new BadRequestHttpException(message: 'Role ID must not be null.');
		}

		$roleEntity = $this->roleRepository->find(id: $roleId);

		if (!$roleEntity) {
			throw new NotFoundHttpException(
				message: sprintf(
					'Role with ID [%d] not found.',
					$roleId,
				),
			);
		}

		return $roleEntity;
	}

	#[Override]
	public function process(
		mixed		$data,
		Operation	$operation,
		array		$payload	= [],
		array		$context	= [],
	): ?UserResourceDto
	{
		return match (true) {
			$data instanceof UserCreateDto	=> $this->handlePost(dto: $data),
			$data instanceof UserUpdateDto	=> $this->handlePatch(dto: $data, payload: $payload),
			$operation instanceof Delete	=> $this->handleDelete(payload: $payload),
			default							=> throw new InvalidArgumentException('Unsupported opearation or input DTO type.'),
		};
	}


	private function handlePost(UserCreateDto $dto): UserResourceDto
	{
		$userDecodedPayload = $this->getDecodedPayload();

		$this->duplicateValidator->validatePost(payload: $userDecodedPayload);
		$this->foreignKeyValidator->validatePost(payload: $userDecodedPayload);

		$userEntity = new UserEntity();

		$userEntity->setId(id: $dto->id);
		$userEntity->setName(name: $dto->name);
		$userEntity->setAvatar(avatar: $dto->avatar);
		$userEntity->setRank(rank: $dto->rank);
		$userEntity->setCountryFlag(countryFlag: $dto->countryFlag);
		$userEntity->setRoleId(roleId: $this->resolveRole(roleId: $dto->roleId));
		$userEntity->setCreateOn(
			createOn: new DateTimeImmutable(
				datetime: 'now',
				timezone: new DateTimeZone(timezone: 'UTC'),
			)
		);

		$this->repository->save(
			entity: $userEntity,
			flush: true
		);

		return $this->mapper->map(
			source: $userEntity,
			target: UserResourceDto::class,
		);
	}

	private function handlePatch(
		UserUpdateDto $dto,
		array $payload,
	): UserResourceDto
	{
		$userId = (int) ($payload['id'] ?? 0);
		$userEntity = $this->repository->find(id: $userId);

		if (!$userEntity) {
			throw new NotFoundHttpException(
				message: sprintf(
					'User with ID [%d] not found.',
					$userId,
				),
			);
		}

		$userDecodedPayload = $this->getDecodedPayload();

		// 400 for an empty request body (entity existence is already checked)
		$this->emptyPayloadValidator->validatePatch(payload: $userDecodedPayload);

		// 400 if another entity already uses the same 'name' value
		$this->duplicateValidator->validatePatch(
			payload: $userDecodedPayload,
			id: $userId
		);

		// 409/400 if the referenced role is already bound to another user (1:1)
		$this->foreignKeyValidator->validatePatch(
			payload: $userDecodedPayload,
			id: $userId,
		);

		// Apply only the fields that the client actually sent (partial update)
		if (array_key_exists(
			key: 'name',
			array: $userDecodedPayload,
		)) {
			$userEntity->setName(name: $dto->name);
		}

		if (array_key_exists(
			key: 'avatar',
			array: $userDecodedPayload,
		)) {
			$userEntity->setAvatar(avatar: $dto->avatar);
		}

		if (array_key_exists(
			key: 'rank',
			array: $userDecodedPayload,
		)) {
			$userEntity->setRank(rank: $dto->rank);
		}

		if (array_key_exists(
			key: 'countryFlag',
			array: $userDecodedPayload,
		)) {
			$userEntity->setCountryFlag(countryFlag: $dto->countryFlag);
		}

		if (array_key_exists(
			key: 'roleId',
			array: $userDecodedPayload,
		)) {
			$userEntity->setRoleId(roleId: $this->resolveRole(roleId: $dto->roleId));
		}

		$this->repository->save(
			entity: $userEntity,
			flush: true,
		);

		return $this->mapper->map(
			source: $userEntity,
			target: UserResourceDto::class,
		);
	}

	private function handleDelete(array $payload): null
	{
		$userId = (int) ($payload['id'] ?? 0);
		$userEntity = $this->repository->find(id: $userId);

		if (!$userEntity) {
			throw new NotFoundHttpException(
				sprintf(
					'User with ID [%d] not found.',
					(int) $userId,
				)
			);
		}

		$this->repository->remove(
			entity: $userEntity,
			flush: true,
		);

		return null;
	}
}
