<?php

namespace App\Service;

use App\Entity\Task;
use App\Form\Type\TaskCollectionType;
use App\Form\Type\TaskType;
use App\Repository\TaskRepository;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class TaskService
{
    private TaskRepository $taskRepository;
    private FileUpload $fileUpload;
    private FormFactory $formFactory;
    private UrlGeneratorInterface $router;

    public function __construct(TaskRepository $taskRepository, FileUpload $fileUpload, FormFactoryInterface $formFactory, UrlGeneratorInterface $urlGenerator)
    {
        $this->taskRepository = $taskRepository;
        $this->fileUpload = $fileUpload;
        $this->formFactory = $formFactory;
        $this->router = $urlGenerator;
    }

    public function addTask($form): void
    {
        $task = $form->getData();

        $taskAttachment = $form->get("Attachment")->getData();
        $allowedExtensions = ["png", "jpg", "jpeg", "pdf", "mp4"];

        $this->uploadAttachment($taskAttachment, $allowedExtensions);
        $task->setFilePath($this->fileUpload->getFileName());
        $sanitizedTaskTitle = filter_var($task->getTitle(), FILTER_SANITIZE_STRING);
        $sanitizedTaskDescription = filter_var($task->getDescription(), FILTER_SANITIZE_STRING);

        $task->setTitle($sanitizedTaskTitle);
        $task->setDescription($sanitizedTaskDescription);

        $this->taskRepository->add($task);
    }

    public function getAddTaskForm(): FormInterface
    {
        $task = new Task();
        return $this->formFactory->create(TaskType::class, $task);
    }

    public function getTaskListingForm(): FormInterface
    {
        $tasks = $this->taskRepository->findAll();
        return $this->formFactory->create(TaskCollectionType::class, ["tasks" => $tasks]);
    }

    public function getAddTaskButtonForm(): FormInterface
    {
        $task = new Task();
        return $this->formFactory->createBuilder(FormType::class, $task)
            ->add("Add_Task", SubmitType::class, [
                "attr" => [
                    "class" => "btn btn-success",
                    "style" => "width : 100px"
                ]
            ])
            ->setAction($this->router->generate("task_add"))
            ->setMethod("GET")
            ->getForm();
    }

    public function updateTask($tasksCollection, $files): void
    {
        $allowedExtensions = ["png", "jpg", "jpeg", "pdf", "mp4"];
        foreach ($tasksCollection["tasks"] as $key => $task) {

            $existingTasks = $this->taskRepository->findAll();
            foreach ($existingTasks as $existingTask) {
                if (!in_array($existingTask, $tasksCollection["tasks"])) {
                    $this->taskRepository->remove($existingTask);
                } else {
                    $taskAttachment = $files[$key]["Attachment"];

                    $this->uploadAttachment($taskAttachment, $allowedExtensions);
                    $task->setFilePath($this->fileUpload->getFileName());
                    $sanitizedTaskTitle = filter_var($task->getTitle(), FILTER_SANITIZE_STRING);
                    $sanitizedTaskDescription = filter_var($task->getDescription(), FILTER_SANITIZE_STRING);

                    $task->setTitle($sanitizedTaskTitle);
                    $task->setDescription($sanitizedTaskDescription);
                }
            }
            $this->taskRepository->add($task);
        }
    }

    private function uploadAttachment($taskAttachment, $allowedExtensions): void
    {
        if ($taskAttachment && $this->fileUpload->validate($taskAttachment, $allowedExtensions)) {
            $this->fileUpload->setDestinationPath("uploads/task_attachments/");
            $this->fileUpload->upload($taskAttachment);
        }
    }
}