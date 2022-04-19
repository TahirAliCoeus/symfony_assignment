<?php

namespace App\Form\Type;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskType extends AbstractType
{
    private  $managerRegistry;
    public function __construct(ManagerRegistry $registry)
    {
        $this->managerRegistry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $users = $this->managerRegistry->getRepository(User::class)->findAll();
        $builder
            ->add('Title', TextType::class,['attr' => ["class" => "form-group"]])
            ->add('Description', TextareaType::class,['attr' => ["class" => "mt-2"]])
            ->add('user',ChoiceType::class,["choices" => $users,"choice_value" => "id",'choice_label' => 'name','label' => "Select assignee", 'attr' => ['class' => "mt-2"]])
            ->add('Attachment', FileType::class,["mapped" => false,"attr" => ["class" => "mt-2"], 'required' => false])
            ->add("Save",SubmitType::class,['attr' => ['class' => "btn btn-success mt-5"]]);
    }
}