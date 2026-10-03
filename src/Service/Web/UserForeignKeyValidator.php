<?php

declare(strict_types=1);

namespace App\Service\Web;


/// --- Main namespaces --- ///
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Interface\Web\UserForeignKeyValidatorInterface;
use App\Repository\Web\RoleRepository;


/**
 * NOTE:
 *
 * `users`.`role_id` is a 1:N relation, so a single role MAY be shared by many
 * users. This validator therefore only owns the existence check for the
 * referenced foreign key; there is no "already assigned" conflict anymore.
 */
final readonly class UserForeignKeyValidator implements UserForeignKeyValidatorInterface
{
	public function __construct(private RoleRepository $roleRepository) {}

	#[Override]
	public function validatePost(array $payload): void
	{
		$roleId = $payload['roleId'] ?? null;

		// Let DTO validation surface the error when 'roleId' field is missing/null
		if ($roleId === null) {
			return;
		}

		$this->assertRoleExists(roleId: $roleId);
	}

	#[Override]
	public function validatePatch(array $payload, int $id): void
	{
		// Only validate 'roleId' field when the client actually sent it (partial update)
		if (!array_key_exists(
			key: 'roleId',
			array: $payload,
		)) {
			return;
		}

		$roleId = $payload['roleId'] ?? null;

		// The owning side is NOT NULL, so an explicit NULL can never be applied
		if ($roleId === null) {
			throw new BadRequestHttpException(
				message: 'Role ID must not be null.',
			);
		}

		$this->assertRoleExists(roleId: $roleId);
	}

	private function assertRoleExists(int $roleId): void
	{
		if ($this->roleRepository->find(id: $roleId) !== null) {
			return;
		}

		throw new NotFoundHttpException(
			message: sprintf(
				'Role with ID [%d] not found.',
				$roleId,
			),
		);
	}
}
