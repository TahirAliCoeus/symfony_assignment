<?php

namespace App\Service;

use App\Entity\User;
use App\Form\Type\UserCollectionType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    private ValidatorInterface $validator;
    private UserRepository $userRepository;
    private FormFactoryInterface $formFactory;
    public function __construct(ValidatorInterface $validator,UserRepository $repository,FormFactoryInterface $formFactory)
    {
        $this->validator = $validator;
        $this->userRepository = $repository;
        $this->formFactory = $formFactory;
    }

    public function addUser($name,$email,$updatedAt): ?string
    {
        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setUpdatedAt($updatedAt);

        $errors =  $this->validator->validate($user);

        if(count($errors))
        {
            return (string) $errors;
        }
        $this->userRepository->add($user);
        return null;
    }
    public function deleteUser($usersCollection)
    {
        $existingUsers = $this->userRepository->findAll();

        foreach ($existingUsers as $existingUser)
        {
            if(!in_array($existingUser,$usersCollection['users']))
            {
               $this->userRepository->remove($existingUser);
            }
        }
    }
    public function getUserListingForm(): FormInterface
    {
        $users = $this->userRepository->findAll();
        return $this->formFactory->create( UserCollectionType::class, ['users' =>$users]);
    }

}