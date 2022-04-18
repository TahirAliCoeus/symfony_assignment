<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(ManagerRegistry $registry): Response
    {

        $task = new Task();
        $task->setTitle("Task 1");
        $task->setDescription("lorem ipsum");
        $task->setAssignee(1);
        $task->setFilePath("");
        $task->setUpdatedAt(new \DateTime("now"));

        $em = $registry->getManager();
        $em->persist($task);
        $em->flush();
        dd("DONE");
       /* return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);*/
    }
}
