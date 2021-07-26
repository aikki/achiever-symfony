<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\ClubRepository;
use App\Service\ClubDataProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $clubRepository;
    private $security;
    private $clubDataProvider;

    public function __construct(Environment $twig, ClubDataProvider $clubDataProvider)
    {
        $this->twig = $twig;
        // $this->clubRepository = $clubRepository;
        // $this->security = $security;
        $this->clubDataProvider = $clubDataProvider;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $clubs = $this->clubDataProvider->getMyClubs();
        $this->twig->addGlobal('myClubs', $clubs);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }
}
