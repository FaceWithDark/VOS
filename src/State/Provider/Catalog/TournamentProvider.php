<?php

declare(strict_types=1);

namespace App\State\Provider\Catalog;


/// --- Main namespaces --- ///
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Catalog\TournamentEntity;
use Override;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Repository\Catalog\TournamentRepository;
use App\Dto\Main\Catalog\TournamentResourceDto;


/**
 * @implements ProviderInterface<TournamentResourceDto>
 */
final readonly class TournamentProvider implements ProviderInterface
{
	public function __construct(
		private TournamentRepository $repository,
		private ObjectMapperInterface $mapper,
	) {}

	#[Override]
	public function provide(
		Operation	$operation,
		array		$uriVariables = [],
		array		$context = [],
	): object|array|null
	{
		// Individual GET: /v1/tournaments/{id}
		if(isset($uriVariables['id'])) {
			$entity = $this->repository->find(id: $uriVariables['id']);

			if(!$entity) {
				// API Platform automatically triggers 404 status code when not found
				return null;
			}

			return $this->mapper->map(
				source: $entity,
				target: TournamentResourceDto::class,
			);
		}

		// Collection GET: /v1/tournaments
		$entities = $this->repository->findAll();

		return array_map(
			callback: fn(TournamentEntity $entity) => $this->mapper->map(
				source: $entity,
				target: TournamentResourceDto::class,
			),
			array: $entities,
		);
	}
}
