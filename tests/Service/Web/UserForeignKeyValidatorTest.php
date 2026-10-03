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
use App\Repository\Web\RoleRepository;
use App\Repository\Web\UserRepository;
use App\Service\Web\UserForeignKeyValidator;


/**
 * NOTE:
 *
 * Every test drives both collaborators, so no mock is left without an
 * expectation and PHPUnit's "mock without expectations" notice never fires.
 */
#[CoversClass(className: UserForeignKeyValidator::class)]
class UserForeignKeyValidatorTest extends TestCase
{
	private RoleRepository&MockObject	$roleRepository;
	private UserRepository&MockObject	$userRepository;
	private UserForeignKeyValidator		$foreignKeyValidator;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->roleRepository		= $this->createMock(type: RoleRepository::class);
		$this->userRepository		= $this->createMock(type: UserRepository::class);
		$this->foreignKeyValidator	= new UserForeignKeyValidator(
			roleRepository: $this->roleRepository,
			userRepository: $this->userRepository,
		);
	}

	private function mockRoleEntity(
		?int	$id		= 3,
		?string	$name	= 'User',
	): RoleEntity
	{
		return (new RoleEntity())
			->setId(id: $id)
			->setName(name: $name);
	}

	/**
	 * NOTE:
	 *
	 * Unlike {@see RoleEntity}, {@see UserEntity} ships with no default data
	 * fixtures. Therefore, we must create a valid mock user so that it can be
	 * adjusted to the specific scenario under each test.
	 */
	private function mockUserEntity(
		?int		$id				= 88888,
		?RoleEntity	$roleId			= null,
		?string		$name			= 'Gambler',
		?string		$avatar			= 'https://a.ppy.sh/88?88.png',
		?int		$rank			= 88,
		?string		$countryFlag	= 'ZW',
	): UserEntity
	{
		return (new UserEntity())
			->setId(id: $id)
			->setRoleId(
				roleId: $roleId ?? $this->mockRoleEntity(id: 1)
			)
			->setName(name: $name)
			->setAvatar(avatar: $avatar)
			->setRank(rank: $rank)
			->setCountryFlag(countryFlag: $countryFlag);
	}


	/**
	 * validatePost() - 409 Conflicts
	 */


	#[Test]
	public function testMissingRoleIdFieldOnPost(): void
	{
		$testPayload = ['name' => 'Gambler'];

		$this
			->roleRepository
			->expects(self::never())
			->method('find');

		$this
			->userRepository
			->expects(self::never())
			->method('findOneBy');

		$this
			->foreignKeyValidator
			->validatePost(payload: $testPayload);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testNullRoleIdFieldOnPost(): void
	{
		$testPayload = ['roleId' => null];

		$this
			->roleRepository
			->expects(self::never())
			->method('find');

		$this
			->userRepository
			->expects(self::never())
			->method('findOneBy');

		$this
			->foreignKeyValidator
			->validatePost(payload: $testPayload);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testUnknownRoleIdFieldOnPost(): void
	{
		$testPayload = ['roleId' => 999];

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with($testPayload['roleId'])
			->willReturn(null);

		// No 1:1 collision can be detected when the FK itself is unknown
		$this
			->userRepository
			->expects(self::never())
			->method('findOneBy');

		$this
			->foreignKeyValidator
			->validatePost(payload: $testPayload);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testFreeRoleIdFieldOnPost(): void
	{
		$testPayload	= ['roleId' => 3];
		$roleEntity		= $this->mockRoleEntity(id: $testPayload['roleId']);

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with($testPayload['roleId'])
			->willReturn($roleEntity);

		$this
			->userRepository
			->expects(self::once())
			->method('findOneBy')
			->with(['roleId' => $roleEntity])
			->willReturn(null);

		$this
			->foreignKeyValidator
			->validatePost(payload: $testPayload);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testTakenRoleIdFieldOnPost(): void
	{
		$testPayload	= ['roleId' => 3];
		$roleEntity		= $this->mockRoleEntity(id: $testPayload['roleId']);
		$userCurrentData
			= $this->mockUserEntity(
				id: 88888,
				roleId: $roleEntity,
			);

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with($testPayload['roleId'])
			->willReturn($roleEntity);

		$this
			->userRepository
			->expects(self::once())
			->method('findOneBy')
			->with(['roleId' => $roleEntity])
			->willReturn($userCurrentData);

		$this->expectException(exception: ConflictHttpException::class);
		$this->expectExceptionMessage(message: "Role with ID [{$testPayload['roleId']}] is already assigned to another user.");

		$this
			->foreignKeyValidator
			->validatePost(payload: $testPayload);
	}


	/**
	 * validatePatch() - 400 Bad Request
	 */


	#[Test]
	public function testMissingRoleIdFieldOnPatch(): void
	{
		$testPayload = ['name' => 'Gambler'];

		$this
			->roleRepository
			->expects(self::never())
			->method('find');

		$this
			->userRepository
			->expects(self::never())
			->method('findOneBy');

		$this
			->foreignKeyValidator
			->validatePatch(
				payload: $testPayload,
				id: 88888,
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testNullRoleIdFieldOnPatch(): void
	{
		$testPayload = ['roleId' => null];

		$this
			->roleRepository
			->expects(self::never())
			->method('find');

		$this
			->userRepository
			->expects(self::never())
			->method('findOneBy');

		$this
			->foreignKeyValidator
			->validatePatch(
				payload: $testPayload,
				id: 88888,
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testUnknownRoleIdFieldOnPatch(): void
	{
		$testPayload = ['roleId' => 999];

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with($testPayload['roleId'])
			->willReturn(null);

		// No 1:1 collision can be detected when the FK itself is unknown
		$this
			->userRepository
			->expects(self::never())
			->method('findOneBy');

		$this
			->foreignKeyValidator
			->validatePatch(
				payload: $testPayload,
				id: 88888,
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testFreeRoleIdFieldOnPatch(): void
	{
		$testPayload	= ['roleId' => 3];
		$roleEntity		= $this->mockRoleEntity(id: $testPayload['roleId']);

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with($testPayload['roleId'])
			->willReturn($roleEntity);

		$this
			->userRepository
			->expects(self::once())
			->method('findOneBy')
			->with(['roleId' => $roleEntity])
			->willReturn(null);

		$this
			->foreignKeyValidator
			->validatePatch(
				payload: $testPayload,
				id: 88888,
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testSameEntityRoleIdFieldOnPatch(): void
	{
		$testPayload	= ['roleId' => 3];
		$roleEntity		= $this->mockRoleEntity(id: $testPayload['roleId']);
		$userCurrentData
			= $this->mockUserEntity(
				id: 88888,
				roleId: $roleEntity,
			);

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with($testPayload['roleId'])
			->willReturn($roleEntity);

		$this
			->userRepository
			->expects(self::once())
			->method('findOneBy')
			->with(['roleId' => $roleEntity])
			->willReturn($userCurrentData);

		$this
			->foreignKeyValidator
			->validatePatch(
				payload: $testPayload,
				id: 88888,
			);

		// Re-assigning the same role to its current owner is a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testDifferentEntityRoleIdFieldOnPatch(): void
	{
		$testPayload	= ['roleId' => 3];
		$roleEntity		= $this->mockRoleEntity(id: $testPayload['roleId']);
		$userCurrentData
			= $this->mockUserEntity(
				id: 19817503,
				roleId: $roleEntity,
			);

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with($testPayload['roleId'])
			->willReturn($roleEntity);

		$this
			->userRepository
			->expects(self::once())
			->method('findOneBy')
			->with(['roleId' => $roleEntity])
			->willReturn($userCurrentData);

		$this->expectException(exception: BadRequestHttpException::class);
		$this->expectExceptionMessage(message: "Another user with role ID [{$testPayload['roleId']}] already exists.");

		$this
			->foreignKeyValidator
			->validatePatch(
				payload: $testPayload,
				id: 88888,
			);
	}
}
