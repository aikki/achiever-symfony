<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\UserClub;
use App\Form\ClubType;
use App\Form\CodeJoinFormType;
use App\Repository\ClubRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClubController extends AbstractController
{
    #[Route('/clubs', name: 'clubs')]
    public function index(Request $request, ClubRepository $clubRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $clubRepository->getClubPaginator($offset);

        $codeJoin = $this->createForm(CodeJoinFormType::class);
        $codeJoin->handleRequest($request);

        if ($codeJoin->isSubmitted() && $codeJoin->isValid()) {
            
        }

        return $this->render('club/index.html.twig', [
            'clubs' => $paginator,
            'prev' => $offset - ClubRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + ClubRepository::PAGINATOR_PER_PAGE),
            'perPage' => ClubRepository::PAGINATOR_PER_PAGE,
            'pages' => floor(count($paginator) / ClubRepository::PAGINATOR_PER_PAGE),
            'offset' => $offset
        ]);
    }
    
    #[Route('/clubs/{id<\d+>}', name: 'club_show')]
    public function show(Club $club): Response
    {
        return $this->render('club/show.html.twig', [
            'controller_name' => $club->getName(),
        ]);
    }

    #[Route('/clubs/join/{id<\d+>}', name: 'club_join')]
    public function join(Club $club, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->urlGenerator->generate('security_login'));
        }

        try {
            $userClub = new UserClub($this->getUser(), $club);
            $entityManager->persist($userClub);
            $entityManager->flush();

            $this->addFlash('success', 'Dołączono do klubu.');
            return new RedirectResponse($this->generateUrl('club_show', ['id' => $club->getId()]));
        } catch (Exception $e) {
            $this->addFlash('error', "Wystąpił błąd przy dołączaniu do klubu.");
            return new RedirectResponse($this->generateUrl('club'));
        }

    }
    
    #[Route('/clubs/create', name: 'club_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $club = new Club();
        $club->setIsPublic(true);
        $form = $this->createForm(ClubType::class, $club);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($club);
            $entityManager->flush();

            $userClub = new UserClub($this->getUser(), $club);
            $userClub->setIsOwner(true);
            $entityManager->persist($userClub);
            $entityManager->flush();
            
            return $this->redirectToRoute('club_show', ['id' => $club->getId()]);
        }

        return $this->render('club/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
