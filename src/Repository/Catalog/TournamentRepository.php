<?php

declare(strict_types=1);

namespace App\Repository\Catalog;


/// --- Main namespaces --- ///
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Catalog\TournamentEntity;


/**
 * @extends ServiceEntityRepository<TournamentEntity>
 */
class TournamentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TournamentEntity::class);
    }

	public function save(
		TournamentEntity	$entity,
		bool				$flush = true,
	): void
	{
		$this->getEntityManager()->persist(object: $entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}
}
