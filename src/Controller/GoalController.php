<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Goal;
use App\Form\GoalType;
use App\Repository\UserClubRepository;
use App\Service\UserGoalManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoalController extends AbstractController
{
    #[Route('/clubs/{id<\d+>}/goals/create', name: 'goal_create')]
    public function create(Club $club, Request $request, EntityManagerInterface $entityManager, UserClubRepository $userClubRepository): Response
    {
        $userClub = $userClubRepository->findOneByUserAndClub($this->getUser(), $club);
        if ($userClub === null) {
            $this->addFlash('note', "Nie należysz do klubu {$club->getName()}");

            return $this->redirectToRoute('clubs');
        }
        if (!$userClub->getIsOwner()) {
            $this->addFlash('note', "Nie jesteś właścicielem klubu {$club->getName()}");

            return $this->redirectToRoute('club_show', [ 'id' => $club->getId() ]);
        }
        
        $goal = new Goal();
        $goal->setClub($club);
        $form = $this->createForm(GoalType::class, $goal);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($goal);
            $entityManager->flush();

            return $this->redirectToRoute('club_show', ['id' => $club->getId()]);
        }

        return $this->render('goal/create.html.twig', [
            'form' => $form->createView(),
            'club' => $club,
            'is_owner' => $userClub->getIsOwner(),
        ]);
    }

    #[Route('/goal/achieve/{id<\d+>}', name: 'goal_achieve')]
    public function achieve(Goal $goal, UserGoalManager $userGoalManager): Response
    {
        if ($this->getUser()->isMember($goal->getClub())) {
            $userGoalManager->achieve($this->getUser(), $goal);
            $this->addFlash('success', "Pomyślnie wykonano cel <strong><i class='bi {$goal->getIconClassName()}'></i> {$goal->getName()}</strong>!");
        }

        return $this->redirectToRoute('club_show', ['id' => $goal->getClub()->getId()]);
    }

    #[Route('/goal/forget/{id<\d+>}', name: 'goal_forget')]
    public function forget(Goal $goal, UserGoalManager $userGoalManager): Response
    {
        if ($this->getUser()->isMember($goal->getClub())) {
            if ($userGoalManager->forget($this->getUser(), $goal)) {
                $this->addFlash('success', "Cofnięto wykonanie celu <strong><i class='bi {$goal->getIconClassName()}'></i> {$goal->getName()}</strong>!");
            } else {
                $this->addFlash('note', "Nie można cofnąć celu <strong><i class='bi {$goal->getIconClassName()}'></i> {$goal->getName()}</strong>.");
            }
        }

        return $this->redirectToRoute('club_show', ['id' => $goal->getClub()->getId()]);
    }
}
