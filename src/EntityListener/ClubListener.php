<?php
namespace App\EntityListener;

use App\Entity\Club;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ClubListener
{
    public function prePersist(Club $club, LifecycleEventArgs $event): void
    {
        if (empty($club->getJoinCode())) {
            $club->setJoinCode(substr(bin2hex(random_bytes(7)), 0, 7));
        }
    }
}