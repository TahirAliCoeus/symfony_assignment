<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\Type\TaskCollectionType;
use App\Form\Type\TaskType;
use App\Service\FileUpload;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/', name: 'task_list')]
    public function index(ManagerRegistry $registry,Request $request,FileUpload $fileUpload,ManagerRegistry $managerRegistry): Response
    {

        $task = new Task();
        $form = $this->createFormBuilder($task)
            ->add('Add_Task', SubmitType::class,[
                'attr' => [
                    'class' => 'btn btn-success',
                    'style' => "width : 100px"
                ]
            ])
            ->setAction($this->generateUrl("task_add"))
            ->setMethod("GET")
            ->getForm();

            $tasks = $registry->getRepository(Task::class)->findAll();
            $taskForm = $this->createForm( TaskCollectionType::class, ['tasks' =>$tasks]);

            $taskForm->handleRequest($request);

            if($taskForm->isSubmitted() && $taskForm->isValid())
            {
                $this->update($taskForm->getData(),$fileUpload,$managerRegistry,$request);
                return $this->redirectToRoute("task_list");
            }

            return  $this->renderForm("task/index.html.twig",[
                "form" => $form,
                "listing_form" =>$taskForm
            ]);
    }

    /**
     * @Route ("task/add",name="task_add")
     */
    public function add(Request $request,ManagerRegistry $managerRegistry,FileUpload $fileUpload)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class,$task);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($task);

            $task = $form->getData();


            $taskAttachment = $form->get("Attachment")->getData();
            $allowedExtensions = ["png","jpg",'jpeg',"pdf","mp4"];
            $allowedUploadSizeInMB = 50;

            if($taskAttachment && $fileUpload->isFileExtensionAllowed($taskAttachment->guessExtension(),$allowedExtensions) && $fileUpload->isValidFileSize($taskAttachment->getSize(),$allowedUploadSizeInMB))
            {
                $fileUpload->setDestinationPath("uploads/task_attachments/");
                $fileName = $fileUpload->upload($taskAttachment);
                if(!$fileName)
                {
                    //TODO :Handle error response
                }
                $task->setFilePath($fileName);
            }
            $sanitizedTaskTitle = filter_var($task->getTitle(),FILTER_SANITIZE_STRING);
            $sanitizedTaskDescription = filter_var($task->getDescription(),FILTER_SANITIZE_STRING);

            $task->setTitle($sanitizedTaskTitle);
            $task->setDescription($sanitizedTaskDescription);

            $entityManager->flush();
            return  $this->redirectToRoute("task_list");
        }

        return  $this->renderForm("task/create.html.twig",[
            "form" => $form
        ]);
    }


    private function update($tasksCollection,FileUpload $fileUpload,$managerRegistry, Request $request)
    {
        $entityManager = $managerRegistry->getManager();
        $allowedExtensions = ["png","jpg",'jpeg',"pdf","mp4"];
        $allowedUploadSizeInMB = 50;
        foreach ($tasksCollection['tasks'] as $key => $task)
        {

            $files = $request->files->get("task_collection")['tasks'];


            $taskAttachment = $files[$key]['Attachment'];

            if($taskAttachment && $fileUpload->isFileExtensionAllowed($taskAttachment->guessExtension(),$allowedExtensions) && $fileUpload->isValidFileSize($taskAttachment->getSize(),$allowedUploadSizeInMB))
            {
                $fileUpload->setDestinationPath("uploads/task_attachments/");
                $fileName = $fileUpload->upload($taskAttachment);
                if(!$fileName)
                {
                    //TODO :Handle error response
                }
                $task->setFilePath($fileName);
            }
            $entityManager->persist($task);
            $sanitizedTaskTitle = filter_var($task->getTitle(),FILTER_SANITIZE_STRING);
            $sanitizedTaskDescription = filter_var($task->getDescription(),FILTER_SANITIZE_STRING);

            $task->setTitle($sanitizedTaskTitle);
            $task->setDescription($sanitizedTaskDescription);
        }

        $entityManager->flush();
    }
}
