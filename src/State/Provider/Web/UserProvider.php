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
use App\Dto\Main\Web\UserResourceDto;
use App\Entity\Web\UserEntity;
use App\Repository\Web\UserRepository;


// @mago-format-ignore-end
/**
 * @implements ProviderInterface<UserResourceDto>
 */
final readonly class UserProvider implements ProviderInterface
{
	/** Initialises the instance. */
	public function __construct(
		private UserRepository        $repository,
		private ObjectMapperInterface $mapper,
	) {}

	/** Provide. */
	#[Override]
	public function provide(
		Operation $operation,
		array     $uriVariables = [],
		array     $context = [],
	): object|array|null {
		// Individual GET: /v1/users/{id}
		$id = $uriVariables['id'] ?? null;

		if ($id !== null) {
			$entity = $this->repository->find(id: $id);

			if ($entity === null) {
				// API Platform automatically triggers 404 status code when not found
				return null;
			}

			return $this->mapper->map(
				source: $entity,
				target: UserResourceDto::class,
			);
		}

		// Collection GET: /v1/users
		$entities = $this->repository->findAll();

		return array_map(
			callback: fn(UserEntity $entity) => $this->mapper->map(
				source: $entity,
				target: UserResourceDto::class,
			),
			array: $entities,
		);
	}
}
