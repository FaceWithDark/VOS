<?php

declare(strict_types=1);

namespace App\Tests\Service\Web;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Entity\Web\RoleEntity;
use App\Repository\Web\RoleRepository;
use App\Service\Web\UserForeignKeyValidator;


/**
 * NOTE:
 *
 * Since `users`.`role_id` is 1:N, the validator now only owns the existence check
 * for the referenced foreign key.
 */
#[CoversClass(className: UserForeignKeyValidator::class)]
class UserForeignKeyValidatorTest extends TestCase
{
	private RoleRepository&MockObject	$roleRepository;
	private UserForeignKeyValidator		$foreignKeyValidator;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->roleRepository		= $this->createMock(type: RoleRepository::class);
		$this->foreignKeyValidator	= new UserForeignKeyValidator(
			roleRepository: $this->roleRepository,
		);
	}

	private function mockRoleEntity(?int $id = 3): RoleEntity
	{
		return (new RoleEntity())
			->setId(id: $id)
			->setName(name: 'User');
	}


	/**
	 * validatePost() - 404 Not Found
	 */


	#[Test]
	public function testPostWithMissingRoleIdSkipsLookup(): void
	{
		// 'roleId' absent: let the DTO NotNull constraint surface the error
		$this
			->roleRepository
			->expects(self::never())
			->method('find');

		$this
			->foreignKeyValidator
			->validatePost(payload: ['name' => 'Gambler']);
	}

	#[Test]
	public function testPostWithNullRoleIdSkipsLookup(): void
	{
		// `?? null` treats an explicit NULL the same as a missing field
		$this
			->roleRepository
			->expects(self::never())
			->method('find');

		$this
			->foreignKeyValidator
			->validatePost(payload: ['roleId' => null]);
	}

	#[Test]
	public function testPostWithUnknownRoleIdThrowsNotFound(): void
	{
		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with(999)
			->willReturn(null);

		$this->expectException(exception: NotFoundHttpException::class);
		$this->expectExceptionMessage(message: 'Role with ID [999] not found.');

		$this
			->foreignKeyValidator
			->validatePost(payload: ['roleId' => 999]);
	}

	#[Test]
	public function testPostWithKnownRoleIdPasses(): void
	{
		$roleEntity = $this->mockRoleEntity(id: 3);

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with(3)
			->willReturn($roleEntity);

		$this
			->foreignKeyValidator
			->validatePost(payload: ['roleId' => 3]);
	}


	/**
	 * validatePatch() - 400 Bad Request / 404 Not Found
	 */


	#[Test]
	public function testPatchWithoutRoleIdSkipsLookup(): void
	{
		// A partial update that does not touch 'roleId' must not query at all
		$this
			->roleRepository
			->expects(self::never())
			->method('find');

		$this
			->foreignKeyValidator
			->validatePatch(
				payload: ['name' => 'Gambler'],
				id: 88888,
			);
	}

	#[Test]
	public function testPatchWithNullRoleIdThrowsBadRequest(): void
	{
		$roleId = null;

		// An explicit NULL cannot be resolved, so no Role lookup happens
		$this
			->roleRepository
			->expects(self::never())
			->method('find');

		$this->expectException(exception: BadRequestHttpException::class);
		$this->expectExceptionMessage(message: 'Role ID must not be null.');

		$this
			->foreignKeyValidator
			->validatePatch(
				payload: ['roleId' => $roleId],
				id: 88888,
			);
	}

	#[Test]
	public function testPatchWithUnknownRoleIdThrowsNotFound(): void
	{
		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with(999)
			->willReturn(null);

		$this->expectException(exception: NotFoundHttpException::class);
		$this->expectExceptionMessage(message: 'Role with ID [999] not found.');

		$this
			->foreignKeyValidator
			->validatePatch(
				payload: ['roleId' => 999],
				id: 88888,
			);
	}

	#[Test]
	public function testPatchWithKnownRoleIdPasses(): void
	{
		$roleEntity = $this->mockRoleEntity(id: 3);

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with(3)
			->willReturn($roleEntity);

		$this
			->foreignKeyValidator
			->validatePatch(
				payload: ['roleId' => 3],
				id: 88888,
			);
	}
}
