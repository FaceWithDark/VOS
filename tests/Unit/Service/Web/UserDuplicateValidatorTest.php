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
use App\Entity\Abstract\UserAbstract;
use App\Entity\Web\RoleEntity;
use App\Entity\Web\UserEntity;
use App\Repository\Web\UserRepository;
use App\Service\Web\UserDuplicateValidator;


// @mago-format-ignore-end
/**
 * NOTE:
 *
 * The validator is a thin policy layer around {@see UserRepository}: it decides
 * which HTTP error a repeated user name maps to. The repository is mocked so
 * each test pins exactly one policy branch.
 */
#[CoversClass(UserDuplicateValidator::class)]
#[UsesClass(RoleAbstract::class)]
#[UsesClass(RoleEntity::class)]
#[UsesClass(UserAbstract::class)]
#[UsesClass(UserEntity::class)]
class UserDuplicateValidatorTest extends TestCase
{
	/** Repository. */
	protected UserRepository&MockObject $repository;

	/** Duplicate validator. */
	protected UserDuplicateValidator $duplicateValidator;

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
				'name'   => 'Gambler',
				'avatar' => 'https://a.ppy.sh/88?88.png',
			],
			id: 88_888,
		);
	}

	/**
	 * Covers: test patch with another entity name throws bad request.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPatchWithAnotherEntityNameThrowsBadRequest(): void
	{
		$current = $this->mockUserEntity(
			id: 19_817_503,
			name: 'Gambler',
		);

		$this->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn($current)
			->seal();

		$this->expectException(BadRequestHttpException::class);

		$this->expectExceptionMessageIs('Another user with the name [Gambler] already exists.');

		$this->duplicateValidator->validatePatch(
			payload: ['name' => 'Gambler'],
			id: 88_888,
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
			id: 88_888,
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
			payload: ['avatar' => 'https://a.ppy.sh/88?88.png'],
			id: 88_888,
		);
	}

	/**
	 * Covers: test patch with own name passes.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testPatchWithOwnNamePasses(): void
	{
		$current = $this->mockUserEntity(
			id: 88_888,
			name: 'Gambler',
		);

		$this->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn($current)
			->seal();

		// Re-sending the current value must NOT be treated as a duplicate
		$this->duplicateValidator->validatePatch(
			payload: ['name' => 'Gambler'],
			id: 88_888,
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
			id: 88_888,
		);
	}

	/**
	 * Covers: test post checks duplicate by name only.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPostChecksDuplicateByNameOnly(): void
	{
		// The sibling 'avatar' field must never leak into the criteria
		$this->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn(null)
			->seal();

		$this->duplicateValidator->validatePost(payload: [
			'name'   => 'Gambler',
			'avatar' => 'https://a.ppy.sh/88?88.png',
		]);
	}

	/**
	 * Covers: test post with duplicate name throws conflict.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPostWithDuplicateNameThrowsConflict(): void
	{
		$current = $this->mockUserEntity(name: 'Gambler');

		$this->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn($current)
			->seal();

		$this->expectException(ConflictHttpException::class);

		$this->expectExceptionMessageIs('A user with the name [Gambler] already exists.');

		$this->duplicateValidator->validatePost(payload: ['name' => 'Gambler']);
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

		$this->duplicateValidator->validatePost(payload: ['avatar' => 'https://a.ppy.sh/88?88.png']);
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

	/**
	 * NOTE:
	 *
	 * Unlike {@see RoleEntity}, {@see UserEntity} ships with no default data
	 * fixtures. Therefore, we must create a valid user so that it can be
	 * adjusted to the specific scenario under each test.
	 */
	protected function mockUserEntity(
		int    $id = 88_888,
		string $name = 'Gambler',
	): UserEntity {
		return new UserEntity()
			->setId(id: $id)
			->setRoleId(
				roleId: new RoleEntity()
					->setId(id: 1)
					->setName(name: 'User'),
			)
			->setName(name: $name)
			->setAvatar(avatar: 'https://a.ppy.sh/88?88.png')
			->setRank(rank: 88)
			->setCountryFlag(countryFlag: 'ZW');
	}

	/** Prepares the test fixtures. */
	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository = $this->createMock(UserRepository::class);

		$this->duplicateValidator = new UserDuplicateValidator(repository: $this->repository);
	}
}
