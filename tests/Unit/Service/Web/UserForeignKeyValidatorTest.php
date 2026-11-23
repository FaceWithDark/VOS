<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Entity\Abstract\RoleAbstract;
use App\Entity\Web\RoleEntity;
use App\Repository\Web\RoleRepository;
use App\Service\Web\UserForeignKeyValidator;


// @mago-format-ignore-end
/**
 * NOTE:
 *
 * Since `users`.`role_id` is 1:N, the validator now only owns the existence check
 * for the referenced foreign key.
 */
#[CoversClass(UserForeignKeyValidator::class)]
#[UsesClass(RoleAbstract::class)]
#[UsesClass(RoleEntity::class)]
class UserForeignKeyValidatorTest extends TestCase
{
	/** Role repository. */
	private RoleRepository&MockObject $roleRepository;
	/** Foreign key validator. */
	private UserForeignKeyValidator $foreignKeyValidator;

	/**
	 * Covers: test patch with known role id passes.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPatchWithKnownRoleIdPasses(): void
	{
		$roleEntity = $this->mockRoleEntity(id: 3);

		$this->roleRepository
			->expects(self::once())
			->method('find')
			->with(3)
			->willReturn($roleEntity)
			->seal();

		$this->foreignKeyValidator->validatePatch(
			payload: ['roleId' => 3],
			id: 88_888,
		);
	}

	/**
	 * Covers: test patch with null role id throws bad request.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPatchWithNullRoleIdThrowsBadRequest(): void
	{
		$roleId = null;

		// An explicit NULL cannot be resolved, so no Role lookup happens
		$this->roleRepository->expects(self::never())->method('find')->seal();

		$this->expectException(BadRequestHttpException::class);
		$this->expectExceptionMessageIs('Role ID must not be null.');

		$this->foreignKeyValidator->validatePatch(
			payload: ['roleId' => $roleId],
			id: 88_888,
		);
	}

	/**
	 * validatePatch() - 400 Bad Request / 404 Not Found
	 */

	#[Test]
	/** @mago-expect lint:literal-named-argument */
	public function testPatchWithoutRoleIdSkipsLookup(): void
	{
		// A partial update that does not touch 'roleId' must not query at all
		$this->roleRepository->expects(self::never())->method('find')->seal();

		$this->foreignKeyValidator->validatePatch(
			payload: ['name' => 'Gambler'],
			id: 88_888,
		);
	}

	/**
	 * Covers: test patch with unknown role id throws not found.
	 * @mago-expect lint:literal-named-argument(4)
	 */
	#[Test]
	public function testPatchWithUnknownRoleIdThrowsNotFound(): void
	{
		$this->roleRepository
			->expects(self::once())
			->method('find')
			->with(999)
			->willReturn(null)
			->seal();

		$this->expectException(NotFoundHttpException::class);
		$this->expectExceptionMessageIs('Role with ID [999] not found.');

		$this->foreignKeyValidator->validatePatch(
			payload: ['roleId' => 999],
			id: 88_888,
		);
	}

	/**
	 * Covers: test post with known role id passes.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPostWithKnownRoleIdPasses(): void
	{
		$roleEntity = $this->mockRoleEntity(id: 3);

		$this->roleRepository
			->expects(self::once())
			->method('find')
			->with(3)
			->willReturn($roleEntity)
			->seal();

		$this->foreignKeyValidator->validatePost(payload: ['roleId' => 3]);
	}

	/**
	 * validatePost() - 404 Not Found
	 */

	#[Test]
	/** @mago-expect lint:literal-named-argument */
	public function testPostWithMissingRoleIdSkipsLookup(): void
	{
		// 'roleId' absent: let the DTO NotNull constraint surface the error
		$this->roleRepository->expects(self::never())->method('find')->seal();

		$this->foreignKeyValidator->validatePost(payload: ['name' => 'Gambler']);
	}

	/**
	 * Covers: test post with null role id skips lookup.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testPostWithNullRoleIdSkipsLookup(): void
	{
		// `?? null` treats an explicit NULL the same as a missing field
		$this->roleRepository->expects(self::never())->method('find')->seal();

		$this->foreignKeyValidator->validatePost(payload: ['roleId' => null]);
	}

	/**
	 * Covers: test post with unknown role id throws not found.
	 * @mago-expect lint:literal-named-argument(4)
	 */
	#[Test]
	public function testPostWithUnknownRoleIdThrowsNotFound(): void
	{
		$this->roleRepository
			->expects(self::once())
			->method('find')
			->with(999)
			->willReturn(null)
			->seal();

		$this->expectException(NotFoundHttpException::class);
		$this->expectExceptionMessageIs('Role with ID [999] not found.');

		$this->foreignKeyValidator->validatePost(payload: ['roleId' => 999]);
	}

	/** Covers: set up. */
	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->roleRepository      = $this->createMock(RoleRepository::class);
		$this->foreignKeyValidator = new UserForeignKeyValidator(
			roleRepository: $this->roleRepository,
		);
	}

	/** Covers: mock role entity. */
	private function mockRoleEntity(int $id = 3): RoleEntity
	{
		return new RoleEntity()
			->setId(id: $id)
			->setName(name: 'User');
	}
}
