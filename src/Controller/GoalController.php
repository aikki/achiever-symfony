<?php

namespace App\Controller;

use App\Entity\Goal;
use App\Service\UserGoalManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoalController extends AbstractController
{
    #[Route('/goal', name: 'goal')]
    public function index(): Response
    {
        return $this->render('goal/index.html.twig', [
            'controller_name' => 'GoalController',
        ]);
    }

    #[Route('/goal/achieve/{id<\d+>}', name: 'goal_achieve')]
    public function achieve(Goal $goal, UserGoalManager $userGoalManager): Response
    {
        if ($this->getUser()->isMember($goal->getClub())) {
            $userGoalManager->achieve($this->getUser(), $goal);
        }

        return $this->redirectToRoute('club_show', ['id' => $goal->getClub()->getId()]);
    }
}
