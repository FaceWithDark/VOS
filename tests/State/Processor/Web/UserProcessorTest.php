<?php

declare(strict_types=1);

namespace App\Tests\State\Processor\Web;


/// --- Main namespaces --- ///
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use InvalidArgumentException;
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
use stdClass;


/// --- Internal namespaces --- ///
use App\Dto\Input\Web\UserCreateDto;
use App\Dto\Input\Web\UserUpdateDto;
use App\Dto\Main\Web\UserResourceDto;
use App\Entity\Web\RoleEntity;
use App\Entity\Web\UserEntity;
use App\Interface\Web\UserDuplicateValidatorInterface;
use App\Interface\Web\UserEmptyPayloadValidatorInterface;
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
 *   - Keeps the "declare once, use everywhere" style across the test methods.
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
	private UserEmptyPayloadValidatorInterface&MockObject	$emptyPayloadValidator;
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
		$this->emptyPayloadValidator	= $this->createMock(type: UserEmptyPayloadValidatorInterface::class);

		$this->processor = new UserProcessor(
			repository:			$this->repository,
			roleRepository:		$this->roleRepository,
			mapper:				$this->mapper,
			requestStack:		$this->requestStack,
			duplicateValidator:	$this->duplicateValidator,
			foreignKeyValidator: $this->foreignKeyValidator,
			emptyPayloadValidator: $this->emptyPayloadValidator,
		);
	}

	private function stubRawPayload(string $json): void
	{
		$this
			->requestStack
			->method('getCurrentRequest')
			->willReturn(new Request(content: $json));
	}

	private function mockRoleEntity(
		?int	$id		= 3,
		?string	$name	= 'Gambler',
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
	 * fixtures. Therefore, we must create a valid user so that it can be
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
	public function testPostPersistsMappedEntity(): void
	{
		$roleEntity = $this->mockRoleEntity();

		$dto = new UserCreateDto();

		$dto->id			= 88888;
		$dto->roleId		= 3;
		$dto->name			= 'Gambler';
		$dto->avatar		= 'https://a.ppy.sh/88?88.png';
		$dto->rank			= 88;
		$dto->countryFlag	= 'ZW';

		$dtoPayload = [
			'id'			=> $dto->id,
			'roleId'		=> $dto->roleId,
			'name'			=> $dto->name,
			'avatar'		=> $dto->avatar,
			'rank'			=> $dto->rank,
			'countryFlag'	=> $dto->countryFlag,
		];

		$resource = new UserResourceDto();

		$this->stubRawPayload(json: json_encode(value: $dtoPayload));

		// Both validators MUST be consulted exactly once with the raw payload
		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePost')
			->with($dtoPayload);

		$this
			->foreignKeyValidator
			->expects(self::once())
			->method('validatePost')
			->with($dtoPayload);

		// The FK MUST be resolved against the Role repository before persisting
		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with($dto->roleId)
			->willReturn($roleEntity);

		// Every DTO field must land on the entity before it is stored
		$this
			->repository
			->expects(self::once())
			->method('save')
			->with(
				self::callback(
					callback: static fn (UserEntity $entity): bool
						=> $entity->getId() === $dto->id
						&& $entity->getName() === $dto->name
						&& $entity->getAvatar() === $dto->avatar
						&& $entity->getRank() === $dto->rank
						&& $entity->getCountryFlag() === $dto->countryFlag
						&& $entity->getRoleId() === $roleEntity
						&& $entity->getCreateOn()?->getTimezone()->getName() === 'UTC'
				),
				true,
			);

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->with(
				self::isInstanceOf(className: UserEntity::class),
				UserResourceDto::class,
			)
			->willReturn($resource);

		$result = $this
			->processor
			->process(
				data: $dto,
				operation: new Post(),
			);

		self::assertSame(
			expected: $resource,
			actual: $result,
		);
	}

	#[Test]
	public function testPostForwardsEmptyPayloadToValidators(): void
	{
		$dto = new UserCreateDto();

		$dto->id			= 88888;
		$dto->roleId		= 3;
		$dto->name			= 'Gambler';
		$dto->avatar		= 'https://a.ppy.sh/88?88.png';
		$dto->rank			= 88;
		$dto->countryFlag	= 'ZW';

		// An empty request body decodes to [], not to a decode error. POST is
		// handled by API Platform's deserialize/validate stage, so the
		// processor simply forwards it.
		$this->stubRawPayload(json: '');

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePost')
			->with([]);

		$this
			->foreignKeyValidator
			->expects(self::once())
			->method('validatePost')
			->with([]);

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->willReturn($this->mockRoleEntity());

		$this
			->repository
			->expects(self::once())
			->method('save');

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new UserResourceDto());

		$this
			->processor
			->process(
				data: $dto,
				operation: new Post(),
			);
	}

	#[Test]
	public function testPostWithoutCurrentRequestForwardsEmptyPayload(): void
	{
		$dto = new UserCreateDto();

		$dto->id			= 88888;
		$dto->roleId		= 3;
		$dto->name			= 'Gambler';
		$dto->avatar		= 'https://a.ppy.sh/88?88.png';
		$dto->rank			= 88;
		$dto->countryFlag	= 'ZW';

		// No stubbed request at all: getDecodedPayload() must fall back to []
		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePost')
			->with([]);

		$this
			->foreignKeyValidator
			->expects(self::once())
			->method('validatePost')
			->with([]);

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->willReturn($this->mockRoleEntity());

		$this
			->repository
			->expects(self::once())
			->method('save');

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new UserResourceDto());

		$this
			->processor
			->process(
				data: $dto,
				operation: new Post(),
			);
	}

	#[Test]
	public function testPostWithDuplicateNameDoesNotPersist(): void
	{
		$dto = new UserCreateDto();

		$dto->id			= 88888;
		$dto->roleId		= 3;
		$dto->name			= 'Gambler';
		$dto->avatar		= 'https://a.ppy.sh/88?88.png';
		$dto->rank			= 88;
		$dto->countryFlag	= 'ZW';

		$this->stubRawPayload(json: json_encode(value: ['name' => $dto->name]));

		$this
			->duplicateValidator
			->method('validatePost')
			->willThrowException(
				new ConflictHttpException(
					message: 'duplicate user name.',
				)
			);

		// An invalid request must never reach the database or the mapper
		$this
			->repository
			->expects(self::never())
			->method('save');

		$this
			->mapper
			->expects(self::never())
			->method('map');

		$this->expectException(exception: ConflictHttpException::class);

		$this
			->processor
			->process(
				data: $dto,
				operation: new Post(),
			);
	}

	#[Test]
	public function testPostWithUnknownRoleThrowsNotFound(): void
	{
		$dto = new UserCreateDto();

		$dto->id			= 88888;
		$dto->roleId		= 999;
		$dto->name			= 'Gambler';
		$dto->avatar		= 'https://a.ppy.sh/88?88.png';
		$dto->rank			= 88;
		$dto->countryFlag	= 'ZW';

		$dtoPayload = [
			'name'		=> $dto->name,
			'roleId'	=> $dto->roleId,
		];

		$this->stubRawPayload(json: json_encode(value: $dtoPayload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePost')
			->with($dtoPayload);

		$this
			->foreignKeyValidator
			->expects(self::once())
			->method('validatePost')
			->with($dtoPayload);

		// The processor also owns the 404 as a defensive fallback
		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with($dto->roleId)
			->willReturn(null);

		$this
			->repository
			->expects(self::never())
			->method('save');

		$this->expectException(exception: NotFoundHttpException::class);
		$this->expectExceptionMessage(message: 'Role with ID [999] not found.');

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
	public function testPatchWithMissingEntityThrowsNotFound(): void
	{
		$dto = new UserUpdateDto();

		$dto->name = 'Gambler';

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn(null);

		// Neither validator nor the mapper should be touched
		$this
			->duplicateValidator
			->expects(self::never())
			->method('validatePatch');

		$this
			->foreignKeyValidator
			->expects(self::never())
			->method('validatePatch');

		$this
			->mapper
			->expects(self::never())
			->method('map');

		$this->expectException(exception: NotFoundHttpException::class);
		$this->expectExceptionMessage(message: 'User with ID [88888] not found.');

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 88888],
			);
	}

	#[Test]
	public function testPatchWithoutPayloadIdFallsBackToZero(): void
	{
		// Locks the `$payload['id'] ?? 0` guard against undefined-key warnings
		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(0)
			->willReturn(null);

		$this->expectException(exception: NotFoundHttpException::class);
		$this->expectExceptionMessage(message: 'User with ID [0] not found.');

		$this
			->processor
			->process(
				data: new UserUpdateDto(),
				operation: new Patch(),
				payload: [],
			);
	}

	#[Test]
	public function testPatchWithEmptyPayloadThrowsBadRequest(): void
	{
		$current = $this->mockUserEntity();

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn($current);

		// An empty request body decodes to []
		$this->stubRawPayload(json: '');

		$this
			->emptyPayloadValidator
			->method('validatePatch')
			->willThrowException(new BadRequestHttpException(message: 'Request payload must not be empty.'));

		// The entity exists, so the empty payload is what fails the request
		$this
			->duplicateValidator
			->expects(self::never())
			->method('validatePatch');

		$this
			->foreignKeyValidator
			->expects(self::never())
			->method('validatePatch');

		$this
			->repository
			->expects(self::never())
			->method('save');

		$this->expectException(exception: BadRequestHttpException::class);
		$this->expectExceptionMessage(message: 'Request payload must not be empty.');

		$this
			->processor
			->process(
				data: new UserUpdateDto(),
				operation: new Patch(),
				payload: ['id' => 88888],
			);
	}

	#[Test]
	public function testPatchWithNameUpdatesOnlyThatField(): void
	{
		$dto = new UserUpdateDto();

		$dto->name = 'DeepInDark';

		$current		= $this->mockUserEntity();
		$previousRole	= $current->getRoleId();
		$resource		= new UserResourceDto();
		$payload		= ['name' => $dto->name];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		// The empty-payload guard is consulted before the duplicate lookup
		$this
			->emptyPayloadValidator
			->expects(self::once())
			->method('validatePatch')
			->with($payload);

		// The validators get (decoded payload, URL ID)
		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88888,
			);

		$this
			->foreignKeyValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88888,
			);

		$this
			->repository
			->expects(self::once())
			->method('save')
			->with(
				$current,
				true,
			);

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn($resource);

		$result = $this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 88888],
			);

		self::assertSame(
			expected: $resource,
			actual: $result,
		);
		self::assertSame(
			expected: 'DeepInDark',
			actual: $current->getName(),
		);
		self::assertSame(
			expected: $previousRole,
			actual: $current->getRoleId(),
			message: 'A name-only PATCH must not touch the role relation.',
		);
	}

	#[Test]
	public function testPatchWithOnlyAvatarKeepsOtherFields(): void
	{
		$dto = new UserUpdateDto();

		$dto->avatar = 'https://a.ppy.sh/19817503?1752731877.png';

		$current = $this->mockUserEntity(
			name: 'DeepInDark',
			rank: 5103,
			countryFlag: 'VN'
		);
		$payload = ['avatar' => $dto->avatar];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88888,
			);

		$this
			->foreignKeyValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88888,
			);

		$this
			->repository
			->expects(self::once())
			->method('save')
			->with(
				$current,
				true,
			);

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new UserResourceDto());

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 88888],
			);

		self::assertSame(
			expected: $dto->avatar,
			actual: $current->getAvatar(),
		);
		self::assertSame(
			expected: 'DeepInDark',
			actual: $current->getName(),
		);
		self::assertSame(
			expected: 5103,
		   	actual: $current->getRank(),
		);
		self::assertSame(
			expected: 'VN',
			actual: $current->getCountryFlag(),
		);
	}

	#[Test]
	public function testPatchWithRoleIdResolvesNewRole(): void
	{
		$dto = new UserUpdateDto();

		$dto->roleId = 3;

		$current = $this->mockUserEntity();
		$newRole = $this->mockRoleEntity(
			id: 3,
			name: 'Gambler',
		);
		$payload = ['roleId' => $dto->roleId];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88888,
			);

		$this
			->foreignKeyValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88888,
			);

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with(3)
			->willReturn($newRole);

		$this
			->repository
			->expects(self::once())
			->method('save')
			->with(
				$current,
				true,
			);

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new UserResourceDto());

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 88888],
			);

		self::assertSame(
			expected: $newRole,
			actual: $current->getRoleId(),
		);
	}

	#[Test]
	public function testPatchWithNullRoleIdThrowsBadRequest(): void
	{
		$dto = new UserUpdateDto();

		$dto->roleId = null;

		$current	= $this->mockUserEntity();
		$payload	= ['roleId' => null];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88888,
			);

		$this
			->foreignKeyValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88888,
			);

		// An explicit NULL cannot be resolved, so no Role lookup happens
		$this
			->roleRepository
			->expects(self::never())
			->method('find');

		$this
			->repository
			->expects(self::never())
			->method('save');

		$this->expectException(exception: BadRequestHttpException::class);
		$this->expectExceptionMessage(message: 'Role ID must not be null.');

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 88888],
			);
	}

	#[Test]
	public function testPatchWithUnknownRoleThrowsNotFound(): void
	{
		$dto = new UserUpdateDto();

		$dto->roleId = 999;

		$current	= $this->mockUserEntity();
		$payload	= ['roleId' => $dto->roleId];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88888,
			);

		$this
			->foreignKeyValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88888,
			);

		$this
			->roleRepository
			->expects(self::once())
			->method('find')
			->with(999)
			->willReturn(null);

		$this
			->repository
			->expects(self::never())
			->method('save');

		$this->expectException(exception: NotFoundHttpException::class);
		$this->expectExceptionMessage(message: 'Role with ID [999] not found.');

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 88888],
			);
	}

	#[Test]
	public function testPatchWithRankAndCountryFlagUpdatesThem(): void
	{
		$dto = new UserUpdateDto();

		$dto->rank			= 5103;
		$dto->countryFlag	= 'VN';

		$current	= $this->mockUserEntity(name: 'DeepInDark');
		$payload	= [
			'rank'			=> $dto->rank,
			'countryFlag'	=> $dto->countryFlag,
		];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88888,
			);

		$this
			->foreignKeyValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88888,
			);

		$this
			->repository
			->expects(self::once())
			->method('save')
			->with(
				$current,
				true,
			);

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new UserResourceDto());

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 88888],
			);

		self::assertSame(
			expected: 5103,
		   	actual: $current->getRank(),);
		self::assertSame(
			expected: 'VN',
			actual: $current->getCountryFlag(),
		);
		self::assertSame(
			expected: 'DeepInDark',
			actual: $current->getName(),
		);
	}

	#[Test]
	public function testPatchWithDuplicateNameDoesNotPersist(): void
	{
		$dto = new UserUpdateDto();

		$dto->name = 'Gambler';

		$current	= $this->mockUserEntity(name: 'DeepInDark');
		$payload	= ['name' => $dto->name];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this
			->duplicateValidator
			->method('validatePatch')
			->willThrowException(
				new BadRequestHttpException(
					message: 'duplicate user name.',
				),
			);

		$this
			->repository
			->expects(self::never())
			->method('save');

		$this
			->mapper
			->expects(self::never())
			->method('map');

		$this->expectException(exception: BadRequestHttpException::class);

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
	public function testDeleteRemovesEntityAndReturnsNull(): void
	{
		$current = $this->mockUserEntity();

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn($current);

		$this
			->repository
			->expects(self::once())
			->method('remove')
			->with(
				$current,
				true,
			);

		$result = $this
			->processor
			->process(
				data: null,
				operation: new Delete(),
				payload: ['id' => 88888],
			);

		self::assertNull(actual: $result);
	}

	#[Test]
	public function testDeleteWithMissingEntityThrowsNotFound(): void
	{
		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn(null);

		$this
			->repository
			->expects(self::never())
			->method('remove');

		$this->expectException(exception: NotFoundHttpException::class);
		$this->expectExceptionMessage(message: 'User with ID [88888] not found.');

		$this
			->processor
			->process(
				data: null,
				operation: new Delete(),
				payload: ['id' => 88888],
			);
	}

	#[Test]
	public function testDeleteWithoutPayloadIdFallsBackToZero(): void
	{
		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(0)
			->willReturn(null);

		$this->expectException(exception: NotFoundHttpException::class);
		$this->expectExceptionMessage(message: 'User with ID [0] not found.');

		$this
			->processor
			->process(
				data: null,
				operation: new Delete(),
				payload: [],
			);
	}


	/**
	 * Unsupported input
	 */


	#[Test]
	public function testProcessRejectsUnsupportedInput(): void
	{
		$this->expectException(exception: InvalidArgumentException::class);
		$this->expectExceptionMessage(message: 'Unsupported opearation or input DTO type.');

		$this
			->processor
			->process(
				data: new stdClass(),
				operation: new Post(),
			);
	}
}
