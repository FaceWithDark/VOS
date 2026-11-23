<?php

declare(strict_types=1);

namespace App\State\Processor\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


/// --- Type hint namespaces --- ///
use const FILTER_VALIDATE_INT;
use function filter_var;
use function sprintf;
use InvalidArgumentException;
use Override;


/// --- Internal namespaces --- ///
use App\Dto\Input\Web\RoleCreateDto;
use App\Dto\Input\Web\RoleUpdateDto;
use App\Dto\Main\Web\RoleResourceDto;
use App\Entity\Web\RoleEntity;
use App\Repository\Web\RoleRepository;
use App\Service\Web\RolePayloadHandler;


// @mago-format-ignore-end
/**
 * @implements ProcessorInterface<RoleCreateDto|RoleUpdateDto|mixed, RoleResourceDto|null>
 */
final readonly class RoleProcessor implements ProcessorInterface
{
	/** Initialises the instance. */
	public function __construct(
		private RoleRepository        $repository,
		private ObjectMapperInterface $mapper,
		private RolePayloadHandler    $handler,
	) {}

	/** Process. */
	#[Override]
	public function process(
		mixed     $data,
		Operation $operation,
		array     $payload = [],
		array     $context = [],
	): ?RoleResourceDto {
		return match (true) {
			$data instanceof RoleCreateDto => $this->handlePost(dto: $data),
			$data instanceof RoleUpdateDto => $this->handlePatch(
				dto: $data,
				payload: $payload,
			),
			$operation instanceof Delete   => $this->handleDelete(payload: $payload),
			default => throw new InvalidArgumentException(message: 'Unsupported opearation or input DTO type.'),
		};
	}

	/**
	 * @param array<array-key, mixed> $payload
	 */
	private function handleDelete(array $payload): null
	{
		$roleId = filter_var(
			value: $payload['id'] ?? 0,
			filter: FILTER_VALIDATE_INT,
		);
		$roleId     = $roleId === false ? 0 : $roleId;
		$roleEntity = $this->repository->find(id: $roleId);

		if ($roleEntity === null) {
			throw new NotFoundHttpException(
				message: sprintf(
					'Role with ID [%d] not found.',
					$roleId,
				),
			);
		}

		$this->repository->remove(entity: $roleEntity);

		return null;
	}

	/**
	 * @param array<array-key, mixed> $payload
	 */
	private function handlePatch(
		RoleUpdateDto $dto,
		array         $payload,
	): RoleResourceDto {
		$roleId = filter_var(
			value: $payload['id'] ?? 0,
			filter: FILTER_VALIDATE_INT,
		);
		$roleId     = $roleId === false ? 0 : $roleId;
		$roleEntity = $this->repository->find(id: $roleId);

		if ($roleEntity === null) {
			throw new NotFoundHttpException(
				message: sprintf(
					'Role with ID [%d] not found.',
					$roleId,
				),
			);
		}

		$decodedPayload = $this->handler->readPayload();

		$this->handler->validatePatch(
			payload: $decodedPayload,
			id: $roleId,
		);
		$this->handler->applyPatch(
			entity: $roleEntity,
			dto: $dto,
			payload: $decodedPayload,
		);

		$this->repository->save(entity: $roleEntity);

		return $this->mapper->map(
			source: $roleEntity,
			target: RoleResourceDto::class,
		);
	}

	/** Handle post. */
	private function handlePost(RoleCreateDto $dto): RoleResourceDto
	{
		$payload = $this->handler->readPayload();

		$this->handler->validatePost(payload: $payload);

		$roleEntity = new RoleEntity();

		$this->handler->applyCreate(
			entity: $roleEntity,
			dto: $dto,
		);

		$this->repository->save(entity: $roleEntity);

		return $this->mapper->map(
			source: $roleEntity,
			target: RoleResourceDto::class,
		);
	}
}
