<?php

declare(strict_types=1);


namespace App\Repository\Tourney;

use App\Entity\Tourney\Beatmap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Beatmap>
 */
class BeatmapRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Beatmap::class);
	}

	//    /**
	//     * @return Beatmap[] Returns an array of Beatmap objects
	//     */
	//    public function findByExampleField($value): array
	//    {
	//        return $this->createQueryBuilder('b')
	//            ->andWhere('b.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->orderBy('b.id', 'ASC')
	//            ->setMaxResults(10)
	//            ->getQuery()
	//            ->getResult()
	//        ;
	//    }

	//    public function findOneBySomeField($value): ?Beatmap
	//    {
	//        return $this->createQueryBuilder('b')
	//            ->andWhere('b.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->getQuery()
	//            ->getOneOrNullResult()
	//        ;
	//    }
}
