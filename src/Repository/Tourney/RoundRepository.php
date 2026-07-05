<?php

declare(strict_types=1);


namespace App\Repository\Tourney;

use App\Entity\Tourney\Round;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Round>
 */
class RoundRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Round::class);
	}

	//    /**
	//     * @return Round[] Returns an array of Round objects
	//     */
	//    public function findByExampleField($value): array
	//    {
	//        return $this->createQueryBuilder('r')
	//            ->andWhere('r.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->orderBy('r.id', 'ASC')
	//            ->setMaxResults(10)
	//            ->getQuery()
	//            ->getResult()
	//        ;
	//    }

	//    public function findOneBySomeField($value): ?Round
	//    {
	//        return $this->createQueryBuilder('r')
	//            ->andWhere('r.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->getQuery()
	//            ->getOneOrNullResult()
	//        ;
	//    }
}
