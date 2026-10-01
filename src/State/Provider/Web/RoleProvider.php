<?php

declare(strict_types=1);

namespace App\State\Provider\Web;


/// --- Main namespaces --- ///
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Override;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Web\RoleEntity;
use App\Repository\Web\RoleRepository;
use App\Dto\Main\Web\RoleResourceDto;


/**
 * @implements ProviderInterface<RoleResourceDto>
 */
final readonly class RoleProvider implements ProviderInterface
{
	public function __construct(
		private RoleRepository			$repository,
		private ObjectMapperInterface	$mapper,
	) {}

	#[Override]
	public function provide(
		Operation	$operation,
		array		$uriVariables = [],
		array		$context = [],
	): object|array|null
	{
		// Individual GET: /v1/roles/{id}
		if(isset($uriVariables['id'])) {
			$entity = $this->repository->find(id: $uriVariables['id']);

			if(!$entity) {
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
