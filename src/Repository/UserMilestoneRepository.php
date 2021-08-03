<?php

namespace App\Repository;

use App\Entity\Milestone;
use App\Entity\User;
use App\Entity\UserMilestone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserMilestone|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMilestone|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMilestone[]    findAll()
 * @method UserMilestone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMilestoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMilestone::class);
    }

    public function findOneByUserAndGoal(User $user, Milestone $milestone): ?UserMilestone
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.achiever = :user')
            ->andWhere('u.milestone = :milestone')
            ->setParameter('user', $user)
            ->setParameter('milestone', $milestone)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findByMilestonesAndUser(User $user, Milestone ...$milestones)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.achiever = :user')
            ->andWhere('u.milestone IN (:milestones)')
            ->setParameter('user', $user)
            ->setParameter('milestones', $milestones)
            ->getQuery()
            ->getResult()
            ;
    }
}
