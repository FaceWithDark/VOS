<?php

declare(strict_types=1);

namespace App\Repository\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Web\UserEntity;


// @mago-format-ignore-end
/**
 * @extends ServiceEntityRepository<UserEntity>
 */
class UserRepository extends ServiceEntityRepository
{
	/** Initialises the instance. */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct(
			registry: $registry,
			entityClass: UserEntity::class,
		);
	}

	/** Remove. */
	public function remove(UserEntity $entity): void
	{
		$this->getEntityManager()->remove(object: $entity);
		$this->getEntityManager()->flush();
	}

	/** Save. */
	public function save(UserEntity $entity): void
	{
		$this->getEntityManager()->persist(object: $entity);
		$this->getEntityManager()->flush();
	}
}
