<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Goal;
use App\Entity\Milestone;
use App\Form\GoalType;
use App\Form\MilestoneType;
use App\Repository\UserClubRepository;
use App\Service\UserGoalManager;
use App\Service\UserMilestoneManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MilestoneController extends AbstractController
{
    #[Route('/clubs/{id<\d+>}/milestones/create', name: 'milestone_create')]
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
        
        $milestone = new Milestone();
        $milestone->setClub($club);
        $form = $this->createForm(MilestoneType::class, $milestone, [ 'club' => $club ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($milestone);
            $entityManager->flush();

            return $this->redirectToRoute('club_show_milestones', ['id' => $club->getId()]);
        }

        return $this->render('milestone/create.html.twig', [
            'form' => $form->createView(),
            'club' => $club,
            'is_owner' => $userClub->getIsOwner(),
        ]);
    }

    #[Route('/milestone/achieve/{id<\d+>}', name: 'milestone_achieve')]
    public function achieve(Milestone $milestone, UserMilestoneManager $userMilestoneManager): Response
    {
        if ($this->getUser()->isMember($milestone->getClub())) {
            if ($userMilestoneManager->achieve($this->getUser(), $milestone)) {
                $this->addFlash('success', "Pomyślnie odblokowano kamień milowy <strong><i class='bi {$milestone->getIconClassName()}'></i> {$milestone->getName()}</strong>!");
            } else {
                $this->addFlash('note', "Kamień milowy <strong><i class='bi {$milestone->getIconClassName()}'></i> {$milestone->getName()}</strong> nie może zostać odblokowany.");
            }
        }

        return $this->redirectToRoute('club_show_milestones', ['id' => $milestone->getClub()->getId()]);
    }
}
