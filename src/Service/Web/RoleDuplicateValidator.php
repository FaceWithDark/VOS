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
use App\Interface\Web\RoleDuplicateValidatorInterface;
use App\Repository\Web\RoleRepository;


// @mago-format-ignore-end
final readonly class RoleDuplicateValidator implements RoleDuplicateValidatorInterface
{
	/** Initialises the instance. */
	public function __construct(
		private RoleRepository $repository,
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

		$roleName = $payload['name'];

		if ($roleName !== null && !is_string($roleName)) {
			return;
		}

		$roleCurrentData = $this->repository->findOneBy(criteria: ['name' => $roleName]);

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
				$roleName,
			),
		);
	}

	/** Validate post. */
	#[Override]
	public function validatePost(array $payload): void
	{
		$roleName = $payload['name'] ?? null;

		// Let DTO validation surface the error when 'name' field is missing
		if (!is_string($roleName)) {
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
}
