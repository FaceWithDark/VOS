<?php

declare(strict_types=1);

namespace App\Interface\Abstract;


/// --- Main namespaces --- ///


/// --- Type hint namespaces --- ///
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


/// --- Internal namespaces --- ///


interface ForeignKeyValidatorInterface
{
	/**
	 * @param array<string, mixed>	$payload Fully JSON decoded request body.
	 *
	 * @throws ConflictHttpException 409 when the referenced record is already
	 *                               linked to another entity (1:1 relationship).
	 */
	public function validatePost(array $payload): void;

	/**
	 * @param array<string, mixed>	$payload	Fully JSON decoded request body.
	 * @param int					$id			ID of the entity being updated.
	 *
	 * @throws BadRequestHttpException 400 when another entity already
	 *                                 references the record.
	 */
	public function validatePatch(array $payload, int $id): void;
}
