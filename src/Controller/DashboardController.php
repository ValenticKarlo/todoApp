<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodoListType;
use App\Repository\TodoListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}