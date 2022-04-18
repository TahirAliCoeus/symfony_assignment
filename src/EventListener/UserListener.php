<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if(!$entity instanceof User)
        {
            return;
        }
        $entity->setIsIdle(true);
    }
}