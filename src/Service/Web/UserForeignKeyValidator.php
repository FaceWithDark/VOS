<?php

declare(strict_types=1);

namespace App\Service\Web;


/// --- Main namespaces --- ///
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Interface\Web\UserForeignKeyValidatorInterface;
use App\Repository\Web\RoleRepository;
use App\Repository\Web\UserRepository;


final readonly class UserForeignKeyValidator implements UserForeignKeyValidatorInterface
{
	public function __construct(
		private RoleRepository	$roleRepository,
		private UserRepository	$userRepository,
	) {}

	#[Override]
	public function validatePost(array $payload): void
	{
		$roleId = $payload['roleId'] ?? null;

		// Let DTO validation surface the error when 'roleId' field is missing
		if ($roleId === null) {
			return;
		}

		$roleEntity = $this->roleRepository->find(id: $roleId);

		// Let UserProcessor::resolveRole() surface the 400 when the FK is unknown
		if ($roleEntity === null) {
			return;
		}

		// 1:1 relationship - a role can only ever belong to a single user
		if ($this->userRepository->findOneBy(criteria: ['roleId' => $roleEntity]) !== null) {
			throw new ConflictHttpException(
				message: "Role with ID [{$roleId}] is already assigned to another user."
			);
		}
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

		// Leave explicit NULL handling to UserProcessor::resolveRole()
		if ($roleId === null) {
			return;
		}

		$roleEntity = $this->roleRepository->find(id: $roleId);

		// Let UserProcessor::resolveRole() surface the 400 when the FK is unknown
		if ($roleEntity === null) {
			return;
		}

		$userWithRole = $this->userRepository->findOneBy(criteria: ['roleId' => $roleEntity]);

		// Detect a 1:1 collision only when another user already holds the role
		if ($userWithRole === null || (int) $userWithRole->getId() === $id) {
			return;
		}

		throw new BadRequestHttpException(
			message: "Another user with role ID [{$roleId}] already exists."
		);
	}
}
