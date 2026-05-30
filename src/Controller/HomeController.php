<?php

declare(strict_types=1);


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class HomeController extends AbstractController
{
    #[Route(
        path: '/home',
        name: 'home_index',
        methods: ["GET"]
    )]
    public function index(): Response
    {
        return $this->render(
            view: 'home/index.html.twig',
            parameters: [
                'controller_name' => 'HomeController',
            ]
        );
    }
}
