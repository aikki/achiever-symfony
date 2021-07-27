<?php
namespace App\EntityListener;

use App\Entity\Club;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ClubListener
{
    private $flashBag;
    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function prePersist(Club $club, LifecycleEventArgs $event): void
    {
        if (empty($club->getJoinCode())) {
            $club->regenerateJoinCode();
        }
    }

    public function preUpdate(Club $club, PreUpdateEventArgs $event): void
    {
        if (array_key_exists('isPublic', $event->getEntityChangeSet())) {
            $club->regenerateJoinCode();
            $this->flashBag->add('note', "Wygenerowano nowy kod dostępu.");
        }
    }
}