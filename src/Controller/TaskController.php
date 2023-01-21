<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/', name: 'app_show_tasks')]
    public function show(TaskRepository $taskRepository):Response
    {
        $orderBy = !empty($_POST['orderBy']) ? $_POST['orderBy'] : 'task';

        $tasks = $taskRepository->orderBySelectedValue($orderBy);


        return $this->render('todoApp/showTasks.html.twig',[
            'tasks'=>$tasks,
            'orderBy'=>$orderBy,
        ]);
    }

    #[Route('/delete-task/{taskId<\d+>}', name: 'app_task_delete')]
    public function deleteTask($taskId, TaskRepository $taskRepository ):Response
    {
        $task = $taskRepository->findOneBy(['id'=>$taskId]);
        if(is_null($taskId))
        {
            throw $this->createNotFoundException('No task with id: '. $taskId);
        }

        $taskRepository->remove($task, true);

        return $this->redirectToRoute('app_show_tasks');
    }

    #[Route('/complete-task/{taskId<\d+>}', name: 'app_complete_task')]
    public function completeTask($taskId, TaskRepository $taskRepository): Response
    {
        $task = $taskRepository->findOneBy(['id'=>$taskId]);
        if(is_null($taskId))
        {
            throw $this->createNotFoundException('No task with id: '. $taskId);
        }
        $taskRepository->completeTask($task, true);

        return $this->redirectToRoute('app_show_tasks');

    }
}