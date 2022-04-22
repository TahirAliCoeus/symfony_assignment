<?php

namespace App\Controller;

use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/",name= "task_list")
     */
    public function index(Request $request, TaskService $taskService): Response
    {
        $form = $taskService->getAddTaskButtonForm();
        $taskForm = $taskService->getTaskListingForm();
        $taskForm->handleRequest($request);

        if ($taskForm->isSubmitted() && $taskForm->isValid()) {
            $taskService->updateTask($taskForm->getData(), $request->files->get("task_collection")['tasks']);
            return $this->redirectToRoute("task_list");
        }
        return $this->renderForm("task/index.html.twig", [
            "form" => $form,
            "listing_form" => $taskForm
        ]);
    }

    /**
     * @Route ("task/add",name= "task_add")
     */
    public function add(Request $request, TaskService $taskService)
    {
        $form = $taskService->getAddTaskForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskService->addTask($form);
            return $this->redirectToRoute("task_list");
        }
        return $this->renderForm("task/create.html.twig", [
            "form" => $form
        ]);
    }
}
