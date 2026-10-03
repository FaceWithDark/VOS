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
use App\Dto\Input\Web\RoleCreateDto;
use App\Dto\Input\Web\RoleUpdateDto;
use App\Dto\Main\Web\RoleResourceDto;
use App\Entity\Web\RoleEntity;
use App\Interface\Web\RoleDuplicateValidatorInterface;
use App\Interface\Web\RoleEmptyPayloadValidatorInterface;
use App\Repository\Web\RoleRepository;
use App\State\Processor\Web\RoleProcessor;


/**
 * ---------------------------------------------------------------------------
 *				NOTE ON `#[AllowMockObjectsWithoutExpectations]`
 * ---------------------------------------------------------------------------
 * All processor collaborators are declared once in `setUp()` as mocks so that
 * individual test methods stay short. However, not every test verifies every
 * collaborator. For example, the DELETE tests don't touch `$mapper` or
 * `$duplicateValidator` at all. PHPUnit 12.5+ emits a notice for each such
 * "mock without expectations" to nudge towards `createStub()`.
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
#[CoversClass(className: RoleProcessor::class)]
class RoleProcessorTest extends TestCase
{
	private RoleRepository&MockObject					$repository;
	private ObjectMapperInterface&MockObject			$mapper;
	private RequestStack&MockObject						$requestStack;
	private RoleDuplicateValidatorInterface&MockObject	$duplicateValidator;
	private RoleEmptyPayloadValidatorInterface&MockObject	$emptyPayloadValidator;
	private RoleProcessor								$processor;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository				= $this->createMock(type: RoleRepository::class);
		$this->mapper					= $this->createMock(type: ObjectMapperInterface::class);
		$this->requestStack				= $this->createMock(type: RequestStack::class);
		$this->duplicateValidator		= $this->createMock(type: RoleDuplicateValidatorInterface::class);
		$this->emptyPayloadValidator	= $this->createMock(type: RoleEmptyPayloadValidatorInterface::class);

		$this->processor = new RoleProcessor(
			repository:				$this->repository,
			mapper:					$this->mapper,
			requestStack:			$this->requestStack,
			duplicateValidator:		$this->duplicateValidator,
			emptyPayloadValidator:	$this->emptyPayloadValidator,
		);
	}

	private function stubRawPayload(string $json): void
	{
		$this
			->requestStack
			->method('getCurrentRequest')
			->willReturn(new Request(content: $json));
	}

	private function mockRoleEntity(): RoleEntity
	{
		return (new RoleEntity())
			->setId(id: 2)
			->setName(name: 'Admin')
			->setDescription(description: 'can take control of the whole website both internally and externally.');
	}


	/**
	 * POST requests
	 */


	#[Test]
	public function testPostPersistsMappedEntity(): void
	{
		$dto = new RoleCreateDto();

		$dto->name			= 'Gambler';
		$dto->description	= 'double the pay, double the deal baby.';

		$dtoPayload = [
			'name'			=> $dto->name,
			'description'	=> $dto->description,
		];

		$resource = new RoleResourceDto();

		$this->stubRawPayload(json: json_encode(value: $dtoPayload));

		// The validator MUST be consulted exactly once with the raw payload
		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePost')
			->with($dtoPayload);

		// Every DTO field must land on the entity before it is stored
		$this
			->repository
			->expects(self::once())
			->method('save')
			->with(
				self::callback(
					callback: static fn (RoleEntity $entity): bool
						=> $entity->getName() === $dto->name
						&& $entity->getDescription() === $dto->description
						&& $entity->getCreateOn()?->getTimezone()->getName() === 'UTC'
				),
				true,
			);

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->with(
				self::isInstanceOf(className: RoleEntity::class),
				RoleResourceDto::class,
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
	public function testPostForwardsEmptyPayloadToValidator(): void
	{
		$dto = new RoleCreateDto();

		$dto->name = 'Gambler';

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
			->repository
			->expects(self::once())
			->method('save');

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new RoleResourceDto());

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
		$dto = new RoleCreateDto();

		$dto->name = 'Gambler';

		// No stubbed request at all: getDecodedPayload() must fall back to []
		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePost')
			->with([]);

		$this
			->repository
			->expects(self::once())
			->method('save');

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new RoleResourceDto());

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
		$dto = new RoleCreateDto();

		$dto->name = 'Admin';

		$this->stubRawPayload(json: json_encode(value: ['name' => $dto->name]));

		$this
			->duplicateValidator
			->method('validatePost')
			->willThrowException(
				new ConflictHttpException(
					message: 'duplicate role name.',
				),
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


	/**
	 * PATCH requests
	 */


	#[Test]
	public function testPatchWithMissingEntityThrowsNotFound(): void
	{
		$dto = new RoleUpdateDto();

		$dto->name = 'Gambler';

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(3)
			->willReturn(null);

		// Neither the validator nor the mapper should be touched
		$this
			->duplicateValidator
			->expects(self::never())
			->method('validatePatch');

		$this
			->mapper
			->expects(self::never())
			->method('map');

		$this->expectException(exception: NotFoundHttpException::class);
		$this->expectExceptionMessage(message: 'Role with ID [3] not found.');

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 3],
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
		$this->expectExceptionMessage(message: 'Role with ID [0] not found.');

		$this
			->processor
			->process(
				data: new RoleUpdateDto(),
				operation: new Patch(),
				payload: [],
			);
	}

	#[Test]
	public function testPatchWithEmptyPayloadThrowsBadRequest(): void
	{
		$current = $this->mockRoleEntity();

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(2)
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
			->repository
			->expects(self::never())
			->method('save');

		$this->expectException(exception: BadRequestHttpException::class);
		$this->expectExceptionMessage(message: 'Request payload must not be empty.');

		$this
			->processor
			->process(
				data: new RoleUpdateDto(),
				operation: new Patch(),
				payload: ['id' => 2],
			);
	}

	#[Test]
	public function testPatchWithNameUpdatesOnlyThatField(): void
	{
		$dto = new RoleUpdateDto();

		$dto->name = 'Gambler';

		$current	= $this->mockRoleEntity();
		$resource	= new RoleResourceDto();
		$payload	= ['name' => $dto->name];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(2)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		// The empty-payload guard is consulted before the duplicate lookup
		$this
			->emptyPayloadValidator
			->expects(self::once())
			->method('validatePatch')
			->with($payload);

		// The validator gets (decoded payload, URL ID)
		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				2,
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
				payload: ['id' => 2],
			);

		self::assertSame(
			expected: $resource,
			actual: $result,
		);
		self::assertSame(
			expected: 'Gambler',
			actual: $current->getName(),
		);
		self::assertSame(
			expected: 'can take control of the whole website both internally and externally.',
			actual: $current->getDescription(),
			message: 'An omitted description must survive a name-only PATCH.',
		);
	}

	#[Test]
	public function testPatchWithDescriptionOnlyKeepsName(): void
	{
		$dto = new RoleUpdateDto();

		$dto->description = 'double the pay, double the deal baby.';

		$current	= $this->mockRoleEntity();
		$payload	= ['description' => $dto->description];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(2)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				2,
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
			->willReturn(new RoleResourceDto());

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 2],
			);

		self::assertSame(
			expected: 'Admin',
			actual: $current->getName(),
		);
		self::assertSame(
			expected: $dto->description,
			actual: $current->getDescription(),
		);
	}

	#[Test]
	public function testPatchWithNullDescriptionClearsIt(): void
	{
		$dto = new RoleUpdateDto();

		$dto->description = null;

		$current	= $this->mockRoleEntity();
		$payload	= ['description' => null];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(2)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				2,
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
			->willReturn(new RoleResourceDto());

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 2],
			);

		self::assertNull(actual: $current->getDescription());
	}

	#[Test]
	public function testPatchWithDuplicateNameDoesNotPersist(): void
	{
		$dto = new RoleUpdateDto();

		$dto->name = 'User';

		$current	= $this->mockRoleEntity();
		$payload	= ['name' => $dto->name];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(2)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this
			->duplicateValidator
			->method('validatePatch')
			->willThrowException(
				new BadRequestHttpException(
					message: 'duplicate role name.',
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
				payload: ['id' => 2],
			);
	}


	/**
	 * DELETE requests
	 */


	#[Test]
	public function testDeleteRemovesEntityAndReturnsNull(): void
	{
		$current = $this->mockRoleEntity();

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(2)
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
				payload: ['id' => 2],
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
			->with(3)
			->willReturn(null);

		$this
			->repository
			->expects(self::never())
			->method('remove');

		$this->expectException(exception: NotFoundHttpException::class);
		$this->expectExceptionMessage(message: 'Role with ID [3] not found.');

		$this
			->processor
			->process(
				data: null,
				operation: new Delete(),
				payload: ['id' => 3],
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
		$this->expectExceptionMessage(message: 'Role with ID [0] not found.');

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
