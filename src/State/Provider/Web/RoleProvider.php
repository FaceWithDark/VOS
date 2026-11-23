<?php

declare(strict_types=1);

namespace App\State\Provider\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


/// --- Type hint namespaces --- ///
use function array_map;
use Override;


/// --- Internal namespaces --- ///
use App\Dto\Main\Web\RoleResourceDto;
use App\Entity\Web\RoleEntity;
use App\Repository\Web\RoleRepository;


// @mago-format-ignore-end
/**
 * @implements ProviderInterface<RoleResourceDto>
 */
final readonly class RoleProvider implements ProviderInterface
{
	/** Initialises the instance. */
	public function __construct(
		private RoleRepository        $repository,
		private ObjectMapperInterface $mapper,
	) {}

	/** Provide. */
	#[Override]
	public function provide(
		Operation $operation,
		array     $uriVariables = [],
		array     $context = [],
	): object|array|null {
		// Individual GET: /v1/roles/{id}
		$id = $uriVariables['id'] ?? null;

		if ($id !== null) {
			$entity = $this->repository->find(id: $id);

			if ($entity === null) {
				// API Platform automatically triggers 404 status code when not found
				return null;
			}

			return $this->mapper->map(
				source: $entity,
				target: RoleResourceDto::class,
			);
		}

		// Collection GET: /v1/roles
		$entities = $this->repository->findAll();

		return array_map(
			callback: fn(RoleEntity $entity) => $this->mapper->map(
				source: $entity,
				target: RoleResourceDto::class,
			),
			array: $entities,
		);
	}
}
