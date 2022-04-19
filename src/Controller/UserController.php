<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\Type\UserCollectionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(ManagerRegistry $registry,Request $request ,ManagerRegistry $managerRegistry): Response
    {
        $users = $registry->getRepository(User::class)->findAll();

        $userForm = $this->createForm( UserCollectionType::class, ['users' =>$users]);

        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid())
        {
            $this->update($userForm->getData(),$managerRegistry);
            return $this->redirectToRoute("app_user");
        }

        return  $this->renderForm("user/index.html.twig",[
            "listing_form" =>$userForm
        ]);
    }


    private function update($usersCollection,ManagerRegistry $managerRegistry)
    {
        $entityManager = $managerRegistry->getManager();
        $existingUsers = $managerRegistry->getRepository(User::class)->findAll();

        foreach ($existingUsers as $existingUser)
        {
            if(!in_array($existingUser,$usersCollection['users']))
            {
                $entityManager->remove($existingUser);
            }
        }

        $entityManager->flush();

    }
}
