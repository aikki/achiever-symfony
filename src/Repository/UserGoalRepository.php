<?php

namespace App\Repository;

use App\Entity\Goal;
use App\Entity\User;
use App\Entity\UserGoal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserGoal|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGoal|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGoal[]    findAll()
 * @method UserGoal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserGoalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserGoal::class);
    }

    public function findByGoalsAndUser(User $user, Goal ...$goals)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.achiever = :user')
            ->andWhere('u.goal IN (:goals)')
            ->setParameter('user', $user)
            ->setParameter('goals', $goals)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByUserAndGoal(User $user, Goal $goal): ?UserGoal
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.achiever = :user')
            ->andWhere('u.goal = :goal')
            ->setParameter('user', $user)
            ->setParameter('goal', $goal)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
