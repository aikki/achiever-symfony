<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\ClubRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $clubRepository;
    private $security;

    public function __construct(Environment $twig, ClubRepository $clubRepository, Security $security)
    {
        $this->twig = $twig;
        $this->clubRepository = $clubRepository;
        $this->security = $security;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $clubs = [];
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $userClubs = $user->getUserClubs()->toArray();
            if (!empty($userClubs)) {
                $clubs = array_map(function($c) { return $c->getClub(); }, $userClubs);
            }
        }
        $this->twig->addGlobal('myClubs', $clubs);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }
}
