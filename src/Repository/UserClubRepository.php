<?php

namespace App\Repository;

use App\Entity\Club;
use App\Entity\User;
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

    public function findOneByUserAndClub(User $user, Club $club): ?UserClub
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.member = :user')
            ->andWhere('u.club = :club')
            ->setParameter('user', $user)
            ->setParameter('club', $club)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOwner(Club $club): ?UserClub
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.club = :club')
            ->andWhere('u.isOwner = :owner')
            ->setParameter('club', $club)
            ->setParameter('owner', true)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
