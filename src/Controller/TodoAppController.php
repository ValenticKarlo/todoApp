<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @method User getUser()
 */
class TodoAppController extends AbstractController
{
    #[Route('/', name: 'app_todoapp_homepage')]
    public function homepage(): Response
    {


        return $this->render('todoApp/homepage.html.twig');
    }
    public function securityCheck(?TodoList $list):void
    {
        if( !$list ){throw $this->createNotFoundException('List not found');}
    }
}