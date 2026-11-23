<?php

declare(strict_types=1);

namespace App\Tests\Unit\State\Processor\Catalog;

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
use App\Dto\Input\Catalog\TournamentCreateDto;
use App\Dto\Input\Catalog\TournamentUpdateDto;
use App\Dto\Main\Catalog\TournamentResourceDto;
use App\Entity\Abstract\TournamentAbstract;
use App\Entity\Catalog\TournamentEntity;
use App\Interface\Catalog\TournamentDuplicateValidatorInterface;
use App\Interface\Catalog\TournamentEmptyPayloadValidatorInterface;
use App\Repository\Catalog\TournamentRepository;
use App\Service\Catalog\TournamentPayloadHandler;
use App\State\Processor\Catalog\TournamentProcessor;


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
#[CoversClass(TournamentProcessor::class)]
#[UsesClass(TournamentEntity::class)]
#[UsesClass(TournamentAbstract::class)]
#[UsesClass(TournamentPayloadHandler::class)]
class TournamentProcessorTest extends TestCase
{
	/** Repository. */
	protected TournamentRepository&MockObject $repository;

	/** Mapper. */
	protected ObjectMapperInterface&MockObject $mapper;

	/** Request stack. */
	protected RequestStack&MockObject $requestStack;

	/** Duplicate validator. */
	protected TournamentDuplicateValidatorInterface&MockObject $duplicateValidator;

	/** Empty payload validator. */
	protected TournamentEmptyPayloadValidatorInterface&MockObject $emptyPayloadValidator;

	/** Payload handler. */
	protected TournamentPayloadHandler $handler;

	/** Processor. */
	protected TournamentProcessor $processor;

	/**
	 * Covers: test delete removes entity and returns null.
	 * @mago-expect lint:literal-named-argument(3)
	 */
	#[Test]
	public function testDeleteRemovesEntityAndReturnsNull(): void
	{
		$current = $this->mockTournamentEntity();

		$this->repository->expects(self::once())->method('find')->with(7)->willReturn($current);

		$this->repository->expects(self::once())->method('remove')->with($current);

		$this->sealCollaborators();

		$result = $this->processor->process(
			data: null,
			operation: new Delete(),
			payload: ['id' => 7],
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
		$this->repository->expects(self::once())->method('find')->with(88)->willReturn(null);

		$this->repository->expects(self::never())->method('remove');

		$this->expectException(NotFoundHttpException::class);

		$this->expectExceptionMessageIs('Tournament with ID [88] not found.');

		$this->sealCollaborators();

		$this->processor->process(
			data: null,
			operation: new Delete(),
			payload: ['id' => 88],
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

		$this->expectExceptionMessageIs('Tournament with ID [0] not found.');

		$this->sealCollaborators();

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
		$dto = new TournamentUpdateDto();

		$dto->description = 'Vietnamese Osu!taiko Tournament 88 (special edition).';

		$current = $this->mockTournamentEntity();

		$payload = ['description' => $dto->description];

		$this->repository->expects(self::once())->method('find')->with(7)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				7,
			);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn(new TournamentResourceDto());

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 7],
		);

