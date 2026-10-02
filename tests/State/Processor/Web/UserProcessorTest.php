<?php

declare(strict_types=1);

namespace App\Tests\State\Processor\Web;


/// --- Main namespaces --- ///
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Dto\Input\Web\UserCreateDto;
use App\Dto\Input\Web\UserUpdateDto;
use App\Dto\Main\Web\UserResourceDto;
use App\Entity\Web\RoleEntity;
use App\Entity\Web\UserEntity;
use App\Interface\Web\UserDuplicateValidatorInterface;
use App\Interface\Web\UserForeignKeyValidatorInterface;
use App\Repository\Web\RoleRepository;
use App\Repository\Web\UserRepository;
use App\State\Processor\Web\UserProcessor;


/**
 * ---------------------------------------------------------------------------
 *				NOTE ON `#[AllowMockObjectsWithoutExpectations]`
 * ---------------------------------------------------------------------------
 * All processor collaborators are declared once in `setUp()` as mocks so that
 * individual test methods stay short. However, not every test verifies every
 * collaborator. For example, the DELETE tests don't touch `$mapper`,
 * `$requestStack`, `$roleRepository`, `$duplicateValidator`, or
 * `$foreignKeyValidator` at all.
 * PHPUnit 12.5+ emits a notice for each such "mock without expectations" to
 * nudge towards `createStub()`.
 *
 * Suppressing the notice is a deliberate trade-off:
 *   - Keeps the "declare once, use everywhere" style across ~10 test methods.
 *   - Disables PHPUnit's built-in signal that a mock might be an over-mock.
 *
 * When to remove this attribute (and refactor towards a per-test factory):
 *   - When adding a new collaborator whose behaviour varies per test in ways
 *     that would benefit from explicit per-test `createMock()` / `createStub()`
 *     discrimination.
 *   - When upgrading to PHPUnit 14+, where "mock without expectations" becomes
 *     a hard error. At that point, migrate to a private processor building
 *     factory pattern.
 *   - When a test failure points to a collaborator that was silently stubbed
 *     rather than verified — a symptom of over-broad suppression.
 * ---------------------------------------------------------------------------
 */


#[AllowMockObjectsWithoutExpectations]
#[CoversClass(className: UserProcessor::class)]
class UserProcessorTest extends TestCase
{
	private UserRepository&MockObject					$repository;
	private RoleRepository&MockObject					$roleRepository;
	private ObjectMapperInterface&MockObject			$mapper;
	private RequestStack&MockObject						$requestStack;
	private UserDuplicateValidatorInterface&MockObject	$duplicateValidator;
	private UserForeignKeyValidatorInterface&MockObject	$foreignKeyValidator;
	private UserProcessor								$processor;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository			= $this->createMock(type: UserRepository::class);
		$this->roleRepository		= $this->createMock(type: RoleRepository::class);
		$this->mapper				= $this->createMock(type: ObjectMapperInterface::class);
		$this->requestStack			= $this->createMock(type: RequestStack::class);
		$this->duplicateValidator	= $this->createMock(type: UserDuplicateValidatorInterface::class);
		$this->foreignKeyValidator	= $this->createMock(type: UserForeignKeyValidatorInterface::class);

