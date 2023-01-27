<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodoListType;
use App\Repository\TodoListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TodoListController extends TodoAppController
{
    #[Route('/dashboard/delete-list/{listId}', name: 'app_delete_list')]
    public function deleteList($listId, TodoListRepository $listRepository): Response
    {
        $user = $this->getUser();
        $list = $listRepository->findOneBy(['id'=>$listId, 'user'=>$user]);
        $this->securityCheck($list);
        $listRepository->remove($list, true);
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/dashboard/create-todolist', name: 'app_create_todoList')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function createTodoList(Request $request, TodoListRepository $listRepository): Response
    {
        $todoList = new TodoList();
        $form = $this->createForm(TodoListType::class, $todoList);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $todoList->setUser($this->getUser());
            $listRepository->save($todoList, true);
            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('forms/createTodoList.html.twig', [
            'form' => $form,
        ]);

    }
}