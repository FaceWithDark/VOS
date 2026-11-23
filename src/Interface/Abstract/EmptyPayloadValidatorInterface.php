<?php

declare(strict_types=1);

namespace App\Interface\Abstract;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
interface EmptyPayloadValidatorInterface
{
	/**
	 * POST bodies are already deserialized & validated by API Platform before
	 * the processor runs, so only PATCH needs this guard.
	 *
	 * @param array<array-key, mixed> $payload Fully JSON decoded request body.
	 *
	 * @throws BadRequestHttpException 400 when the payload is empty.
	 */
	public function validatePatch(array $payload): void;
}
