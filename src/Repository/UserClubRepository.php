<?php

namespace App\Repository;

use App\Entity\UserClub;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserClub|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserClub|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserClub[]    findAll()
 * @method UserClub[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserClubRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserClub::class);
    }

    // /**
    //  * @return UserClub[] Returns an array of UserClub objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserClub
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
