<?php

declare(strict_types=1);

namespace App\Interface\Abstract;


/// --- Main namespaces --- ///


/// --- Type hint namespaces --- ///
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


/// --- Internal namespaces --- ///


interface DuplicateValidatorInterface
{
	/**
	 * @param array<string, mixed>	$payload Fully JSON decoded request body.
	 *
	 * @throws ConflictHttpException 409 when a duplicate exists.
	 */
	public function validatePost(array $payload): void;

	/**
	 * @param array<string, mixed>	$payload	Fully JSON decoded request body.
	 * @param int					$id			ID of the entity being updated.
	 *
	 * @throws BadRequestException 400 when another entity collides.
	 */
	public function validatePatch(array $payload, int $id): void;
}
