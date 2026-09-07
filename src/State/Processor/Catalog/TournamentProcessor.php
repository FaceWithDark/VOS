<?php

declare(strict_types=1);

namespace App\State\Processor\Catalog;


/// --- Main namespaces --- ///
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
 * @implements ProcessorInterface<TournamentCreateDto, TournamentResourceDto>
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
	): TournamentResourceDto
	{
		if ($data instanceof TournamentCreateDto) {
			$entity = new TournamentEntity();

			$entity->setName(name: $data->name);
			$entity->setDescription(description: $data->description);
			$entity->setCreateOn(
				createOn: new DateTimeImmutable(
					datetime: 'now',
					timezone: new DateTimeZone(timezone: 'UTC'),
				)
			);
		} elseif ($data instanceof TournamentUpdateDto) {
			$tournamentId = $uriVariables['id'] ?? null;
			$entity = $this->repository->find(id: $tournamentId);

			if (!$entity) {
				throw new NotFoundHttpException(
					sprintf(
						'Tournament with ID [%d] not found',
						(int) $tournamentId,
					)
				);
			}

			if ($data->name !== null) {
				$entity->setName(name: $data->name);
			}

			if ($data->description !== null) {
				$entity->setDescription(description: $data->description);
			}
		} else {
			throw new InvalidArgumentException('Unexpected input DTO type.');
		}

		$this->repository->save(
			entity: $entity,
			flush: true
		);

		return $this->mapper->map(
			source: $entity,
			target: TournamentResourceDto::class,
		);
	}
}
