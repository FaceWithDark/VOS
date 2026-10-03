<?php

declare(strict_types=1);

namespace App\Interface\Abstract;


/// --- Main namespaces --- ///


/// --- Type hint namespaces --- ///
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/// --- Internal namespaces --- ///


interface ForeignKeyValidatorInterface
{
	/**
	 * @param array<string, mixed>	$payload Fully JSON decoded request body.
	 *
	 * @throws NotFoundHttpException 404 when the referenced record is unknown.
	 */
	public function validatePost(array $payload): void;

	/**
	 * @param array<string, mixed>	$payload	Fully JSON decoded request body.
	 * @param int					$id			ID of the entity being updated.
	 *
	 * @throws BadRequestHttpException 400 when an explicit NULL targets a
	 *                                 column that does not accept it.
	 * @throws NotFoundHttpException   404 when the referenced record is unknown.
	 */
	public function validatePatch(array $payload, int $id): void;
}
