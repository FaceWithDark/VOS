<?php

declare(strict_types=1);

namespace App\Repository\Tourney;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Tourney\Mod;


// @mago-format-ignore-end
/**
 * @extends ServiceEntityRepository<Mod>
 */
class ModRepository extends ServiceEntityRepository
{
	/** Initialises the instance. */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct(
			$registry,
			Mod::class,
		);
	}

	//    /**
	//     * @return Mod[] Returns an array of Mod objects
	//     */
	//    public function findByExampleField($value): array
	//    {
	//        return $this->createQueryBuilder('m')
	//            ->andWhere('m.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->orderBy('m.id', 'ASC')
	//            ->setMaxResults(10)
	//            ->getQuery()
	//            ->getResult()
	//        ;
	//    }
	//    public function findOneBySomeField($value): ?Mod
	//    {
	//        return $this->createQueryBuilder('m')
	//            ->andWhere('m.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->getQuery()
	//            ->getOneOrNullResult()
	//        ;
	//    }
}
