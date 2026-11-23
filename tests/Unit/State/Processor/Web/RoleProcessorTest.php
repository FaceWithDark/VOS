<?php

declare(strict_types=1);

namespace App\Tests\Unit\State\Processor\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


/// --- Type hint namespaces --- ///
use function json_encode;
use InvalidArgumentException;
use Override;
use RuntimeException;
use stdClass;


/// --- Internal namespaces --- ///
use App\Dto\Input\Web\RoleCreateDto;
use App\Dto\Input\Web\RoleUpdateDto;
use App\Dto\Main\Web\RoleResourceDto;
use App\Entity\Abstract\RoleAbstract;
use App\Entity\Web\RoleEntity;
use App\Interface\Web\RoleDuplicateValidatorInterface;
use App\Interface\Web\RoleEmptyPayloadValidatorInterface;
use App\Repository\Web\RoleRepository;
use App\Service\Web\RolePayloadHandler;
use App\State\Processor\Web\RoleProcessor;


// @mago-format-ignore-end
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
#[CoversClass(RoleProcessor::class)]
#[UsesClass(RoleEntity::class)]
#[UsesClass(RoleAbstract::class)]
#[UsesClass(RolePayloadHandler::class)]
class RoleProcessorTest extends TestCase
{
	/** Repository. */
	protected RoleRepository&MockObject $repository;

	/** Mapper. */
	protected ObjectMapperInterface&MockObject $mapper;

	/** Request stack. */
	protected RequestStack&MockObject $requestStack;

	/** Duplicate validator. */
	protected RoleDuplicateValidatorInterface&MockObject $duplicateValidator;

	/** Empty payload validator. */
	protected RoleEmptyPayloadValidatorInterface&MockObject $emptyPayloadValidator;

	/** Payload handler. */
	protected RolePayloadHandler $handler;

	/** Processor. */
	protected RoleProcessor $processor;

	/**
	 * Covers: test delete removes entity and returns null.
	 * @mago-expect lint:literal-named-argument(3)
	 */
	#[Test]
	public function testDeleteRemovesEntityAndReturnsNull(): void
	{
		$current = $this->mockRoleEntity();

		$this->repository->expects(self::once())->method('find')->with(2)->willReturn($current);

		$this->repository->expects(self::once())->method('remove')->with($current);

		$this->sealMockObjects();

		$result = $this->processor->process(
			data: null,
			operation: new Delete(),
			payload: ['id' => 2],
		);

		self::assertNull($result);
	}

	/**
	 * Covers: test delete with missing entity throws not found.
	 * @mago-expect lint:literal-named-argument(5)
	 */
	#[Test]
	public function testDeleteWithMissingEntityThrowsNotFound(): void
	{
		$this->repository->expects(self::once())->method('find')->with(3)->willReturn(null);

		$this->repository->expects(self::never())->method('remove');

		$this->expectException(NotFoundHttpException::class);

		$this->expectExceptionMessageIs('Role with ID [3] not found.');

		$this->sealMockObjects();

		$this->processor->process(
			data: null,
			operation: new Delete(),
			payload: ['id' => 3],
		);
	}

	/**
	 * Covers: test delete without payload id falls back to zero.
	 * @mago-expect lint:literal-named-argument(4)
	 */
	#[Test]
	public function testDeleteWithoutPayloadIdFallsBackToZero(): void
	{
		$this->repository->expects(self::once())->method('find')->with(0)->willReturn(null);

		$this->expectException(NotFoundHttpException::class);

		$this->expectExceptionMessageIs('Role with ID [0] not found.');

		$this->sealMockObjects();

		$this->processor->process(
			data: null,
			operation: new Delete(),
			payload: [],
		);
	}

	/**
	 * Covers: test patch with description only keeps name.
	 * @mago-expect lint:literal-named-argument(7)
	 */
	#[Test]
	public function testPatchWithDescriptionOnlyKeepsName(): void
	{
		$dto = new RoleUpdateDto();

		$dto->description = 'double the pay, double the deal baby.';

		$current = $this->mockRoleEntity();

		$payload = ['description' => $dto->description];

		$this->repository->expects(self::once())->method('find')->with(2)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				2,
			);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn(new RoleResourceDto());

