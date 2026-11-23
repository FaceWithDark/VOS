<?php

declare(strict_types=1);

namespace App\Repository\Catalog;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Catalog\TournamentEntity;


// @mago-format-ignore-end
/**
 * @extends ServiceEntityRepository<TournamentEntity>
 */
class TournamentRepository extends ServiceEntityRepository
{
	/** Initialises the instance. */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct(
			registry: $registry,
			entityClass: TournamentEntity::class,
		);
	}

	/** Remove. */
	public function remove(TournamentEntity $entity): void
	{
		$this->getEntityManager()->remove(object: $entity);
		$this->getEntityManager()->flush();
	}

	/** Save. */
	public function save(TournamentEntity $entity): void
	{
		$this->getEntityManager()->persist(object: $entity);
		$this->getEntityManager()->flush();
	}
}
