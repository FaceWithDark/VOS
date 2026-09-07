<?php

declare(strict_types=1);

namespace App\State\Processor\Catalog;


/// --- Main namespaces --- ///
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Override;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;


/// --- Internal namespaces --- ///
use App\Dto\Input\Catalog\TournamentCreateDto;
use App\Dto\Input\Catalog\TournamentUpdateDto;
use App\Dto\Main\Catalog\TournamentResourceDto;
use App\Entity\Catalog\TournamentEntity;
use App\Repository\Catalog\TournamentRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @implements ProcessorInterface<TournamentCreateDto|TournamentUpdateDto|mixed, TournamentResourceDto|null>
 */
final readonly class TournamentProcessor implements ProcessorInterface
{
	public function __construct(
		private TournamentRepository $repository,
		private ObjectMapperInterface $mapper,
	) {}

	#[Override]
	public function process(
		mixed		$data,
		Operation	$operation,
		array		$uriVariables	= [],
		array		$context		= [],
	): ?TournamentResourceDto
	{
		return match (true) {
			$data instanceof TournamentCreateDto	=> $this->handleCreatePayload(dto: $data),
			$data instanceof TournamentUpdateDto	=> $this->handleUpdatePayload(dto: $data, payload: $uriVariables),
			$operation instanceof Delete			=> $this->handleDeletePayload(payload: $uriVariables),
			default									=> throw new InvalidArgumentException('Unsupported opearation or input DTO type.'),
		};
	}


	private function handleCreatePayload(TournamentCreateDto $dto): TournamentResourceDto
	{
		$entity = new TournamentEntity();

		$entity->setName(name: $dto->name);
		$entity->setDescription(description: $dto->description);
		$entity->setCreateOn(
			createOn: new DateTimeImmutable(
				datetime: 'now',
				timezone: new DateTimeZone(timezone: 'UTC'),
			)
		);

		$this->repository->save(
			entity: $entity,
			flush: true,
		);

		return $this->mapper->map(
			source: $entity,
			target: TournamentResourceDto::class,
		);
	}

	private function handleUpdatePayload(
		TournamentUpdateDto $dto,
		array $payload,
	): TournamentResourceDto
	{
		$tournamentId = ((int) $payload['id']) ?? null;
		$entity = $this->repository->find(id: $tournamentId);

		if (!$entity) {
			throw new NotFoundHttpException(
				sprintf(
					'Tournament with ID [%d] not found',
					(int) $tournamentId,
				)
			);
		}

		if ($dto->name !== null) {
			$entity->setName(name: $dto->name);
		}

		// NOTE: 'description' can be NULL, hence no validation checks here

		$this->repository->save(
			entity: $entity,
			flush: true,
		);

		return $this->mapper->map(
			source: $entity,
			target: TournamentResourceDto::class,
		);
	}

	private function handleDeletePayload(array $payload): null
	{
		$tournamentId = ((int) $payload['id']) ?? null;
		$entity = $this->repository->find(id: $tournamentId);

		if (!$entity) {
			throw new NotFoundHttpException(
				sprintf(
					'Tournament with ID [%d] not found',
					(int) $tournamentId,
				)
			);
		}

		$this->repository->remove(
			entity: $entity,
			flush: true,
		);

		return null;
	}
}
