<?php

namespace App\Controller;

use App\Repository\TodoListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}