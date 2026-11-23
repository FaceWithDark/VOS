<?php

declare(strict_types=1);

namespace App\Service\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;
use DateTimeZone;
use function array_key_exists;
use function is_array;
use function json_decode;
use function sprintf;


/// --- Internal namespaces --- ///
use App\Dto\Input\Web\UserCreateDto;
use App\Dto\Input\Web\UserUpdateDto;
use App\Entity\Web\RoleEntity;
use App\Entity\Web\UserEntity;
use App\Interface\Web\UserDuplicateValidatorInterface;
use App\Interface\Web\UserEmptyPayloadValidatorInterface;
use App\Interface\Web\UserForeignKeyValidatorInterface;
use App\Repository\Web\RoleRepository;


// @mago-format-ignore-end
/**
 * Reads, validates, and applies the user request payload, keeping the processor
 * itself focused on persistence and DTO mapping.
 */
final readonly class UserPayloadHandler
{
	/** Initialises the instance. */
	public function __construct(
		private RequestStack                       $requestStack,
		private RoleRepository                     $roleRepository,
		private UserDuplicateValidatorInterface    $duplicateValidator,
		private UserForeignKeyValidatorInterface   $foreignKeyValidator,
		private UserEmptyPayloadValidatorInterface $emptyPayloadValidator,
	) {}

	/** Applies the create DTO to the entity. */
	public function applyCreate(UserEntity $entity, UserCreateDto $dto): void
	{
		$entity->setId(id: $dto->id);
		$entity->setName(name: $dto->name);
		$entity->setAvatar(avatar: $dto->avatar);
		$entity->setRank(rank: $dto->rank);
		$entity->setCountryFlag(countryFlag: $dto->countryFlag);
		$entity->setRoleId(roleId: $this->resolveRole(roleId: $dto->roleId));
		$entity->setCreateOn(
			createOn: new DateTimeImmutable(
				datetime: 'now',
				timezone: new DateTimeZone(timezone: 'UTC'),
			),
		);
	}

	/**
	 * @param array<array-key, mixed> $payload
	 */
	public function applyPatch(UserEntity $entity, UserUpdateDto $dto, array $payload): void
	{
		if (
			array_key_exists(
				key: 'name',
				array: $payload,
			)
			&& $dto->name !== null
		) {
			$entity->setName(name: $dto->name);
		}

		if (
			array_key_exists(
				key: 'avatar',
				array: $payload,
			)
			&& $dto->avatar !== null
		) {
			$entity->setAvatar(avatar: $dto->avatar);
		}

		if (
			array_key_exists(
				key: 'rank',
				array: $payload,
			)
			&& $dto->rank !== null
		) {
			$entity->setRank(rank: $dto->rank);
		}

		if (
			array_key_exists(
				key: 'countryFlag',
				array: $payload,
			)
			&& $dto->countryFlag !== null
		) {
			$entity->setCountryFlag(countryFlag: $dto->countryFlag);
		}

		if (array_key_exists(
			key: 'roleId',
			array: $payload,
		)) {
			$entity->setRoleId(roleId: $this->resolveRole(roleId: $dto->roleId));
		}
	}

	/**
	 * @return array<array-key, mixed>
	 */
	public function readPayload(): array
	{
		$request = $this->requestStack->getCurrentRequest();

		if ($request === null) {
			return [];
		}

		$decoded = json_decode(
			json: $request->getContent(),
			associative: true,
		);

		if (is_array(value: $decoded)) {
			return $decoded;
		}

		return [];
	}

	/**
	 * @param array<array-key, mixed> $payload
	 */
	public function validatePatch(array $payload, int $id): void
	{
		$this->emptyPayloadValidator->validatePatch(payload: $payload);
		$this->duplicateValidator->validatePatch(
			payload: $payload,
			id: $id,
		);
		$this->foreignKeyValidator->validatePatch(
			payload: $payload,
			id: $id,
		);
	}

	/**
	 * @param array<array-key, mixed> $payload
	 */
	public function validatePost(array $payload): void
	{
		$this->duplicateValidator->validatePost(payload: $payload);
		$this->foreignKeyValidator->validatePost(payload: $payload);
	}

	/** Resolves the referenced role or fails with 400/404. */
	private function resolveRole(?int $roleId): RoleEntity
	{
		if ($roleId === null) {
			throw new BadRequestHttpException(message: 'Role ID must not be null.');
		}

		$roleEntity = $this->roleRepository->find(id: $roleId);

		if ($roleEntity === null) {
			throw new NotFoundHttpException(
				message: sprintf(
					'Role with ID [%d] not found.',
					$roleId,
				),
			);
		}

		return $roleEntity;
	}
}
