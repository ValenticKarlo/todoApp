<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodoListType;
use App\Repository\TodoListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends TodoAppController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(TodoListRepository $listRepository, Request $request): Response
    {
        $user = $this->getUser();
        $orderBy = $request->get('orderBy') ? $request->get('orderBy') : 'name';
        $orderDirection = $request->get('orderDirection') ? $request->get('orderDirection') : 'ASC';
        $searchTerm = $request->get('searchTerm');
        if ($user === null)
        {
            throw $this->createNotFoundException('No user logged in.');
        }
        $lists = $listRepository->orderAndSearchByParameters($user->getId(), $orderBy,  $orderDirection,  $searchTerm);

        return $this->render('todoApp/dashboard.html.twig',[
            'lists'=>$lists,
            'orderBy'=>$orderBy,
            'orderDirection'=>$orderDirection,
            'searchTerm'=>$searchTerm
        ]);
    }

    #[Route('/dashboard/delete-list/{listId}', name: 'app_delete_list')]
    public function deleteList($listId, TodoListRepository $listRepository): Response
    {
        $list = $listRepository->findOneBy(['id'=>$listId]);
        if(is_null($listId))
        {
            throw $this->createNotFoundException('No list with Id: '.$listId);
        }
        $listRepository->remove($list, true);
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/dashboard/create-todolist', name: 'app_create_todoList')]
    public function createTodoList(Request $request, TodoListRepository $listRepository): Response
    {
        $todoList = new TodoList();
        $form = $this->createForm(TodoListType::class, $todoList);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $todoList = $form->getData();
            $todoList->setUser($this->getUser());
            $listRepository->save($todoList, true);
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('forms/createTodoList.html.twig', [
            'form' => $form,
        ]);

    }
}