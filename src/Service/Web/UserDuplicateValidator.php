<?php

declare(strict_types=1);

namespace App\Service\Web;


/// --- Main namespaces --- ///
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Interface\Web\UserDuplicateValidatorInterface;
use App\Repository\Web\UserRepository;


final readonly class UserDuplicateValidator implements UserDuplicateValidatorInterface
{
	public function __construct(private UserRepository $repository) {}

	#[Override]
	public function validatePost(array $payload): void
	{
		$userName = $payload['name'] ?? null;

		// Let DTO validation surface the error when 'name' field is missing
		if ($userName === null) {
			return;
		}

		$userCurrentNameValue = $this->repository->findOneBy(criteria: ['name' => $userName]);

		if ($userCurrentNameValue !== null) {
			throw new ConflictHttpException(
				message: sprintf(
					'A user with the name [] already exists.',
					$userName,
				),
			);
		}
	}

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

		$userCurrentData = $this->repository->findOneBy(criteria: ['name' => $payload['name']]);

		// Detect duplicate data if any field within the incoming payload
		// matched current one
		if ($userCurrentData === null || (int) $userCurrentData->getId() === $id) {
			return;
		}

		throw new BadRequestHttpException(
			message: sprintf(
				'Another user with the name [%s] already exists.',
				$payload['name'],
			),
		);
	}
}
