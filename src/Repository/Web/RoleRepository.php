<?php

declare(strict_types=1);

namespace App\Repository\Web;


/// --- Main namespaces --- ///
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Web\RoleEntity;


/**
 * @extends ServiceEntityRepository<RoleEntity>
 */
class RoleRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, RoleEntity::class);
	}

	public function save(
		RoleEntity	$entity,
		bool		$flush = true,
	): void
	{
		$this->getEntityManager()->persist(object: $entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(
		RoleEntity	$entity,
		bool		$flush = true,
	): void
	{
		$this->getEntityManager()->remove(object: $entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}
}
