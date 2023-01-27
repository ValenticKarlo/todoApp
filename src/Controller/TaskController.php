<?php

namespace App\Controller;


use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends TodoAppController
{
    public function checkIfTaskExist( ?Task $task): void
    {
        if( !$task )
        {
            throw $this->createNotFoundException('No task found');
        }
    }
    #[Route('/dashboard/{listId<\d+>}', name: 'app_show_tasks')]
    public function showTasks( $listId, TaskRepository $taskRepository, TodoListRepository $listRepository, Request $request):Response
    {
        $user = $this->getUser();
        $list = $listRepository->findOneBy(['id'=>$listId]);
        $this->securityCheck($list,$user);
        $orderBy = $request->get('orderBy') ? $request->get('orderBy') : 'task' ;
        $orderDirection = $request->get('orderDirection') ? $request->get('orderDirection') : 'ASC' ;
        $searchTerm = $request->get('searchTerm');
        $tasks = $taskRepository->orderAndSearchByParameters( $listId, $orderBy, $orderDirection, $searchTerm);
        return $this->render('todoApp/showTasks.html.twig',[
            'tasks'=>$tasks,
            'list'=>$list,
            'orderBy'=>$orderBy,
            'orderDirection'=>$orderDirection,
            'searchTerm'=>$searchTerm
        ]);
    }

    #[Route('/dashboard/create_task/{listId<\d+>}', name: 'app_task_create')]
    public function createTask($listId, Request $request, TaskRepository $taskRepository, TodoListRepository $listRepository): Response
    {
        $user = $this->getUser();
        $list = $listRepository->findOneBy(['id'=>$listId]);
        $this->securityCheck($list,$user);
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $task->setTodoList($list);
            $taskRepository->save($task, true);
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('forms/createTask.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/dashboard/edit_task/{listId}/{taskId}', name:'app_edit_task')]
    public function editTask($listId, $taskId, TaskRepository $taskRepository, Request $request, TodoListRepository $listRepository ): Response
    {
        $user = $this->getUser();
        $list = $listRepository->findOneBy(['id'=>$listId]);
        $this->securityCheck($list, $user);
        $task = $taskRepository->findOneBy(['id'=>$taskId]);
        $this->checkIfTaskExist($task);
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $taskRepository->save($task, true);
            return $this->redirectToRoute('app_show_tasks', ['listId'=>$listId]);
        }
        return $this->render('forms/createTask.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/dashboard/delete-task/{taskId<\d+>}/{listId<\d+>}', name: 'app_task_delete')]
    public function deleteTask($taskId, $listId, TaskRepository $taskRepository, TodoListRepository $listRepository ):Response
    {
        $user = $this->getUser();
        $list = $listRepository->findOneBy(['id'=>$listId]);
        $this->securityCheck($list,$user);
        $task = $taskRepository->findOneBy(['id'=>$taskId]);
        $this->checkIfTaskExist($task);
        $taskRepository->remove($task, true);
        return $this->redirectToRoute('app_show_tasks',['listId'=>$listId]);
    }

    #[Route('/dashboard/complete-task/{taskId<\d+>}/{listId<\d+>}', name: 'app_complete_task')]
    public function completeTask($taskId, $listId, TaskRepository $taskRepository, TodoListRepository $listRepository): Response
    {
        $user = $this->getUser();
        $list = $listRepository->findOneBy(['id'=>$listId]);
        $this->securityCheck($list,$user);
        $task = $taskRepository->findOneBy(['id'=>$taskId]);
        $this->checkIfTaskExist($task);
        $taskRepository->updateStatus($task->getId());
        $listRepository->updateCount($listId);
        return $this->redirectToRoute('app_show_tasks',['listId'=>$listId]);
    }
}