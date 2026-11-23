<?php

declare(strict_types=1);

namespace App\Repository\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Web\RoleEntity;


// @mago-format-ignore-end
/**
 * @extends ServiceEntityRepository<RoleEntity>
 */
class RoleRepository extends ServiceEntityRepository
{
	/** Initialises the instance. */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct(
			registry: $registry,
			entityClass: RoleEntity::class,
		);
	}

	/** Remove. */
	public function remove(RoleEntity $entity): void
	{
		$this->getEntityManager()->remove(object: $entity);
		$this->getEntityManager()->flush();
	}

	/** Save. */
	public function save(RoleEntity $entity): void
	{
		$this->getEntityManager()->persist(object: $entity);
		$this->getEntityManager()->flush();
	}
}
