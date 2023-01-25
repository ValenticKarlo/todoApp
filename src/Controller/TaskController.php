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
    public function show( $listId, TaskRepository $taskRepository, TodoListRepository $listRepository, Request $request):Response
    {
        $user = $this->getUser();
        $orderBy = $request->get('orderBy') ? $request->get('orderBy') : 'task' ;
        $orderDirection = $request->get('orderDirection') ? $request->get('orderDirection') : 'ASC' ;
        $searchTerm = $request->get('searchTerm');
        $list = $listRepository->findOneBy(['id'=>$listId]);
        if( $listId === null && !($list->getUser() === $user) )
        {
            throw $this->createNotFoundException('No list of tasks wit id: '. $listId);
        }
        $tasks = $taskRepository->orderAndSearchByParameters($orderBy, $listId, $orderDirection, $searchTerm);
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
        if (is_null($user))
        {
            throw $this->createNotFoundException('No user logged in.');
        }
        $lists = $listRepository->findBy(['user'=>$user]);
        $task = $taskRepository->findOneBy(['id'=>$taskId]);
        $form = $this->createForm(TaskType::class, $task, ['listRepo'=>$lists]);
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

    #[Route('/dashboard/create_task', name: 'app_task_create')]
    public function createTask(Request $request, TaskRepository $taskRepository, TodoListRepository $listRepository): Response
    {
        $user = $this->getUser();
        if (is_null($user))
        {
            throw $this->createNotFoundException('No user logged in.');
        }
        $list = $listRepository->findBy(['user'=>$user]);
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task, ['listRepo'=>$list]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $task = $form->getData();
            $taskRepository->save($task, true);
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('forms/createTask.html.twig', [
            'form' => $form
        ]);

    }

    #[Route('/dashboard/delete-task/{taskId<\d+>}/{listId<\d+>}', name: 'app_task_delete')]
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

    #[Route('/dashboard/complete-task/{taskId<\d+>}/{listId<\d+>}', name: 'app_complete_task')]
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