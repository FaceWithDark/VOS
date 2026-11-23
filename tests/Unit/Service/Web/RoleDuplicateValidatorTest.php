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
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Entity\Abstract\RoleAbstract;
use App\Entity\Web\RoleEntity;
use App\Repository\Web\RoleRepository;
use App\Service\Web\RoleDuplicateValidator;


// @mago-format-ignore-end
/**
 * NOTE:
 *
 * The validator is a thin policy layer around {@see RoleRepository}: it decides
 * which HTTP error a repeated role name maps to. The repository is mocked so
 * each test pins exactly one policy branch.
 */
#[CoversClass(RoleDuplicateValidator::class)]
#[UsesClass(RoleEntity::class)]
#[UsesClass(RoleAbstract::class)]
class RoleDuplicateValidatorTest extends TestCase
{
	/** Repository. */
	protected RoleRepository&MockObject $repository;

	/** Duplicate validator. */
	protected RoleDuplicateValidator $duplicateValidator;

	/**
	 * Covers: test patch checks duplicate by name only.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPatchChecksDuplicateByNameOnly(): void
	{
		$this->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn(null)
			->seal();

		$this->duplicateValidator->validatePatch(
			payload: [
				'name'        => 'Gambler',
				'description' => 'double the pay, double the deal baby.',
			],
			id: 2,
		);
	}

	/**
	 * Covers: test patch with another entity name throws bad request.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPatchWithAnotherEntityNameThrowsBadRequest(): void
	{
		$current = $this->mockRoleEntity(
			id: 1,
			name: 'Admin',
		);

		$this->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Admin'])
			->willReturn($current)
			->seal();

		$this->expectException(BadRequestHttpException::class);

		$this->expectExceptionMessageIs('Another role with the name [Admin] already exists.');

		$this->duplicateValidator->validatePatch(
			payload: ['name' => 'Admin'],
			id: 2,
		);
	}

	/**
	 * Covers: test patch with null name looks up null and passes.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPatchWithNullNameLooksUpNullAndPasses(): void
	{
		// `array_key_exists` treats an explicit NULL as a real update, so the
		// NULL is looked up and simply not found.
		$this->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => null])
			->willReturn(null)
			->seal();

		$this->duplicateValidator->validatePatch(
			payload: ['name' => null],
			id: 2,
		);
	}

	/**
	 * Covers: test patch without name skips lookup.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testPatchWithoutNameSkipsLookup(): void
	{
		// A partial update that does not touch 'name' must not query at all
		$this->repository->expects(self::never())->method('findOneBy')->seal();

		$this->duplicateValidator->validatePatch(
			payload: ['description' => 'double the pay, double the deal baby.'],
			id: 2,
		);
	}

	/**
	 * Covers: test patch with own name passes.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testPatchWithOwnNamePasses(): void
	{
		$current = $this->mockRoleEntity(
			id: 2,
			name: 'Admin',
		);

		$this->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Admin'])
			->willReturn($current)
			->seal();

		// Re-sending the current value must NOT be treated as a duplicate
		$this->duplicateValidator->validatePatch(
			payload: ['name' => 'Admin'],
			id: 2,
		);
	}

	/**
	 * Covers: test patch with unique name passes.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPatchWithUniqueNamePasses(): void
	{
		$this->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn(null)
			->seal();

		$this->duplicateValidator->validatePatch(
			payload: ['name' => 'Gambler'],
			id: 2,
		);
	}

	/**
	 * Covers: test post checks duplicate by name only.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPostChecksDuplicateByNameOnly(): void
	{
		// The sibling 'description' field must never leak into the criteria
		$this->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn(null)
			->seal();

		$this->duplicateValidator->validatePost(payload: [
			'name'        => 'Gambler',
			'description' => 'double the pay, double the deal baby.',
		]);
	}

	/**
	 * Covers: test post with duplicate name throws conflict.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPostWithDuplicateNameThrowsConflict(): void
	{
		$current = $this->mockRoleEntity(name: 'Admin');

		$this->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Admin'])
			->willReturn($current)
			->seal();

		$this->expectException(ConflictHttpException::class);

		$this->expectExceptionMessageIs('A role with the name [Admin] already exists.');

		$this->duplicateValidator->validatePost(payload: ['name' => 'Admin']);
	}

	/**
	 * validatePost() - 409 Conflict
	 */
	/**
	 * validatePatch() - 400 Bad Request
	 */

	/**
	 * Covers: test post with missing name skips lookup.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testPostWithMissingNameSkipsLookup(): void
	{
		// 'name' absent: let the DTO NotBlank constraint surface the error
		$this->repository->expects(self::never())->method('findOneBy')->seal();

		$this->duplicateValidator->validatePost(payload: [
			'description' => 'double the pay, double the deal baby.',
		]);
	}

	/**
	 * Covers: test post with null name skips lookup.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testPostWithNullNameSkipsLookup(): void
	{
		// `?? null` treats an explicit NULL the same as a missing field
		$this->repository->expects(self::never())->method('findOneBy')->seal();

		$this->duplicateValidator->validatePost(payload: ['name' => null]);
	}

	/**
	 * Covers: test post with unique name passes.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPostWithUniqueNamePasses(): void
	{
		$this->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn(null)
			->seal();

		$this->duplicateValidator->validatePost(payload: ['name' => 'Gambler']);
	}

	/** Mock role entity. */
	protected function mockRoleEntity(
		int    $id = 2,
		string $name = 'Admin',
	): RoleEntity {
		return new RoleEntity()
			->setId(id: $id)
			->setName(name: $name);
	}

	/** Set up. */
	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository = $this->createMock(RoleRepository::class);

		$this->duplicateValidator = new RoleDuplicateValidator(repository: $this->repository);
	}
}
