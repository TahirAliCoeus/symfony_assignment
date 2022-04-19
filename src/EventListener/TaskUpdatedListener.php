<?php

namespace App\EventListener;


use App\Entity\Task;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class TaskUpdatedListener
{
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if(!$entity instanceof Task)
        {
            return;
        }
        $entity->setUpdatedAt(new \DateTime("now"));
    }
}