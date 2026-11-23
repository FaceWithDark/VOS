<?php

declare(strict_types=1);

namespace App\State\Processor\Catalog;

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
use App\Dto\Input\Catalog\TournamentCreateDto;
use App\Dto\Input\Catalog\TournamentUpdateDto;
use App\Dto\Main\Catalog\TournamentResourceDto;
use App\Entity\Catalog\TournamentEntity;
use App\Repository\Catalog\TournamentRepository;
use App\Service\Catalog\TournamentPayloadHandler;


// @mago-format-ignore-end
/**
 * @implements ProcessorInterface<TournamentCreateDto|TournamentUpdateDto|mixed, TournamentResourceDto|null>
 */
final readonly class TournamentProcessor implements ProcessorInterface
{
	/** Initialises the instance. */
	public function __construct(
		private TournamentRepository     $repository,
		private ObjectMapperInterface    $mapper,
		private TournamentPayloadHandler $handler,
	) {}

	/** Process. */
	#[Override]
	public function process(
		mixed     $data,
		Operation $operation,
		array     $payload = [],
		array     $context = [],
	): ?TournamentResourceDto {
		return match (true) {
			$data instanceof TournamentCreateDto => $this->handlePost(dto: $data),
			$data instanceof TournamentUpdateDto => $this->handlePatch(
				dto: $data,
				payload: $payload,
			),
			$operation instanceof Delete         => $this->handleDelete(payload: $payload),
			default => throw new InvalidArgumentException(message: 'Unsupported opearation or input DTO type.'),
		};
	}

	/**
	 * @param array<array-key, mixed> $payload
	 */
	private function handleDelete(array $payload): null
	{
		$tournamentId = filter_var(
			value: $payload['id'] ?? 0,
			filter: FILTER_VALIDATE_INT,
		);
		$tournamentId     = $tournamentId === false ? 0 : $tournamentId;
		$tournamentEntity = $this->repository->find(id: $tournamentId);

		if ($tournamentEntity === null) {
			throw new NotFoundHttpException(
				message: sprintf(
					'Tournament with ID [%d] not found.',
					$tournamentId,
				),
			);
		}

		$this->repository->remove(entity: $tournamentEntity);

		return null;
	}

	/**
	 * @param array<array-key, mixed> $payload
	 */
	private function handlePatch(
		TournamentUpdateDto $dto,
		array               $payload,
	): TournamentResourceDto {
		$tournamentId = filter_var(
			value: $payload['id'] ?? 0,
			filter: FILTER_VALIDATE_INT,
		);
		$tournamentId     = $tournamentId === false ? 0 : $tournamentId;
		$tournamentEntity = $this->repository->find(id: $tournamentId);

		if ($tournamentEntity === null) {
			throw new NotFoundHttpException(
				message: sprintf(
					'Tournament with ID [%d] not found.',
					$tournamentId,
				),
			);
		}

		$decodedPayload = $this->handler->readPayload();

		$this->handler->validatePatch(
			payload: $decodedPayload,
			id: $tournamentId,
		);
		$this->handler->applyPatch(
			entity: $tournamentEntity,
			dto: $dto,
			payload: $decodedPayload,
		);

		$this->repository->save(entity: $tournamentEntity);

		return $this->mapper->map(
			source: $tournamentEntity,
			target: TournamentResourceDto::class,
		);
	}

	/** Handle post. */
	private function handlePost(TournamentCreateDto $dto): TournamentResourceDto
	{
		$payload = $this->handler->readPayload();

		$this->handler->validatePost(payload: $payload);

		$tournamentEntity = new TournamentEntity();

		$this->handler->applyCreate(
			entity: $tournamentEntity,
			dto: $dto,
		);

		$this->repository->save(entity: $tournamentEntity);

		return $this->mapper->map(
			source: $tournamentEntity,
			target: TournamentResourceDto::class,
		);
	}
}
