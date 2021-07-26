<?php

namespace App\Controller;

use App\Repository\ClubRepository;
use App\Service\ClubDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route("/", name: "index")]
    public function index(Request $request, ClubRepository $clubRepository, ClubDataProvider $clubDataProvider): Response
    {
        $myClubsData = $clubDataProvider->getMyClubsData();
        return $this->render('index/index.html.twig', [
            'clubs' => $myClubsData,
        ]);
    }
}
