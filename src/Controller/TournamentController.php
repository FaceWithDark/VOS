<?php

declare(strict_types=1);

namespace App\Controller;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
final class TournamentController extends AbstractController
{
	/** Index. */
	#[Route(
		path: '/tournament',
		name: 'tournament_index',
		methods: ['GET'],
	)]
	public function index(): Response
	{
		return $this->render(
			view: 'mains/Tournament/index.html.twig',
			parameters: [
				'controller_name' => 'TournamentController',
			],
		);
	}
}
