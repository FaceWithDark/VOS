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
use App\Entity\Web\UserEntity;
use App\Repository\Web\UserRepository;
use App\Dto\Main\Web\UserResourceDto;


/**
 * @implements ProviderInterface<UserResourceDto>
 */
final readonly class UserProvider implements ProviderInterface
{
	public function __construct(
		private UserRepository			$repository,
		private ObjectMapperInterface	$mapper,
	) {}

	#[Override]
	public function provide(
		Operation	$operation,
		array		$uriVariables = [],
		array		$context = [],
	): object|array|null
	{
		// Individual GET: /v1/users/{id}
		if(isset($uriVariables['id'])) {
			$entity = $this->repository->find(id: $uriVariables['id']);

			if(!$entity) {
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
