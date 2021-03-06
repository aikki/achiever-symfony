<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\User;
use App\Entity\UserClub;
use App\Form\ClubType;
use App\Form\CodeFormType;
use App\Form\SingleSubmitFormType;
use App\Repository\ClubRepository;
use App\Repository\UserClubRepository;
use App\Service\UserGoalManager;
use App\Service\UserMilestoneManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClubController extends AbstractController
{
    private $userClubRepository;
    private $clubOwner;

    public function __construct(UserClubRepository $userClubRepository)
    {
        $this->userClubRepository = $userClubRepository;
    }

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
            'pages' => max(ceil(count($paginator) / ClubRepository::PAGINATOR_PER_PAGE) - 1, 0),
            'offset' => $offset,
            'codeJoin' => $codeJoin->createView(),
        ]);
    }
    
    #[Route('/clubs/{id<\d+>}', name: 'club_show')]
    public function show(Club $club, Request $request, UserGoalManager $userGoalManager): Response
    {
        $userClub = $this->userClubRepository->findOneByUserAndClub($this->getUser(), $club);

        if ($userClub === null) {
            $this->addFlash('note', "Nie nale??ysz do klubu {$club->getName()}");

            return $this->redirectToRoute('clubs');
        }

        $owner = $this->getClubOwner($club);

        $userGoalManager->fillGoals($this->getUser(), ...$club->getGoals());

        return $this->render('club/show.html.twig', [
            'club' => $club,
            'is_owner' => $userClub->getIsOwner(),
            'owner' => $owner,
            'goals' => $club->getGoals(),
        ]);
    }

    #[Route('/clubs/{id<\d+>}/milestones', name: 'club_show_milestones')]
    public function showMilestones(Club $club, Request $request, UserGoalManager $userGoalManager, UserMilestoneManager $userMilestoneManager): Response
    {
        $userClub = $this->userClubRepository->findOneByUserAndClub($this->getUser(), $club);

        if ($userClub === null) {
            $this->addFlash('note', "Nie nale??ysz do klubu {$club->getName()}");

            return $this->redirectToRoute('clubs');
        }

        $owner = $this->getClubOwner($club);

        $milestones = $club->getMilestones();
        $userGoalManager->fillGoals($this->getUser(), ...$club->getGoals());
        $userMilestoneManager->fillMilestones($this->getUser(), ...$milestones);

        return $this->render('club/milestones.html.twig', [
            'club' => $club,
            'is_owner' => $userClub->getIsOwner(),
            'owner' => $owner,
            'milestones' => $milestones,
        ]);
    }

    #[Route('/clubs/join/{code}', name: 'club_join_code')]
    public function join(Request $request, EntityManagerInterface $entityManager, ClubRepository $clubRepository, UserClubRepository $userClubRepository): Response
    {
        $code = $request->get('code');
        $club = $clubRepository->findOneByCode($code);
        $user = $this->getUser();

        if ($club instanceof Club) {
            if ($user->isMember($club)) {
                $this->addFlash('note', 'Ju?? nale??ysz do tego klubu.');
                return $this->redirectToRoute('club_show', ['id' => $club->getId()]);
            }
            $form = $this->createForm(SingleSubmitFormType::class, null, [ 'label' => 'Join' ]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $userClub = new UserClub($user, $club);
                    $entityManager->persist($userClub);
                    $entityManager->flush();
        
                    $this->addFlash('success', 'Do????czono do klubu.');
                    return $this->redirectToRoute('club_show', ['id' => $club->getId()]);
                } catch (Exception $e) {
                    $this->addFlash('note', "Wyst??pi?? b????d przy do????czaniu do klubu.");
                    return $this->redirectToRoute('clubs');
                }
            } else {
                $owner = $this->getClubOwner($club);

                return $this->render('club/join.html.twig', [
                    'club' => $club,
                    'owner' => $owner,
                    'form' => $form->createView(),
                ]);
            }
        }

        $this->addFlash('note', "Wyst??pi?? b????d przy do????czaniu do klubu.");
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

        $this->addFlash('note', "Wyst??pi?? b????d przy do????czaniu do klubu.");
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

    private function getClubOwner(Club $club, bool $object = false) {
        if (empty($this->clubOwner)) {
            $this->clubOwner = $this->userClubRepository->findOwner($club);
        }
        
        if ($object) {
            return $this->clubOwner;
        }

        if ($this->clubOwner === null) {
            return '';
        } else {
            return (string) $this->clubOwner->getMember();
        }
    }
    
    #[Route('/clubs/regenerate_code/{id<\d+>}', name: 'club_regenerate_code')]
    public function regenerateCode(Club $club, EntityManagerInterface $entityManager): Response
    {
        $userClub = $this->userClubRepository->findOneByUserAndClub($this->getUser(), $club);

        if ($userClub === null) {
            $this->addFlash('note', "Nie nale??ysz do tego klubu.");
            return $this->redirectToRoute('clubs');
        }
        if ($userClub->getIsOwner()) {
            $club->regenerateJoinCode();
            $entityManager->persist($club);
            $entityManager->flush();
            $this->addFlash('success', "Pomy??lnie wygenerowano nowy kod dost??pu.");
        } else {
            $this->addFlash('note', "Nie jeste?? w??a??cicielem tego klubu.");
        }
        
        return $this->redirectToRoute('club_show', ['id' => $club->getId()]);
    }
    
    #[Route('/clubs/leave/{id<\d+>}', name: 'club_leave')]
    public function leave(Club $club, Request $request, EntityManagerInterface $entityManager): Response
    {
        $userClub = $this->userClubRepository->findOneByUserAndClub($this->getUser(), $club);

        if ($userClub === null) {
            $this->addFlash('note', 'Nie nale??ysz do tego klubu.');
        } else if ($userClub->getIsOwner()) {
            $this->addFlash('note', 'Nie mo??esz opu??ci?? klubu, kt??rego jeste?? w??a??cicielem.');
            return $this->redirectToRoute('club_show', ['id' => $club->getId()]);
        } else {
            $form = $this->createForm(SingleSubmitFormType::class, null, [ 'label' => 'Leave' ]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->remove($userClub);
                $entityManager->flush();

                $this->addFlash('success', 'Pomy??lnie opu??ci??e?? klub.');
            } else {
                return $this->render('club/leave.html.twig', [
                    'club' => $club,
                    'form' => $form->createView(),
                    'is_owner' => $userClub->getIsOwner(),
                    'owner' => $this->getClubOwner($club),
                ]);
            }
        }
        
        return $this->redirectToRoute('clubs');
    }

    #[Route('/clubs/manage/{id<\d+>}', name: 'club_manage')]
    public function manage(Club $club, Request $request, EntityManagerInterface $entityManager): Response
    {
        $userClub = $this->userClubRepository->findOneByUserAndClub($this->getUser(), $club);
        if ($userClub === null) {
            $this->addFlash('note', 'Nie nale??ysz do tego klubu');
            return $this->redirectToRoute('clubs');
        }
        if (!$userClub->getIsOwner()) {
            $this->addFlash('note', 'Nie jeste?? w??a??cicielem tego klubu');
            return $this->redirectToRoute('club_show', ['id' => $club->getId()]);
        }

        $form = $this->createForm(ClubType::class, $club);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($club);
            $entityManager->flush();
            
            $this->addFlash('success', 'Pomy??lnie zapisano zmiany.');

            return $this->redirectToRoute('club_show', ['id' => $club->getId()]);
        }

        return $this->render('club/manage.html.twig', [
            'club' => $club,
            'is_owner' => true,
            'form' => $form->createView(),
        ]);
    }
}
