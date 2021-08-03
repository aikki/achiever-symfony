<?php
namespace App\Service;

use App\Entity\Goal;
use App\Entity\Milestone;
use App\Entity\User;
use App\Entity\UserGoal;
use App\Entity\UserMilestone;
use App\Repository\UserGoalRepository;
use App\Repository\UserMilestoneRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserMilestoneManager {

    private $entityManager;
    private $userMilestoneRepository;
    private $userGoalRepository;
    private $userGoalManager;

    public function __construct(EntityManagerInterface $entityManagerInterface, UserMilestoneRepository $userMilestoneRepository, UserGoalManager $userGoalManager, UserGoalRepository $userGoalRepository)
    {
        $this->entityManager = $entityManagerInterface;
        $this->userMilestoneRepository = $userMilestoneRepository;
        $this->userGoalManager = $userGoalManager;
        $this->userGoalRepository = $userGoalRepository;
    }

    public function achieve(User $user, Milestone $milestone) : bool {
        $userMilestone = $this->userMilestoneRepository->findOneByUserAndGoal($user, $milestone);

        if ($userMilestone === null) {
            $userMilestone = new UserMilestone();
            $userMilestone->setAchiever($user);
            $userMilestone->setMilestone($milestone);
        }

        $userMilestone->setIsAchieved(true);

        $userGoals = $this->userGoalRepository->findByGoalsAndUser($user, ...$milestone->getGoals());
        foreach ($userGoals as $goal) {
            if ($goal->getIsAchieved() === null) {
                $this->userGoalManager->fillGoals($user, ...$milestone->getGoals());
            }
            if (!$goal->getIsAchieved()) {
                return false;
            }
            $goal->setIsLocked(true);
            $this->entityManager->persist($goal);
        }

        $this->entityManager->persist($userMilestone);
        $this->entityManager->flush();
        return true;
    }

    public function fillMilestones(User $user, Milestone ...$milestones) {
        $userMilestones = $this->userMilestoneRepository->findByMilestonesAndUser($user, ...$milestones);

        foreach ($milestones as $milestone) {
            $milestone->setIsDone(false);
            foreach ($userMilestones as $userMilestone) {
                if ($milestone === $userMilestone->getMilestone()) {
                    $milestone->setIsDone($userMilestone->getIsAchieved());
                    break;
                }
            }
            $milestone->setCanBeDone(true);
            foreach ($milestone->getGoals() as $goal) {
                if ($goal->getIsAchieved() === null) {
                    $this->userGoalManager->fillGoals($user, ...$milestone->getGoals());
                }
                if (!$goal->getIsAchieved()) {
                    $milestone->setCanBeDone(false);
                    break;
                }
            }
        }

        return $milestones;
    }
}