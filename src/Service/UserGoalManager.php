<?php
namespace App\Service;

use App\Entity\Goal;
use App\Entity\User;
use App\Entity\UserGoal;
use App\Repository\UserGoalRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserGoalManager {

    private $entityManager;
    private $userGoalRepository;

    public function __construct(EntityManagerInterface $entityManagerInterface, UserGoalRepository $userGoalRepository)
    {
        $this->entityManager = $entityManagerInterface;
        $this->userGoalRepository = $userGoalRepository;
    }

    private function findOrCreateUserGoal(User $user, Goal $goal): UserGoal {
        $userGoal = $this->userGoalRepository->findOneByUserAndGoal($user, $goal);
        
        if ($userGoal === null) {
            $userGoal = new UserGoal();
            $userGoal->setAchiever($user);
            $userGoal->setGoal($goal);
        }

        return $userGoal;
    }

    public function achieve(User $user, Goal $goal) {
        $userGoal = $this->findOrCreateUserGoal($user, $goal);
        $userGoal->setIsAchieved(true);
        $userGoal->setIsLocked(false);
        $this->entityManager->persist($userGoal);
        $this->entityManager->flush();
    }

    public function forget(User $user, Goal $goal) : bool {
        $userGoal = $this->findOrCreateUserGoal($user, $goal);
        if ($userGoal->getIsLocked()) {
            return false;
        }
        $userGoal->setIsAchieved(false);
        $this->entityManager->persist($userGoal);
        $this->entityManager->flush();
        return true;
    }

    public function fillGoals(User $user, Goal ...$goals) {
        $goals = array_filter($goals, function ($goal) {
            return $goal->getIsAchieved() === null;
        });
        $userGoals = $this->userGoalRepository->findByGoalsAndUser($user, ...$goals);

        foreach ($goals as $goal) {
            $goal->setIsAchieved(false);
            $goal->setIsLocked(false);
            foreach ($userGoals as $userGoal) {
                if ($goal === $userGoal->getGoal()) {
                    $goal->setIsAchieved($userGoal->getIsAchieved());
                    $goal->setIsLocked($userGoal->getIsLocked());
                    break;
                }
            }
        }

        return $goals;
    }
}