<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodoListType;
use App\Repository\TodoListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(TodoListRepository $listRepository): Response
    {
        $orderBy = !empty($_POST['orderBy']) ? $_POST['orderBy'] : 'name';
        $lists = $listRepository->orderBySelectedValue($orderBy);

        return $this->render('todoApp/dashboard.html.twig',[
            'lists'=>$lists,
            'orderBy'=>$orderBy
        ]);
    }

    #[Route('/delete-list/{listId}', name: 'app_delete_list')]
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

    #[Route('/create-todolist', name: 'app_create_todoList')]
    public function createTodoList(Request $request, TodoListRepository $listRepository): Response
    {
        $todoList = new TodoList();
        $form = $this->createForm(TodoListType::class, $todoList);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $todoList = $form->getData();
            $listRepository->save($todoList, true);
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('forms/createTodoList.html.twig', [
            'form' => $form,
        ]);

    }
}