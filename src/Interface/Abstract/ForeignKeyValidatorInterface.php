<?php

declare(strict_types=1);

namespace App\Interface\Abstract;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
interface ForeignKeyValidatorInterface
{
	/**
	 * @param array<array-key, mixed> $payload Fully JSON decoded request body.
	 * @param int $id ID of the entity being updated.
	 *
	 * @throws BadRequestHttpException 400 when an explicit NULL targets a
	 *                                 column that does not accept it.
	 * @throws NotFoundHttpException 404 when the referenced record is unknown.
	 */
	public function validatePatch(array $payload, int $id): void;

	/**
	 * @param array<array-key, mixed> $payload Fully JSON decoded request body.
	 *
	 * @throws NotFoundHttpException 404 when the referenced record is unknown.
	 */
	public function validatePost(array $payload): void;
}
