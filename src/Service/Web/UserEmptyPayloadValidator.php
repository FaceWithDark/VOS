<?php

declare(strict_types=1);

namespace App\Service\Web;


/// --- Main namespaces --- ///
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Interface\Web\UserEmptyPayloadValidatorInterface;


/**
 * NOTE:
 *
 * Rejects a PATCH request whose decoded body is empty. The check is strict on
 * the array, not on its values, so `{"name": ""}` or `{"rank": 0}` still counts
 * as a present payload.
 */
final readonly class UserEmptyPayloadValidator implements UserEmptyPayloadValidatorInterface
{
	#[Override]
	public function validatePatch(array $payload): void
	{
		if ($payload !== []) {
			return;
		}

		throw new BadRequestHttpException(
			message: 'Request payload must not be empty.',
		);
	}
}
