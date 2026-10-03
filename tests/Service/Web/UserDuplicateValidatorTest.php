<?php

declare(strict_types=1);

namespace App\Tests\Service\Web;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Entity\Web\RoleEntity;
use App\Entity\Web\UserEntity;
use App\Repository\Web\UserRepository;
use App\Service\Web\UserDuplicateValidator;


/**
 * NOTE:
 *
 * The validator is a thin policy layer around {@see UserRepository}: it decides
 * which HTTP error a repeated user name maps to. The repository is mocked so
 * each test pins exactly one policy branch.
 */
#[CoversClass(className: UserDuplicateValidator::class)]
class UserDuplicateValidatorTest extends TestCase
{
	private UserRepository&MockObject	$repository;
	private UserDuplicateValidator		$duplicateValidator;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository			= $this->createMock(type: UserRepository::class);
		$this->duplicateValidator	= new UserDuplicateValidator(repository: $this->repository);
	}

	/**
	 * NOTE:
	 *
	 * Unlike {@see RoleEntity}, {@see UserEntity} ships with no default data
	 * fixtures. Therefore, we must create a valid user so that it can be
	 * adjusted to the specific scenario under each test.
	 */
	private function mockUserEntity(
		?int	$id		= 88888,
		?string	$name	= 'Gambler',
	): UserEntity
	{
		return (new UserEntity())
			->setId(id: $id)
			->setRoleId(
				roleId: (new RoleEntity())
					->setId(id: 1)
					->setName(name: 'User')
			)
			->setName(name: $name)
			->setAvatar(avatar: 'https://a.ppy.sh/88?88.png')
			->setRank(rank: 88)
			->setCountryFlag(countryFlag: 'ZW');
	}


	/**
	 * validatePost() - 409 Conflict
	 */


	#[Test]
	public function testPostWithMissingNameSkipsLookup(): void
	{
		// 'name' absent: let the DTO NotBlank constraint surface the error
		$this
			->repository
			->expects(self::never())
			->method('findOneBy');

		$this
			->duplicateValidator
			->validatePost(payload: ['avatar' => 'https://a.ppy.sh/88?88.png']);
	}

	#[Test]
	public function testPostWithNullNameSkipsLookup(): void
	{
		// `?? null` treats an explicit NULL the same as a missing field
		$this
			->repository
			->expects(self::never())
			->method('findOneBy');

		$this
			->duplicateValidator
			->validatePost(payload: ['name' => null]);
	}

	#[Test]
	public function testPostWithUniqueNamePasses(): void
	{
		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn(null);

		$this
			->duplicateValidator
			->validatePost(payload: ['name' => 'Gambler']);
	}

	#[Test]
	public function testPostWithDuplicateNameThrowsConflict(): void
	{
		$current = $this->mockUserEntity(name: 'Gambler');

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn($current);

		$this->expectException(exception: ConflictHttpException::class);
		$this->expectExceptionMessage(message: 'A user with the name [Gambler] already exists.');

		$this
			->duplicateValidator
			->validatePost(payload: ['name' => 'Gambler']);
	}

	#[Test]
	public function testPostChecksDuplicateByNameOnly(): void
	{
		// The sibling 'avatar' field must never leak into the criteria
		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn(null);

		$this
			->duplicateValidator
			->validatePost(payload: [
				'name'		=> 'Gambler',
				'avatar'	=> 'https://a.ppy.sh/88?88.png',
			]);
	}


	/**
	 * validatePatch() - 400 Bad Request
	 */


	#[Test]
	public function testPatchWithoutNameSkipsLookup(): void
	{
		// A partial update that does not touch 'name' must not query at all
		$this
			->repository
			->expects(self::never())
			->method('findOneBy');

		$this
			->duplicateValidator
			->validatePatch(
				payload: ['avatar' => 'https://a.ppy.sh/88?88.png'],
				id: 88888,
			);
	}

	#[Test]
	public function testPatchWithNullNameLooksUpNullAndPasses(): void
	{
		// `array_key_exists` treats an explicit NULL as a real update, so the
		// NULL is looked up and simply not found.
		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => null])
			->willReturn(null);

		$this
			->duplicateValidator
			->validatePatch(
				payload: ['name' => null],
				id: 88888,
			);
	}

	#[Test]
	public function testPatchWithUniqueNamePasses(): void
	{
		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn(null);

		$this
			->duplicateValidator
			->validatePatch(
				payload: ['name' => 'Gambler'],
				id: 88888,
			);
	}

	#[Test]
	public function testPatchWithOwnNamePasses(): void
	{
		$current = $this->mockUserEntity(
			id: 88888,
			name: 'Gambler',
		);

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn($current);

		// Re-sending the current value must NOT be treated as a duplicate
		$this
			->duplicateValidator
			->validatePatch(
				payload: ['name' => 'Gambler'],
				id: 88888,
			);
	}

	#[Test]
	public function testPatchWithAnotherEntityNameThrowsBadRequest(): void
	{
		$current = $this->mockUserEntity(
			id: 19817503,
			name: 'Gambler',
		);

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn($current);

		$this->expectException(exception: BadRequestHttpException::class);
		$this->expectExceptionMessage(message: 'Another user with the name [Gambler] already exists.');

		$this
			->duplicateValidator
			->validatePatch(
				payload: ['name' => 'Gambler'],
				id: 88888,
			);
	}

	#[Test]
	public function testPatchChecksDuplicateByNameOnly(): void
	{
		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with(['name' => 'Gambler'])
			->willReturn(null);

		$this
			->duplicateValidator
			->validatePatch(
				payload: [
					'name'		=> 'Gambler',
					'avatar'	=> 'https://a.ppy.sh/88?88.png',
				],
				id: 88888,
			);
	}
}
