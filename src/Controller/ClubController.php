<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\UserClub;
use App\Form\ClubType;
use App\Form\CodeFormType;
use App\Form\CodeJoinFormType;
use App\Form\JoinFormType;
use App\Repository\ClubRepository;
use App\Repository\UserClubRepository;
use App\Service\UserGoalManager;
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

        $codeJoin = $this->createForm(CodeFormType::class, null, [
            'action' => $this->generateUrl('club_join'),
        ]);
        

        return $this->render('club/index.html.twig', [
            'clubs' => $paginator,
            'prev' => $offset - ClubRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + ClubRepository::PAGINATOR_PER_PAGE),
            'perPage' => ClubRepository::PAGINATOR_PER_PAGE,
            'pages' => floor(count($paginator) / ClubRepository::PAGINATOR_PER_PAGE),
            'offset' => $offset,
            'codeJoin' => $codeJoin->createView(),
        ]);
    }
    
    #[Route('/clubs/{id<\d+>}', name: 'club_show')]
    public function show(Club $club, Request $request, UserClubRepository $userClubRepository, UserGoalManager $userGoalManager): Response
    {
        $userClub = $userClubRepository->findOneByUserAndClub($this->getUser(), $club);

        if ($userClub === null) {
            $this->addFlash('note', "Nie należysz do klubu {$club->getName()}");

            return $this->redirectToRoute('clubs');
        }

        $owner = $userClubRepository->findOwner($club);
        if ($owner === null) {
            $owner = '';
        } else {
            $owner = (string) $owner->getMember();
        }

        $goals = $userGoalManager->fillGoals($this->getUser(), ...$club->getGoals());

        return $this->render('club/show.html.twig', [
            'club' => $club,
            'is_owner' => $userClub->getIsOwner(),
            'owner' => $owner,
            'goals' => $goals,
        ]);
    }

    #[Route('/clubs/join/{code}', name: 'club_join_code')]
    public function join(Request $request, EntityManagerInterface $entityManager, ClubRepository $clubRepository, UserClubRepository $userClubRepository): Response
    {
        $code = $request->get('code');

        $club = $clubRepository->findOneByCode($code);

        if ($club instanceof Club) {
            $form = $this->createForm(CodeJoinFormType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $userClub = new UserClub($this->getUser(), $club);
                    $entityManager->persist($userClub);
                    $entityManager->flush();
        
                    $this->addFlash('success', 'Dołączono do klubu.');
                    return $this->redirectToRoute('club_show', ['id' => $club->getId()]);
                } catch (Exception $e) {
                    $this->addFlash('note', "Wystąpił błąd przy dołączaniu do klubu.");
                    return $this->redirectToRoute('clubs');
                }
            } else {
                $owner = $userClubRepository->findOwner($club);
                if ($owner === null) {
                    $owner = '';
                } else {
                    $owner = (string) $owner->getMember();
                }

                return $this->render('club/join.html.twig', [
                    'club' => $club,
                    'owner' => $owner,
                    'form' => $form->createView(),
                ]);
            }
        }

        $this->addFlash('note', "Wystąpił błąd przy dołączaniu do klubu.");
        return $this->redirectToRoute('clubs');
    }

    #[Route('/clubs/join', methods: [ 'POST' ], name: 'club_join')]
    public function codeJoin(Request $request): Response
    {
        $form = $this->createForm(CodeFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $code = $form->get('code')->getData();

            if (!empty($code)) {
                return $this->redirectToRoute('club_join_code', [ 'code' => $code ]);
            }
        }

        $this->addFlash('note', "Wystąpił błąd przy dołączaniu do klubu.");
        return $this->redirectToRoute('clubs');

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
    
    #[Route('/clubs/regenerate_code/{id<\d+>}', name: 'club_regenerate_code')]
    public function regenerateCode(Club $club, EntityManagerInterface $entityManager): Response
    {
        # if owner
        $club->regenerateJoinCode();
        $entityManager->persist($club);
        $entityManager->flush();
        
        return $this->redirectToRoute('club_show', ['id' => $club->getId()]);
    }
}
