<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RouteController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(): Response
    {
        return $this->render('route/index.html.twig', [
            'controller_name' => 'RouteController',
        ]);
    }

    // #[Route('/tasks', name: 'tasks')]
    // public function tasks(): Response
    // {
    //     return $this->render('task/index.html.twig', [
    //         'controller_name' => 'RouteController',
    //     ]);
    // }   
    
}
