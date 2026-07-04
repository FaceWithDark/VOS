<?php

declare(strict_types=1);


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class TournamentController extends AbstractController
{
	#[Route(
		path: '/tournament',
		name: 'tournament_index',
		methods: ['GET']
	)]
	public function index(): Response
	{
		return $this->render(
			view: 'mains/Tournament/index.html.twig',
			parameters: [
				'controller_name' => 'TournamentController',
			]
		);
	}
}
