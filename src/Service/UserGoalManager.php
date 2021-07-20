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

    public function achieve(User $user, Goal $goal) {
        $userGoal = $this->userGoalRepository->findOneByUserAndGoal($user, $goal);
        
        if ($userGoal === null) {
            $userGoal = new UserGoal();
            $userGoal->setAchiever($user);
            $userGoal->setGoal($goal);
        }
        $userGoal->setIsAchieved(true);
        $this->entityManager->persist($userGoal);
        $this->entityManager->flush();
    }

    public function fillGoals(User $user, Goal ...$goals) {
        $userGoals = $this->userGoalRepository->findByGoalsAndUser($user, ...$goals);

        foreach ($goals as $goal) {
            $goal->setIsAchieved(false);
            foreach ($userGoals as $userGoal) {
                if ($goal == $userGoal->getGoal()) {
                    $goal->setIsAchieved($userGoal->getIsAchieved());
                    continue;
                }
            }
        }

        return $goals;
    }
}