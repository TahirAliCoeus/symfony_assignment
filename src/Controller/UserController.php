<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user",name= "app_user")
     */
    public function index(Request $request, UserService $userService): Response
    {
        $userForm = $userService->getUserListingForm();
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid())
        {
            $userService->deleteUser($userForm->getData());
            return $this->redirectToRoute("app_user");
        }

        return  $this->renderForm("user/index.html.twig",[
            "listing_form" =>$userForm
        ]);
    }
}
