<?php

declare(strict_types=1);

namespace App\Interface\Abstract;


/// --- Main namespaces --- ///


/// --- Type hint namespaces --- ///
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


/// --- Internal namespaces --- ///


interface EmptyPayloadValidatorInterface
{
	/**
	 * POST bodies are already deserialized & validated by API Platform before
	 * the processor runs, so only PATCH needs this guard.
	 *
	 * @param array<string, mixed>	$payload Fully JSON decoded request body.
	 *
	 * @throws BadRequestHttpException 400 when the payload is empty.
	 */
	public function validatePatch(array $payload): void;
}
