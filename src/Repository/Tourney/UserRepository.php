<?php

declare(strict_types=1);

namespace App\Repository\Tourney;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Tourney\User;


// @mago-format-ignore-end
/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
	/** Initialises the instance. */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct(
			$registry,
			User::class,
		);
	}

	//    /**
	//     * @return User[] Returns an array of User objects
	//     */
	//    public function findByExampleField($value): array
	//    {
	//        return $this->createQueryBuilder('u')
	//            ->andWhere('u.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->orderBy('u.id', 'ASC')
	//            ->setMaxResults(10)
	//            ->getQuery()
	//            ->getResult()
	//        ;
	//    }
	//    public function findOneBySomeField($value): ?User
	//    {
	//        return $this->createQueryBuilder('u')
	//            ->andWhere('u.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->getQuery()
	//            ->getOneOrNullResult()
	//        ;
	//    }
}
