<?php

declare(strict_types=1);

namespace App\Service\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


/// --- Type hint namespaces --- ///
use function array_key_exists;
use function is_string;
use function sprintf;
use Override;


/// --- Internal namespaces --- ///
use App\Interface\Web\UserDuplicateValidatorInterface;
use App\Repository\Web\UserRepository;


// @mago-format-ignore-end
final readonly class UserDuplicateValidator implements UserDuplicateValidatorInterface
{
	/** Initialises the instance. */
	public function __construct(
		private UserRepository $repository,
	) {}

	/** Validate patch. */
	#[Override]
	public function validatePatch(array $payload, int $id): void
	{
		// Only validate 'name' field when the client actually sent it (partial update)
		if (!array_key_exists(
			key: 'name',
			array: $payload,
		)) {
			return;
		}

		$userName = $payload['name'];

		if ($userName !== null && !is_string($userName)) {
			return;
		}

		$userCurrentData = $this->repository->findOneBy(criteria: ['name' => $userName]);

		// Detect duplicate data if any field within the incoming payload
		// matched current one
		if ($userCurrentData === null) {
			return;
		}

		if ((int) $userCurrentData->getId() === $id) {
			return;
		}

		throw new BadRequestHttpException(
			message: sprintf(
				'Another user with the name [%s] already exists.',
				$userName,
			),
		);
	}

	/** Validate post. */
	#[Override]
	public function validatePost(array $payload): void
	{
		$userName = $payload['name'] ?? null;

		// Let DTO validation surface the error when 'name' field is missing
		if (!is_string($userName)) {
			return;
		}

		$userCurrentNameValue = $this->repository->findOneBy(criteria: ['name' => $userName]);

		if ($userCurrentNameValue !== null) {
			throw new ConflictHttpException(
				message: sprintf(
					'A user with the name [%s] already exists.',
					$userName,
				),
			);
		}
	}
}
