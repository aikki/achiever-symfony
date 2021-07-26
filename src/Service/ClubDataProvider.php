<?php
namespace App\Service;

use App\Entity\ClubData;
use App\Entity\Goal;
use App\Entity\User;
use App\Repository\ClubRepository;
use App\Repository\GoalRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class ClubDataProvider {
    private $clubRepository;
    private $goalRepository;
    private $userGoalManager;
    private $token;
    private ?array $myClubs = null;

    public function __construct(ClubRepository $clubRepository, TokenStorageInterface $tokenStorage, GoalRepository $goalRepository, UserGoalManager $userGoalManager) {
        $this->clubRepository = $clubRepository;
        $this->goalRepository = $goalRepository;
        $this->userGoalManager = $userGoalManager;
        $this->token = $tokenStorage->getToken();
    }

    private function loadMyClubs() {
        if ($this->token) {
            $userClubs = $this->token->getUser()->getUserClubs()->toArray();
            if (!empty($userClubs)) {
                $clubs = array_map(function ($c) {
                    return $c->getClub()->setIsOwner($c->getIsOwner());
                }, $userClubs);
                usort($clubs, function ($a, $b) {
                    return strcmp($a->getName(), $b->getName());
                });
            }
            $this->myClubs = $clubs;
        }
    }

    public function getMyClubs($forceReload = false) {
        if ($this->myClubs === null || $forceReload) {
            $this->loadMyClubs();
        }
        return $this->myClubs;
    }

    public function getMyClubsData() {
        $clubsData = [];
        foreach ($this->getMyClubs() as $club) {
            $goals = $club->getGoals();
            $this->userGoalManager->fillGoals($this->token->getUser(), ...$goals);
            $achieved = 0;
            $goalsAchieved = [];
            $goalsNotAchieved = [];
            foreach ($goals as $goal) {
                if ($goal->getIsAchieved()) {
                    $achieved++;
                    $goalsAchieved[] = $goal;
                } else {
                    $goalsNotAchieved[] = $goal;
                }
            }
            
            $data = new ClubData();
            $data->setClub($club);
            $data->setGoals($goals);
            $data->setGoalsAchievedNumber($achieved);
            $data->setGoalsAchieved($goalsAchieved);
            $data->setGoalsNotAchieved($goalsNotAchieved);
            $clubsData[] = $data;
        }
        return $clubsData;
    }
}