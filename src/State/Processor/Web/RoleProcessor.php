<?php

declare(strict_types=1);

namespace App\State\Processor\Web;


/// --- Main namespaces --- ///
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Override;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;


/// --- Internal namespaces --- ///
use App\Dto\Input\Web\RoleCreateDto;
use App\Dto\Input\Web\RoleUpdateDto;
use App\Dto\Main\Web\RoleResourceDto;
use App\Entity\Web\RoleEntity;
use App\Interface\Web\RoleDuplicateValidatorInterface;
use App\Repository\Web\RoleRepository;


/**
 * @implements ProcessorInterface<RoleCreateDto|RoleUpdateDto|mixed, RoleResourceDto|null>
 */
final readonly class RoleProcessor implements ProcessorInterface
{
	public function __construct(
		private RoleRepository					$repository,
		private ObjectMapperInterface			$mapper,
		private RequestStack					$requestStack,
		private RoleDuplicateValidatorInterface	$duplicateValidator,
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

	#[Override]
	public function process(
		mixed		$data,
		Operation	$operation,
		array		$payload	= [],
		array		$context	= [],
	): ?RoleResourceDto
	{
		return match (true) {
			$data instanceof RoleCreateDto	=> $this->handlePost(dto: $data),
			$data instanceof RoleUpdateDto	=> $this->handlePatch(dto: $data, payload: $payload),
			$operation instanceof Delete	=> $this->handleDelete(payload: $payload),
			default							=> throw new InvalidArgumentException('Unsupported opearation or input DTO type.'),
		};
	}


	private function handlePost(RoleCreateDto $dto): RoleResourceDto
	{
		$this->duplicateValidator->validatePost(payload: $this->getDecodedPayload());

		$roleEntity = new RoleEntity();

		$roleEntity->setName(name: $dto->name);
		$roleEntity->setDescription(description: $dto->description);
		$roleEntity->setCreateOn(
			createOn: new DateTimeImmutable(
				datetime: 'now',
				timezone: new DateTimeZone(timezone: 'UTC'),
			)
		);

		$this->repository->save(
			entity: $roleEntity,
			flush: true
		);

		return $this->mapper->map(
			source: $roleEntity,
			target: RoleResourceDto::class,
		);
	}

	private function handlePatch(
		RoleUpdateDto $dto,
		array $payload,
	): RoleResourceDto
	{
		$roleId = (int) ($payload['id'] ?? 0);
		$roleEntity = $this->repository->find(id: $roleId);

		if (!$roleEntity) {
			throw new NotFoundHttpException(
				message: sprintf(
					'Role with ID [%d] not found.',
					$roleId,
				),
			);
		}

		$roleDecodedPayload = $this->getDecodedPayload();

		// 400 if another entity already uses the same 'name' value
		$this->duplicateValidator->validatePatch(
			payload: $roleDecodedPayload,
			id: $roleId
		);

		// Apply the PATCH request since we allowed 'description' field value to be NULL
		if (array_key_exists(
			key: 'name',
			array: $roleDecodedPayload,
		)) {
			$roleEntity->setName(name: $dto->name);
		}

		if (array_key_exists(
			key: 'description',
			array: $roleDecodedPayload,
		)) {
			$roleEntity->setDescription(description: $dto->description);
		}

		$this->repository->save(
			entity: $roleEntity,
			flush: true,
		);

		return $this->mapper->map(
			source: $roleEntity,
			target: RoleResourceDto::class,
		);
	}

	private function handleDelete(array $payload): null
	{
		$roleId = (int) ($payload['id'] ?? 0);
		$roleEntity = $this->repository->find(id: $roleId);

		if (!$roleEntity) {
			throw new NotFoundHttpException(
				sprintf(
					'Role with ID [%d] not found.',
					(int) $roleId,
				)
			);
		}

		$this->repository->remove(
			entity: $roleEntity,
			flush: true,
		);

		return null;
	}
}