		$this->processor = new UserProcessor(
			repository:			$this->repository,
			roleRepository:		$this->roleRepository,
			mapper:				$this->mapper,
			requestStack:		$this->requestStack,
			duplicateValidator: $this->duplicateValidator,
			foreignKeyValidator: $this->foreignKeyValidator,
		);
	}

	private function stubRawPayload(string $json): void
	{
		$request = new Request(content: $json);

		$this
			->requestStack
			->method('getCurrentRequest')
			->willReturn($request);
	}

	private function mockRoleEntity(
		?int $id = 3,
		?string $name = 'Gambler'
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
	 * fixtures. Therefore, we must be create a valid mock user so that it can be
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
				roleId: $roleId ?? $this->mockRoleEntity(
					id: 1,
					name: 'User',
				)
			)
			->setName(name: $name)
			->setAvatar(avatar: $avatar)
			->setRank(rank: $rank)
			->setCountryFlag(countryFlag: $countryFlag);
	}


	/**
	 * POST requests
	 */


	#[Test]
	public function testValidatePostWhenPersistData(): void
	{
		$roleEntity = $this->mockRoleEntity();

		$dto = new UserCreateDto();

		$dto->id			= 88888;
		$dto->roleId		= 1;
		$dto->name			= 'Gambler';
		$dto->avatar		= 'https://a.ppy.sh/88?88.png';
		$dto->rank			= 88;
		$dto->countryFlag	= 'ZW';

		$testPayload = [
			'id'			=> $dto->id,
			'roleId'		=> $dto->roleId,
			'name'			=> $dto->name,
			'avatar'		=> $dto->avatar,
			'rank'			=> $dto->rank,
			'countryFlag'	=> $dto->countryFlag,
		];

		$this->stubRawPayload(json: json_encode(value: $testPayload));

		// The validator MUST be consulted exactly once with the raw payload
		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePost')
			->with($testPayload);

		// The 1:1 FK validator MUST be consulted exactly once with the raw payload
		$this
			->foreignKeyValidator
			->expects(self::once())
			->method('validatePost')
			->with($testPayload);

		// The FK MUST be resolved against the Role repository before persisting
		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with($dto->roleId)
			->willReturn($roleEntity);

		$this
			->repository
			->expects(self::once())
			->method('save')
			->with(
				self::callback(
					callback: fn(UserEntity $entity)
						=> $entity->getId()				=== $dto->id
						&& $entity->getName()			=== $dto->name
						&& $entity->getAvatar()			=== $dto->avatar
						&& $entity->getRank()			=== $dto->rank
						&& $entity->getCountryFlag()	=== $dto->countryFlag
						&& $entity->getRoleId()			=== $roleEntity
				),
				true
			);

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new UserResourceDto());

		$resource
			= $this
			->processor
			->process(
				data: $dto,
				operation: new Post(),
			);

		self::assertInstanceOf(
			expected: UserResourceDto::class,
			actual: $resource,
		);
	}

	#[Test]
	public function testValidatePostWhenMissingRole(): void
	{
		$dto = new UserCreateDto();

		$dto->id			= 88888;
		$dto->roleId		= 999;
		$dto->name			= 'Gambler';
		$dto->avatar		= 'https://a.ppy.sh/88?88.png';
		$dto->rank			= 88;
		$dto->countryFlag	= 'ZW';

		$testPayload = [
			'name' => $dto->name,
			'roleId' => $dto->roleId
		];

		$this->stubRawPayload(json: json_encode(value: $testPayload));

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with($dto->roleId)
			->willReturn(null);

		// `repository->save` must NEVER be called since the FK is invalid
		$this
			->repository
			->expects(self::never())
			->method('save');

		$this->expectException(exception: BadRequestHttpException::class);
		$this->expectExceptionMessage(message: "Role with ID [{$dto->roleId}] not found.");

		$this
			->processor
			->process(
				data: $dto,
				operation: new Post(),
			);
	}

	#[Test]
	public function testValidatePostWhenNotPersistData(): void
	{
		$dto = new UserCreateDto();

		$dto->id			= 1;
		$dto->name			= 'Admin';
		$dto->avatar		= 'https://a.ppy.sh/1';
		$dto->rank			= 100;
		$dto->countryFlag	= 'VN';
		$dto->roleId		= 3;

		$testPayload = ['name' => $dto->name];

		$this->stubRawPayload(json: json_encode(value: $testPayload));

		$this
			->duplicateValidator
			->method('validatePost')
			->willThrowException(new ConflictHttpException(message: 'duplicate user name.'));

		// `repository->save` must NEVER be called since this's an invalid request
		$this
			->repository
			->expects(self::never())
			->method('save');

		$this->expectException(exception: ConflictHttpException::class);

		$this
			->processor
			->process(
				data: $dto,
				operation: new Post(),
			);
	}


	/**
	 * PATCH requests
	 */


	#[Test]
	public function testValidatePatchWhenMissingEntity(): void
	{
		$dto = new UserUpdateDto();

		$dto->name = 'Gambler';

		$testPayload = ['id' => 88888];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with($testPayload['id'])
			->willReturn(null);

		// Neither the validator nor the mapper should be touched
		$this
			->duplicateValidator
			->expects(self::never())
			->method('validatePatch');

		$this->expectException(exception: NotFoundHttpException::class);
		$this->expectExceptionMessage(message: "User with ID [{$testPayload['id']}] not found.");

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: $testPayload,
			);
	}

	#[Test]
	public function testValidatePatchWhenPassedData(): void
	{
		$testPayload		= ['name' => 'Gambler'];
		$userCurrentData	= $this->mockUserEntity();

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn($userCurrentData);

		$this->stubRawPayload(json: json_encode(value: $testPayload));

		// The validator gets (payload, id) with 'id' being the URL ID
		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$testPayload,
				88888,
			);

		$this
			->repository
			->expects(self::once())
			->method('save');

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new UserResourceDto());

		$dto = new UserUpdateDto();

		$dto->name = $testPayload['name'];

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 88888],
			);

		self::assertSame(
			expected: $testPayload['name'],
			actual: $userCurrentData->getName(),
		);
	}

	#[Test]
	public function testValidatePatchWhenOnlyAvatarData(): void
	{
		$testPayload = ['avatar' => 'https://a.ppy.sh/88?88.png'];
		$userCurrentData = $this->mockUserEntity(name: 'Admin');

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn($userCurrentData);

		// Only provide the optional 'avatar' field
		$this->stubRawPayload(json: json_encode(value: $testPayload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$testPayload,
				88888,
			);

		$this
			->repository
			->expects(self::once())
			->method('save');

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new UserResourceDto());

		$dto = new UserUpdateDto();

		$dto->avatar = $testPayload['avatar'];

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 88888],
			);

		self::assertSame(
			expected: 'Admin',
			actual: $userCurrentData->getName(),
			message: 'User name must NOT change.',
		);
		self::assertSame(
			expected: $testPayload['avatar'],
			actual: $userCurrentData->getAvatar(),
		);
	}

	#[Test]
	public function testValidatePatchWhenRoleData(): void
	{
		$testPayload = ['roleId' => 1];
		$roleEntity = $this->mockRoleEntity();
		$userCurrentData
			= $this->mockUserEntity(roleId: $this->mockRoleEntity(id: 1));

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(1)
			->willReturn($userCurrentData);

		$this->stubRawPayload(json: json_encode(value: $testPayload));

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with($testPayload['roleId'])
			->willReturn($roleEntity);

		// The 1:1 FK validator MUST be consulted exactly once with (payload, URL ID)
		$this
			->foreignKeyValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$testPayload,
				1,
			);

		$this
			->repository
			->expects(self::once())
			->method('save');

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new UserResourceDto());

		$dto = new UserUpdateDto();

		$dto->roleId = $testPayload['roleId'];

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 1],
			);

		self::assertSame(
			expected: $roleEntity,
			actual: $userCurrentData->getRoleId(),
		);
	}

	#[Test]
	public function testValidatePatchWhenSameData(): void
	{
		$testPayload		= ['name' => 'Gambler'];
		$userCurrentData	= $this->mockUserEntity();

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn($userCurrentData);

		$this->stubRawPayload(json: json_encode(value: $testPayload));

		$this
			->duplicateValidator
			->method('validatePatch')
			->willThrowException(new BadRequestHttpException(message: 'duplicate user name.'));

		$this
			->repository
			->expects(self::never())
			->method('save');

		$this->expectException(exception: BadRequestHttpException::class);

		$dto = new UserUpdateDto();

		$dto->name = $testPayload['name'];

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 88888],
			);
	}


	/**
	 * DELETE requests
	 */


	#[Test]
	public function testValidateDeleteWhenRemoveEntity(): void
	{
		$testPayload = ['id' => 88888];
		$userCurrentData = $this->mockUserEntity();

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with($testPayload['id'])
			->willReturn($userCurrentData);

		$this
			->repository
			->expects(self::once())
			->method('remove')
			->with(
				$userCurrentData,
				true
			);

		$result
			= $this
			->processor
			->process(
				data: null,
				operation: new Delete(),
				payload: $testPayload,
			);

		self::assertNull(actual: $result);
	}

	#[Test]
	public function testValidateDeleteWhenMissingEntity(): void
	{
		$testPayload = ['id' => 1];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with($testPayload['id'])
			->willReturn(null);

		$this
			->repository
			->expects(self::never())
			->method('remove');

		$this->expectException(exception: NotFoundHttpException::class);

		$this
			->processor
			->process(
				data: null,
				operation: new Delete(),
				payload: $testPayload,
			);
	}
}
