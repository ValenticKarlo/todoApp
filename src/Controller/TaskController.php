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
    #[Route('/dashboard/{listId<\d+>}', name: 'app_show_tasks')]
    public function showTasks( $listId, TaskRepository $taskRepository, TodoListRepository $listRepository, Request $request):Response
    {
        $user = $this->getUser();
        $orderBy = $request->get('orderBy') ? $request->get('orderBy') : 'task' ;
        $orderDirection = $request->get('orderDirection') ? $request->get('orderDirection') : 'ASC' ;
        $searchTerm = $request->get('searchTerm');
        $list = $listRepository->findOneBy(['id'=>$listId]);
        if( $listId === null || !($list->getUser() === $user) )
        {
            throw $this->createNotFoundException('List with Id: '. $listId.' not found or is not owned by User');
        }
        $tasks = $taskRepository->orderAndSearchByParameters( $listId, $orderBy, $orderDirection, $searchTerm);
        return $this->render('todoApp/showTasks.html.twig',[
            'tasks'=>$tasks,
            'list'=>$list,
            'orderBy'=>$orderBy,
            'orderDirection'=>$orderDirection,
            'searchTerm'=>$searchTerm
        ]);
    }

    #[Route('/dashboard/edit_task/{listId}/{taskId}', name:'app_edit_task')]
    public function editTask($listId, $taskId, TaskRepository $taskRepository, Request $request, TodoListRepository $listRepository ): Response
    {
        $user = $this->getUser();
        $list = $listRepository->findBy(['id'=>$listId]);
        if ( !$list || $user === null)
        {
            throw $this->createNotFoundException('No user logged in, or no lists connected to the user.');
        }
        $task = $taskRepository->findOneBy(['id'=>$taskId]);
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

    #[Route('/dashboard/create_task/{listId<\d+>}', name: 'app_task_create')]
    public function createTask($listId, Request $request, TaskRepository $taskRepository, TodoListRepository $listRepository): Response
    {
        $user = $this->getUser();
        $list = $listRepository->findOneBy(['id'=>$listId]);
        if (!$user || !$list)
        {
            throw $this->createNotFoundException('No user logged in, or no lists connected to the user.');
        }
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

    #[Route('/dashboard/delete-task/{taskId<\d+>}/{listId<\d+>}', name: 'app_task_delete')]
    public function deleteTask($taskId, $listId, TaskRepository $taskRepository, TodoListRepository $listRepository ):Response
    {
        $user = $this->getUser();
        $list = $listRepository->findOneBy(['id'=>$listId]);
        $task = $taskRepository->findOneBy(['id'=>$taskId]);
        if($list->getUser() !== $user || !$task)
        {
            throw $this->createNotFoundException('No task with id: '. $taskId . 'or task not owned by user.');
        }
        $taskRepository->remove($task, true);
        return $this->redirectToRoute('app_show_tasks',['listId'=>$listId]);
    }

    #[Route('/dashboard/complete-task/{taskId<\d+>}/{listId<\d+>}', name: 'app_complete_task')]
    public function completeTask($taskId, $listId, TaskRepository $taskRepository, TodoListRepository $listRepository): Response
    {
        $user = $this->getUser();
        $list = $listRepository->findOneBy(['id'=>$listId]);
        $task = $taskRepository->findOneBy(['id'=>$taskId]);
        if($list->getUser() !== $user || !$task)
        {
            throw $this->createNotFoundException('No task with id: '. $taskId . 'or task not owned by user.');
        }
        $taskRepository->updateStatus($task->getId());
        $listRepository->updateCount($listId);
        return $this->redirectToRoute('app_show_tasks',['listId'=>$listId]);
    }
}