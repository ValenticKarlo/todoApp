<?php

namespace App\Controller;


use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskController extends AbstractController
{
    #[Route('/dashboard/{listId<\d+>}', name: 'app_show_tasks')]
    public function show($listId, TaskRepository $taskRepository, TodoListRepository $listRepository):Response
    {
        $searchTerm = !empty($_POST['searchTerm']) ? $_POST['searchTerm'] : null;
        $orderBy = !empty($_POST['orderBy']) ? $_POST['orderBy'] : 'task';
        $list = $listRepository->findOneBy(['id'=>$listId]);
        if(is_null($listId)){ throw $this->createNotFoundException('No list of tasks wit id: '. $listId);}
        $tasks = $taskRepository->orderBySelectedValue($orderBy, $listId, $searchTerm);

        //dd($tasks);
        return $this->render('todoApp/showTasks.html.twig',[
            'tasks'=>$tasks,
            'orderBy'=>$orderBy,
            'list'=>$list,
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
        $list = $listRepository->findBy(['user'=>$user]);
        $task = $taskRepository->findOneBy(['id'=>$taskId]);
        $form = $this->createForm(TaskType::class, $task, ['listRepo'=>$list]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $task = $form->getData();
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