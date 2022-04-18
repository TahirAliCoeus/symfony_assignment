<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(ManagerRegistry $managerRegistry): Response
    {
        $user = $managerRegistry->getRepository(User::class)->find(1);

/*        $user = new User();
        $user->setName("Tahir");
        $user->setEmail("tahir@test.com");
        $user->setIsIdle(false);
        $user->setUpdatedAt(new \DateTime("now"));
        $entityManager->persist($user);
        $entityManager->flush();*/
        foreach ( $user->getTasks() as $task)
        {
            dump($task);
        }
        dd("end");
       /* return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);*/
    }
}
