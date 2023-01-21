<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/dashboard/{listId<\d+>}', name: 'app_show_tasks')]
    public function show($listId, TaskRepository $taskRepository, TodoListRepository $listRepository):Response
    {
        $orderBy = !empty($_POST['orderBy']) ? $_POST['orderBy'] : 'task';
        $list = $listRepository->findOneBy(['id'=>$listId]);
        if(is_null($listId)){ throw $this->createNotFoundException('No list of tasks wit id: '. $listId);}
        $tasks = $taskRepository->orderBySelectedValue($orderBy, $listId);

        return $this->render('todoApp/showTasks.html.twig',[
            'tasks'=>$tasks,
            'orderBy'=>$orderBy,
            'list'=>$list,
        ]);
    }

    #[Route('/delete-task/{taskId<\d+>}/{listId<\d+>}', name: 'app_task_delete')]
    public function deleteTask($taskId, $listId, TaskRepository $taskRepository ):Response
    {
        $task = $taskRepository->findOneBy(['id'=>$taskId]);
        if(is_null($taskId) || is_null($listId) )
        {
            throw $this->createNotFoundException('No task with id: '. $taskId);
        }

        $taskRepository->remove($task, true);

        return $this->redirectToRoute('app_show_tasks',['listId'=>$listId]);
    }

    #[Route('/complete-task/{taskId<\d+>}/{listId<\d+>}', name: 'app_complete_task')]
    public function completeTask($taskId, $listId, TaskRepository $taskRepository): Response
    {
        $task = $taskRepository->findOneBy(['id'=>$taskId]);
        if(is_null($taskId) || is_null($listId))
        {
            throw $this->createNotFoundException('No task with id: '. $taskId);
        }
        $taskRepository->completeTask($task, true);

        return $this->redirectToRoute('app_show_tasks',['listId'=>$listId]);

    }
}