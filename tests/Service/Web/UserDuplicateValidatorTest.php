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


#[CoversClass(className: UserDuplicateValidator::class)]
class UserDuplicateValidatorTest extends TestCase
{
	private UserRepository&MockObject	$repository;
	private UserDuplicateValidator		$duplcateValidator;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository			= $this->createMock(type: UserRepository::class);
		$this->duplcateValidator	= new UserDuplicateValidator(repository: $this->repository);
	}

	/**
	 * NOTE:
	 *
	 * Unlike {@see RoleEntity}, {@see UserEntity} ships with no default data
	 * fixtures. Therefore, we must be create a valid mock user so that it can be
	 * adjusted to the specific scenario under each test.
	 */
	private function mockUserEntity(
		?int	$id				= 88888,
		?string	$name			= 'Gambler',
		?string	$avatar			= 'https://a.ppy.sh/88?88.png',
		?int	$rank			= 88,
		?string $countryFlag	= 'ZW',
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
			->setAvatar(avatar: $avatar)
			->setRank(rank: $rank)
			->setCountryFlag(countryFlag: $countryFlag);
	}


	/**
	 * validatePost() - 409 Conflicts
	 */


	#[Test]
	public function testMissingNameFieldOnPost(): void
	{
		$testPayload = ['avatar' => 'https://a.ppy.sh/88?88.png'];

		$this
			->repository
			->expects(self::never())
			->method('findOneBy');

		$this
			->duplcateValidator
			->validatePost(payload: $testPayload);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testNullNameFieldOnPost(): void
	{
		$testPayload = ['name' => null];

		$this
			->repository
			->expects(self::never())
			->method('findOneBy');

		$this
			->duplcateValidator
			->validatePost(payload: $testPayload);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testPassedNameFieldOnPost(): void
	{
		$testPayload = ['name' => 'Gambler'];

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn(null);

		$this
			->duplcateValidator
			->validatePost(payload: $testPayload);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testMatchingNameFieldOnPost(): void
	{
		$testPayload		= ['name' => 'Gambler'];
		$userCurrentData	= $this->mockUserEntity();

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn($userCurrentData);

		$this->expectException(exception: ConflictHttpException::class);
		$this->expectExceptionMessage(message: "A user with the name [{$testPayload['name']}] already exists.");

		$this
			->duplcateValidator
			->validatePost(payload: $testPayload);
	}

	#[Test]
	public function testOptionalAvatarFieldOnPost(): void
	{
		$testPayload = ['name' => 'Gambler'];

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn(null);

		$this
			->duplcateValidator
			->validatePost(
				payload: array_merge(
					$testPayload,
					['avatar' => 'https://a.ppy.sh/88?88.png'],
				),
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}


	/**
	 * validatePatch() - 400 Bad Request
	 */


	#[Test]
	public function testMissingNameFieldOnPatch(): void
	{
		$testPayload = ['avatar' => 'https://a.ppy.sh/88?88.png'];

		$this
			->repository
			->expects(self::never())
			->method('findOneBy');

		$this
			->duplcateValidator
			->validatePatch(
				payload: $testPayload,
				id: 88888,
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testNullNameFieldOnPatch(): void
	{
		$testPayload = ['name' => null];

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn(null);

		$this
			->duplcateValidator
			->validatePatch(
				payload: $testPayload,
				id: 88888,
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testPassedNameFieldOnPatch(): void
	{
		$testPayload = ['name' => 'Gambler'];

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn(null);

		$this
			->duplcateValidator
			->validatePatch(
				payload: $testPayload,
				id: 88888,
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testSameEntityMatchingNameFieldOnPatch(): void
	{
		$testPayload		= ['name' => 'Gambler'];
		$userCurrentData	= $this->mockUserEntity();

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn($userCurrentData);

		$this
			->duplcateValidator
			->validatePatch(
				payload: $testPayload,
				id: 88888,
			);

		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testDifferentEntityMatchingNameFieldOnPatch(): void
	{
		$testPayload = ['name' => 'Gambler'];
		$userCurrentData
			= $this->mockUserEntity(
				id:				19817503,
				name:			'DeepInDark',
				avatar:			'https://a.ppy.sh/19817503?1752731877.png',
				rank:			5103,
				countryFlag:	'VN',
			);

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn($userCurrentData);

		$this->expectException(exception: BadRequestHttpException::class);
		$this->expectExceptionMessage(message: "Another user with the name [{$testPayload['name']}] already exists.");

		$this
			->duplcateValidator
			->validatePatch(
				payload: $testPayload,
				id: 88888,
			);
	}

	#[Test]
	public function testOptionalAvatarFieldOnPatch(): void
	{
		$testPayload = ['name' => 'Gambler'];

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn(null);

		$this
			->duplcateValidator
			->validatePatch(
				payload: array_merge(
					$testPayload,
					['avatar' => 'https://a.ppy.sh/88?88.png'],
				),
				id: 88888,
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}
}
