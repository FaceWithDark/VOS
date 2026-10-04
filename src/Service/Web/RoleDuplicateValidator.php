<?php

declare(strict_types=1);

namespace App\Service\Web;


/// --- Main namespaces --- ///
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Interface\Web\RoleDuplicateValidatorInterface;
use App\Repository\Web\RoleRepository;


final readonly class RoleDuplicateValidator implements RoleDuplicateValidatorInterface
{
	public function __construct(private RoleRepository $repository) {}

	#[Override]
	public function validatePost(array $payload): void
	{
		$roleName = $payload['name'] ?? null;

		// Let DTO validation surface the error when 'name' field is missing
		if ($roleName === null) {
			return;
		}

		$roleCurrentNameValue = $this->repository->findOneBy(criteria: ['name' => $roleName]);

		if ($roleCurrentNameValue !== null) {
			throw new ConflictHttpException(
				message: sprintf(
					'A role with the name [%s] already exists.',
					$roleName,
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

		$roleCurrentData = $this->repository->findOneBy(criteria: ['name' => $payload['name']]);

		// Detect duplicate data if any field within the incoming payload
		// matched current one
		if ($roleCurrentData === null) {
			return;
		}

		if ((int) $roleCurrentData->getId() === $id) {
			return;
		}

		throw new BadRequestHttpException(
			message: sprintf(
				'Another role with the name [%s] already exists.',
				$payload['name'],
			)
		);
	}
}