		$this->sealMockObjects();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 2],
		);

		self::assertSame(
			'Admin',
			$current->getName(),
		);

		self::assertSame(
			$dto->description,
			$current->getDescription(),
		);
	}

	/**
	 * Covers: test patch with duplicate name does not persist.
	 * @mago-expect lint:literal-named-argument(5)
	 */
	#[Test]
	public function testPatchWithDuplicateNameDoesNotPersist(): void
	{
		$dto = new RoleUpdateDto();

		$dto->name = 'User';

		$current = $this->mockRoleEntity();

		$payload = ['name' => $dto->name];

		$this->repository->expects(self::once())->method('find')->with(2)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->duplicateValidator
			->method('validatePatch')
			->willThrowException(
				new BadRequestHttpException(
					message: 'duplicate role name.',
				),
			);

		$this->repository->expects(self::never())->method('save');

		$this->mapper->expects(self::never())->method('map');

		$this->expectException(BadRequestHttpException::class);

		$this->sealMockObjects();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 2],
		);
	}

	/**
	 * Covers: test patch with empty payload throws bad request.
	 * @mago-expect lint:literal-named-argument(6)
	 */
	#[Test]
	public function testPatchWithEmptyPayloadThrowsBadRequest(): void
	{
		$current = $this->mockRoleEntity();

		$this->repository->expects(self::once())->method('find')->with(2)->willReturn($current);

		// An empty request body decodes to []
		$this->stubRawPayload(json: '');

		$this->emptyPayloadValidator
			->method('validatePatch')
			->willThrowException(new BadRequestHttpException(message: 'Request payload must not be empty.'));

		// The entity exists, so the empty payload is what fails the request
		$this->duplicateValidator->expects(self::never())->method('validatePatch');

		$this->repository->expects(self::never())->method('save');

		$this->expectException(BadRequestHttpException::class);

		$this->expectExceptionMessageIs('Request payload must not be empty.');

		$this->sealMockObjects();

		$this->processor->process(
			data: new RoleUpdateDto(),
			operation: new Patch(),
			/** Covers: test patch with missing entity throws not found. */
			payload: ['id' => 2],
		);
	}

	/**
	 * Covers: test patch with missing entity throws not found.
	 * @mago-expect lint:literal-named-argument(6)
	 */
	#[Test]
	public function testPatchWithMissingEntityThrowsNotFound(): void
	{
		$dto = new RoleUpdateDto();

		$dto->name = 'Gambler';

		$this->repository->expects(self::once())->method('find')->with(3)->willReturn(null);

		// Neither the validator nor the mapper should be touched
		$this->duplicateValidator->expects(self::never())->method('validatePatch');

		$this->mapper->expects(self::never())->method('map');

		$this->expectException(NotFoundHttpException::class);

		$this->expectExceptionMessageIs('Role with ID [3] not found.');

		$this->sealMockObjects();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 3],
		);
	}

	/**
	 * Covers: test patch with name and description updates both fields.
	 * @mago-expect lint:literal-named-argument(8)
	 */
	#[Test]
	public function testPatchWithNameAndDescriptionUpdatesBothFields(): void
	{
		$dto = new RoleUpdateDto();

		$dto->name = 'Gambler';

		$dto->description = 'double the pay, double the deal baby.';

		$current = $this->mockRoleEntity();

		$payload = [
			'name'        => $dto->name,
			'description' => $dto->description,
		];

		$this->repository->expects(self::once())->method('find')->with(2)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		// Both fields are present, so both must be applied
		$this->emptyPayloadValidator->expects(self::once())->method('validatePatch')->with($payload);

		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				2,
			);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn(new RoleResourceDto());

		$this->sealMockObjects();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 2],
		);

		self::assertSame(
			'Gambler',
			$current->getName(),
		);

		self::assertSame(
			$dto->description,
			$current->getDescription(),
		);
	}

	/**
	 * Covers: test patch with name updates only that field.
	 * @mago-expect lint:literal-named-argument(10)
	 */
	#[Test]
	public function testPatchWithNameUpdatesOnlyThatField(): void
	{
		$dto = new RoleUpdateDto();

		$dto->name = 'Gambler';

		$current = $this->mockRoleEntity();

		$resource = new RoleResourceDto();

		$payload = ['name' => $dto->name];

		$this->repository->expects(self::once())->method('find')->with(2)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		// The empty-payload guard is consulted before the duplicate lookup
		$this->emptyPayloadValidator->expects(self::once())->method('validatePatch')->with($payload);

		// The validator gets (decoded payload, URL ID)
		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				2,
			);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn($resource);

		$this->sealMockObjects();

		$result = $this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 2],
		);

		self::assertSame(
			$resource,
			$result,
		);

		self::assertSame(
			'Gambler',
			$current->getName(),
		);

		/** Covers: test patch with null description clears it. */
		self::assertSame(
			'can take control of the whole website both internally and externally.',
			$current->getDescription(),
			'An omitted description must survive a name-only PATCH.',
		);
	}

	/**
	 * Covers: test patch with null description clears it.
	 * @mago-expect lint:literal-named-argument(6)
	 */
	#[Test]
	public function testPatchWithNullDescriptionClearsIt(): void
	{
		$dto = new RoleUpdateDto();

		$dto->description = null;

		$current = $this->mockRoleEntity();

		$payload = ['description' => null];

		$this->repository->expects(self::once())->method('find')->with(2)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				2,
			);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn(new RoleResourceDto());

		$this->sealMockObjects();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 2],
		);

		self::assertNull($current->getDescription());
	}

	/**
	 * Covers: test patch without payload id falls back to zero.
	 * @mago-expect lint:literal-named-argument(4)
	 */
	#[Test]
	public function testPatchWithoutPayloadIdFallsBackToZero(): void
	{
		// Locks the `$payload['id'] ?? 0` guard against undefined-key warnings
		$this->repository->expects(self::once())->method('find')->with(0)->willReturn(null);

		$this->expectException(NotFoundHttpException::class);

		$this->expectExceptionMessageIs('Role with ID [0] not found.');

		$this->sealMockObjects();

		$this->processor->process(
			data: new RoleUpdateDto(),
			operation: new Patch(),
			payload: [],
		);
	}

	/**
	 * Covers: test patch with unknown field leaves entity unchanged.
	 * @mago-expect lint:literal-named-argument(9)
	 */
	#[Test]
	public function testPatchWithUnknownFieldLeavesEntityUnchanged(): void
	{
		$dto = new RoleUpdateDto();

		$payload = ['rank' => 0];

		$current = $this->mockRoleEntity();

		$this->repository->expects(self::once())->method('find')->with(2)->willReturn($current);

		// A non-empty body without 'name'/'description' passes both guards
		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->emptyPayloadValidator->expects(self::once())->method('validatePatch')->with($payload);

		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				2,
			);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn(new RoleResourceDto());

		$this->sealMockObjects();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 2],
		);

		// Neither branch applied, so the stored entity keeps its values
		self::assertSame(
			'Admin',
			$current->getName(),
		);

		self::assertSame(
			'can take control of the whole website both internally and externally.',
			$current->getDescription(),
		);
	}

	/**
	 * Covers: test post forwards empty payload to validator.
	 * @mago-expect lint:literal-named-argument(3)
	 */
	#[Test]
	public function testPostForwardsEmptyPayloadToValidator(): void
	{
		$dto = new RoleCreateDto();

		$dto->name = 'Gambler';

		// An empty request body decodes to [], not to a decode error. POST is
		// handled by API Platform's deserialize/validate stage, so the
		// processor simply forwards it.
		$this->stubRawPayload(json: '');

		$this->duplicateValidator->expects(self::once())->method('validatePost')->with([]);

		$this->repository->expects(self::once())->method('save');

		$this->mapper->expects(self::once())->method('map')->willReturn(new RoleResourceDto());

		$this->sealMockObjects();

		$this->processor->process(
			data: $dto,
			operation: new Post(),
		);
	}

	/**
	 * POST requests
	 */
	/**
	 * PATCH requests
	 */
	/**
	 * DELETE requests
	 */
	/**
	 * Unsupported input
	 */

	/**
	 * Covers: test post persists mapped entity.
	 * @mago-expect lint:literal-named-argument(3)
	 */
	#[Test]
	public function testPostPersistsMappedEntity(): void
	{
		$dto = new RoleCreateDto();

		$dto->name = 'Gambler';

		$dto->description = 'double the pay, double the deal baby.';

		$dtoPayload = [
			'name'        => $dto->name,
			'description' => $dto->description,
		];

		$resource = new RoleResourceDto();

		$this->stubRawPayload(json: json_encode(value: $dtoPayload));

		// The validator MUST be consulted exactly once with the raw payload
		$this->duplicateValidator->expects(self::once())->method('validatePost')->with($dtoPayload);

		// Every DTO field must land on the entity before it is stored
		$this->repository
			->expects(self::once())
			->method('save')
			->with(self::callback(
				static fn(RoleEntity $entity): bool => (
					$entity->getName() === $dto->name
					&& $entity->getDescription() === $dto->description
					&& $entity->getCreateOn()?->getTimezone()->getName() === 'UTC'
				),
			));

		$this->mapper
			->expects(self::once())
			->method('map')
			->with(
				self::isInstanceOf(RoleEntity::class),
				RoleResourceDto::class,
			)
			->willReturn($resource);

		$this->sealMockObjects();

		$result = $this->processor->process(
			data: $dto,
			operation: new Post(),
		);

		self::assertSame(
			$resource,
			$result,
		);
	}

	/**
	 * Covers: test post with duplicate name does not persist.
	 * @mago-expect lint:literal-named-argument(3)
	 */
	#[Test]
	public function testPostWithDuplicateNameDoesNotPersist(): void
	{
		$dto = new RoleCreateDto();

		$dto->name = 'Admin';

		$this->stubRawPayload(json: json_encode(value: ['name' => $dto->name]));

		$this->duplicateValidator
			->method('validatePost')
			->willThrowException(
				new ConflictHttpException(
					message: 'duplicate role name.',
				),
			);

		// An invalid request must never reach the database or the mapper
		$this->repository->expects(self::never())->method('save');

		$this->mapper->expects(self::never())->method('map');

		$this->expectException(ConflictHttpException::class);

		$this->sealMockObjects();

		$this->processor->process(
			data: $dto,
			operation: new Post(),
		);
	}

	/**
	 * Covers: test post without current request forwards empty payload.
	 * @mago-expect lint:literal-named-argument(3)
	 */
	#[Test]
	public function testPostWithoutCurrentRequestForwardsEmptyPayload(): void
	{
		$dto = new RoleCreateDto();

		$dto->name = 'Gambler';

		// No stubbed request at all: getDecodedPayload() must fall back to []
		$this->duplicateValidator->expects(self::once())->method('validatePost')->with([]);

		$this->repository->expects(self::once())->method('save');

		$this->mapper->expects(self::once())->method('map')->willReturn(new RoleResourceDto());

		$this->sealMockObjects();

		$this->processor->process(
			data: $dto,
			operation: new Post(),
		);
	}

	/**
	 * Covers: test process rejects unsupported input.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testProcessRejectsUnsupportedInput(): void
	{
		$this->expectException(InvalidArgumentException::class);

		$this->expectExceptionMessageIs('Unsupported opearation or input DTO type.');

		$this->sealMockObjects();

		$this->processor->process(
			data: new stdClass(),
			operation: new Post(),
		);
	}

	/** Covers: mock role entity. */
	protected function mockRoleEntity(): RoleEntity
	{
		return new RoleEntity()
			->setId(id: 2)
			->setName(name: 'Admin')
			->setDescription(description: 'can take control of the whole website both internally and externally.');
	}

	/**
	 * Seals every collaborator once all of a test's expectations are declared,
	 * so any undeclared interaction fails instead of being silently ignored.
	 * @mago-expect lint:literal-named-argument(5)
	 */
	protected function sealMockObjects(): void
	{
		$this->repository->method('find')->seal();

		$this->mapper->method('map')->seal();

		$this->requestStack->method('getCurrentRequest')->seal();

		$this->duplicateValidator->method('validatePost')->seal();

		$this->emptyPayloadValidator->method('validatePatch')->seal();
	}

	/** Covers: set up. */
	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository = $this->createMock(RoleRepository::class);

		$this->mapper = $this->createMock(ObjectMapperInterface::class);

		$this->requestStack = $this->createMock(RequestStack::class);

		$this->duplicateValidator = $this->createMock(RoleDuplicateValidatorInterface::class);

		$this->emptyPayloadValidator = $this->createMock(RoleEmptyPayloadValidatorInterface::class);

		$this->handler = new RolePayloadHandler(
			requestStack: $this->requestStack,
			duplicateValidator: $this->duplicateValidator,
			emptyPayloadValidator: $this->emptyPayloadValidator,
		);

		$this->processor = new RoleProcessor(
			repository: $this->repository,
			mapper: $this->mapper,
			handler: $this->handler,
		);
	}

	/**
	 * Covers: stub raw payload.
	 * @mago-expect lint:literal-named-argument
	 */
	protected function stubRawPayload(string|false $json): void
	{
		if ($json === false) {
			throw new RuntimeException('Unable to encode the stub payload.');
		}

		$this->requestStack->method('getCurrentRequest')->willReturn(new Request(content: $json));
	}
}
