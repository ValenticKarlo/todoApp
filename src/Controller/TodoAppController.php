<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoAppController extends AbstractController
{
    #[Route('/', name: 'app_todoapp_homepage')]
    public function homepage(): Response
    {


        return $this->render('todoApp/homepage.html.twig');
    }

}