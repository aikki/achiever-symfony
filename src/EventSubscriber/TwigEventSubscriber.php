<?php

namespace App\EventSubscriber;

use App\Repository\ClubRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $clubRepository;

    public function __construct(Environment $twig, ClubRepository $clubRepository)
    {
        $this->twig = $twig;
        $this->clubRepository = $clubRepository;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $this->twig->addGlobal('myClubs', $this->clubRepository->findAll());
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }
}
