<?php

declare(strict_types=1);

namespace App\State\Provider\Catalog;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


/// --- Type hint namespaces --- ///
use function array_map;
use Override;


/// --- Internal namespaces --- ///
use App\Dto\Main\Catalog\TournamentResourceDto;
use App\Entity\Catalog\TournamentEntity;
use App\Repository\Catalog\TournamentRepository;


// @mago-format-ignore-end
/**
 * @implements ProviderInterface<TournamentResourceDto>
 */
final readonly class TournamentProvider implements ProviderInterface
{
	/** Initialises the instance. */
	public function __construct(
		private TournamentRepository  $repository,
		private ObjectMapperInterface $mapper,
	) {}

	/** Provide. */
	#[Override]
	public function provide(
		Operation $operation,
		array     $uriVariables = [],
		array     $context = [],
	): object|array|null {
		// Individual GET: /v1/tournaments/{id}
		$id = $uriVariables['id'] ?? null;

		if ($id !== null) {
			$entity = $this->repository->find(id: $id);

			if ($entity === null) {
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
