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
use App\Dto\Input\Web\UserCreateDto;
use App\Dto\Input\Web\UserUpdateDto;
use App\Dto\Main\Web\UserResourceDto;
use App\Entity\Web\UserEntity;
use App\Repository\Web\UserRepository;
use App\Service\Web\UserPayloadHandler;


// @mago-format-ignore-end
/**
 * @implements ProcessorInterface<UserCreateDto|UserUpdateDto|mixed, UserResourceDto|null>
 */
final readonly class UserProcessor implements ProcessorInterface
{
	/** Initialises the instance. */
	public function __construct(
		private UserRepository        $repository,
		private ObjectMapperInterface $mapper,
		private UserPayloadHandler    $handler,
	) {}

	/** Process. */
	#[Override]
	public function process(
		mixed     $data,
		Operation $operation,
		array     $payload = [],
		array     $context = [],
	): ?UserResourceDto {
		return match (true) {
			$data instanceof UserCreateDto => $this->handlePost(dto: $data),
			$data instanceof UserUpdateDto => $this->handlePatch(
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
		$userId = filter_var(
			value: $payload['id'] ?? 0,
			filter: FILTER_VALIDATE_INT,
		);
		$userId     = $userId === false ? 0 : $userId;
		$userEntity = $this->repository->find(id: $userId);

		if ($userEntity === null) {
			throw new NotFoundHttpException(
				message: sprintf(
					'User with ID [%d] not found.',
					$userId,
				),
			);
		}

		$this->repository->remove(entity: $userEntity);

		return null;
	}

	/**
	 * @param array<array-key, mixed> $payload
	 */
	private function handlePatch(
		UserUpdateDto $dto,
		array         $payload,
	): UserResourceDto {
		$userId = filter_var(
			value: $payload['id'] ?? 0,
			filter: FILTER_VALIDATE_INT,
		);
		$userId     = $userId === false ? 0 : $userId;
		$userEntity = $this->repository->find(id: $userId);

		if ($userEntity === null) {
			throw new NotFoundHttpException(
				message: sprintf(
					'User with ID [%d] not found.',
					$userId,
				),
			);
		}

		$decodedPayload = $this->handler->readPayload();

		$this->handler->validatePatch(
			payload: $decodedPayload,
			id: $userId,
		);
		$this->handler->applyPatch(
			entity: $userEntity,
			dto: $dto,
			payload: $decodedPayload,
		);

		$this->repository->save(entity: $userEntity);

		return $this->mapper->map(
			source: $userEntity,
			target: UserResourceDto::class,
		);
	}

	/** Handle post. */
	private function handlePost(UserCreateDto $dto): UserResourceDto
	{
		$payload = $this->handler->readPayload();

		$this->handler->validatePost(payload: $payload);

		$userEntity = new UserEntity();

		$this->handler->applyCreate(
			entity: $userEntity,
			dto: $dto,
		);

		$this->repository->save(entity: $userEntity);

		return $this->mapper->map(
			source: $userEntity,
			target: UserResourceDto::class,
		);
	}
}