		self::assertSame(
			'VOT6',
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
		$dto = new TournamentUpdateDto();

		$dto->name = 'VTC3';

		$current = $this->mockTournamentEntity();

		$payload = ['name' => $dto->name];

		$this->repository->expects(self::once())->method('find')->with(7)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->duplicateValidator
			->method('validatePatch')
			->willThrowException(
				new BadRequestHttpException(
					message: 'duplicate tournament name.',
				),
			);

		$this->repository->expects(self::never())->method('save');

		$this->mapper->expects(self::never())->method('map');

		$this->expectException(BadRequestHttpException::class);

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 7],
		);
	}

	/**
	 * Covers: test patch with empty payload throws bad request.
	 * @mago-expect lint:literal-named-argument(6)
	 */
	#[Test]
	public function testPatchWithEmptyPayloadThrowsBadRequest(): void
	{
		$current = $this->mockTournamentEntity();

		$this->repository->expects(self::once())->method('find')->with(7)->willReturn($current);

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

		$this->sealCollaborators();

		$this->processor->process(
			data: new TournamentUpdateDto(),
			operation: new Patch(),
			payload: ['id' => 7],
		);
	}

	/**
	 * Covers: test patch with missing entity throws not found.
	 * @mago-expect lint:literal-named-argument(6)
	 */
	#[Test]
	public function testPatchWithMissingEntityThrowsNotFound(): void
	{
		$dto = new TournamentUpdateDto();

		$dto->name = 'VOT88';

		$this->repository->expects(self::once())->method('find')->with(88)->willReturn(null);

		// Neither the validator nor the mapper should be touched
		$this->duplicateValidator->expects(self::never())->method('validatePatch');

		$this->mapper->expects(self::never())->method('map');

		$this->expectException(NotFoundHttpException::class);

		$this->expectExceptionMessageIs('Tournament with ID [88] not found.');

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 88],
		);
	}

	/**
	 * Covers: test patch with name and description updates both fields.
	 * @mago-expect lint:literal-named-argument(8)
	 */
	#[Test]
	public function testPatchWithNameAndDescriptionUpdatesBothFields(): void
	{
		$dto = new TournamentUpdateDto();

		$dto->name = 'VOT88';

		$dto->description = 'Vietnamese Osu!taiko Tournament 88 (special edition).';

		$current = $this->mockTournamentEntity();

		$payload = [
			'name'        => $dto->name,
			'description' => $dto->description,
		];

		$this->repository->expects(self::once())->method('find')->with(7)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->emptyPayloadValidator->expects(self::once())->method('validatePatch')->with($payload);

		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				7,
			);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn(new TournamentResourceDto());

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 7],
		);

		self::assertSame(
			'VOT88',
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
		$dto = new TournamentUpdateDto();

		$dto->name = 'VOT88';

		$current = $this->mockTournamentEntity();

		$resource = new TournamentResourceDto();

		$payload = ['name' => $dto->name];

		$this->repository->expects(self::once())->method('find')->with(7)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		// The empty-payload guard is consulted before the duplicate lookup
		$this->emptyPayloadValidator->expects(self::once())->method('validatePatch')->with($payload);

		// The validator gets (decoded payload, URL ID)
		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				7,
			);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn($resource);

		$this->sealCollaborators();

		$result = $this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 7],
		);

		self::assertSame(
			$resource,
			$result,
		);

		self::assertSame(
			'VOT88',
			$current->getName(),
		);

		self::assertSame(
			'Vietnamese Osu!taiko Tournament 6',
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
		$dto = new TournamentUpdateDto();

		$dto->description = null;

		$current = $this->mockTournamentEntity();

		$payload = ['description' => null];

		$this->repository->expects(self::once())->method('find')->with(7)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				7,
			);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn(new TournamentResourceDto());

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 7],
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

		$this->expectExceptionMessageIs('Tournament with ID [0] not found.');

		$this->sealCollaborators();

		$this->processor->process(
			data: new TournamentUpdateDto(),
			operation: new Patch(),
			payload: [],
		);
	}

	/**
	 * Covers: test patch with unrelated payload skips both field updates.
	 * @mago-expect lint:literal-named-argument(9)
	 */
	#[Test]
	public function testPatchWithUnrelatedPayloadSkipsBothFieldUpdates(): void
	{
		$dto = new TournamentUpdateDto();

		$current = $this->mockTournamentEntity();

		$payload = ['rank' => 5];

		$this->repository->expects(self::once())->method('find')->with(7)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->emptyPayloadValidator->expects(self::once())->method('validatePatch')->with($payload);

		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				7,
			);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn(new TournamentResourceDto());

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 7],
		);

		// Neither 'name' nor 'description' was sent: stored values survive
		self::assertSame(
			'VOT6',
			$current->getName(),
		);

		self::assertSame(
			'Vietnamese Osu!taiko Tournament 6',
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
		$dto = new TournamentCreateDto();

		$dto->name = 'VOT88';

		// An empty request body decodes to [], not to a decode error. POST is
		// handled by API Platform's deserialize/validate stage, so the
		// processor simply forwards it.
		$this->stubRawPayload(json: '');

		$this->duplicateValidator->expects(self::once())->method('validatePost')->with([]);

		$this->repository->expects(self::once())->method('save');

		$this->mapper->expects(self::once())->method('map')->willReturn(new TournamentResourceDto());

		$this->sealCollaborators();

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
		$dto = new TournamentCreateDto();

		$dto->name = 'VOT88';

		$dto->description = 'Vietnamese Osu!taiko Tournament 88 (special edition).';

		$dtoPayload = [
			'name'        => $dto->name,
			'description' => $dto->description,
		];

		$resource = new TournamentResourceDto();

		$this->stubRawPayload(json: json_encode(value: $dtoPayload));

		// The validator MUST be consulted exactly once with the raw payload
		$this->duplicateValidator->expects(self::once())->method('validatePost')->with($dtoPayload);

		// Every DTO field must land on the entity before it is stored
		$this->repository
			->expects(self::once())
			->method('save')
			->with(self::callback(
				static fn(TournamentEntity $entity): bool => (
					$entity->getName() === $dto->name
					&& $entity->getDescription() === $dto->description
					&& $entity->getCreateOn()?->getTimezone()->getName() === 'UTC'
				),
			));

		$this->mapper
			->expects(self::once())
			->method('map')
			->with(
				self::isInstanceOf(TournamentEntity::class),
				TournamentResourceDto::class,
			)
			->willReturn($resource);

		$this->sealCollaborators();

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
		$dto = new TournamentCreateDto();

		$dto->name = 'VOT6';

		$this->stubRawPayload(
			json: json_encode(
				value: ['name' => $dto->name],
			),
		);

		$this->duplicateValidator
			->method('validatePost')
			->willThrowException(
				new ConflictHttpException(
					message: 'duplicate tournament name.',
				),
			);

		// An invalid request must never reach the database or the mapper
		$this->repository->expects(self::never())->method('save');

		$this->mapper->expects(self::never())->method('map');

		$this->expectException(ConflictHttpException::class);

		$this->sealCollaborators();

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
		$dto = new TournamentCreateDto();

		$dto->name = 'VOT88';

		// No stubbed request at all: getDecodedPayload() must fall back to []
		$this->duplicateValidator->expects(self::once())->method('validatePost')->with([]);

		$this->repository->expects(self::once())->method('save');

		$this->mapper->expects(self::once())->method('map')->willReturn(new TournamentResourceDto());

		$this->sealCollaborators();

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

		$this->sealCollaborators();

		$this->processor->process(
			data: new stdClass(),
			operation: new Post(),
		);
	}

	/** Covers: mock tournament entity. */
	protected function mockTournamentEntity(): TournamentEntity
	{
		return new TournamentEntity()
			->setId(id: 7)
			->setName(name: 'VOT6')
			->setDescription(description: 'Vietnamese Osu!taiko Tournament 6');
	}

	/**
	 * Seals every collaborator once the test has declared all of its
	 * expectations: any undeclared call now fails instead of returning null.
	 * @mago-expect lint:literal-named-argument(5)
	 */
	protected function sealCollaborators(): void
	{
		$this->repository->method('find')->seal();

		$this->mapper->method('map')->seal();

		$this->requestStack->method('getCurrentRequest')->seal();

		$this->duplicateValidator->method('validatePost')->seal();

		$this->emptyPayloadValidator->method('validatePatch')->seal();
	}

	/** Prepares the test fixtures. */
	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository = $this->createMock(TournamentRepository::class);

		$this->mapper = $this->createMock(ObjectMapperInterface::class);

		$this->requestStack = $this->createMock(RequestStack::class);

		$this->duplicateValidator = $this->createMock(TournamentDuplicateValidatorInterface::class);

		$this->emptyPayloadValidator = $this->createMock(TournamentEmptyPayloadValidatorInterface::class);

		$this->handler = new TournamentPayloadHandler(
			requestStack: $this->requestStack,
			duplicateValidator: $this->duplicateValidator,
			emptyPayloadValidator: $this->emptyPayloadValidator,
		);

		$this->processor = new TournamentProcessor(
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
