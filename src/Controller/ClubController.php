<?php

namespace App\Controller;

use App\Entity\Club;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClubController extends AbstractController
{
    #[Route('/club', name: 'club')]
    public function index(): Response
    {
        return $this->render('club/index.html.twig', [
            'controller_name' => 'ClubController',
        ]);
    }

    #[Route('/club/join/{id}', name: 'club_join')]
    public function join(Club $club): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->urlGenerator->generate('security_login'));
        }

        dump($this->getUser()->isMember($club));
        
    
    }
}
