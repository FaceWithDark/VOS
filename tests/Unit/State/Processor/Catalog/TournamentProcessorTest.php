<?php

declare(strict_types=1);

namespace App\Tests\Unit\State\Processor\Catalog;


/// --- Main namespaces --- ///
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use InvalidArgumentException;
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
use Override;
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
use App\State\Processor\Catalog\TournamentProcessor;


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
#[CoversClass(className: TournamentProcessor::class)]
#[UsesClass(className: TournamentEntity::class)]
#[UsesClass(className: TournamentAbstract::class)]
class TournamentProcessorTest extends TestCase
{
	private TournamentRepository&MockObject						$repository;
	private ObjectMapperInterface&MockObject					$mapper;
	private RequestStack&MockObject								$requestStack;
	private TournamentDuplicateValidatorInterface&MockObject	$duplicateValidator;
	private TournamentEmptyPayloadValidatorInterface&MockObject	$emptyPayloadValidator;
	private TournamentProcessor									$processor;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository				= $this->createMock(type: TournamentRepository::class);
		$this->mapper					= $this->createMock(type: ObjectMapperInterface::class);
		$this->requestStack				= $this->createMock(type: RequestStack::class);
		$this->duplicateValidator		= $this->createMock(type: TournamentDuplicateValidatorInterface::class);
		$this->emptyPayloadValidator	= $this->createMock(type: TournamentEmptyPayloadValidatorInterface::class);

		$this->processor = new TournamentProcessor(
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

	private function mockTournamentEntity(): TournamentEntity
	{
		return (new TournamentEntity())
			->setId(id: 7)
			->setName(name: 'VOT6')
			->setDescription(description: 'Vietnamese Osu!taiko Tournament 6');
	}

	/**
	 * Seals every collaborator once the test has declared all of its
	 * expectations: any undeclared call now fails instead of returning null.
	 */
	private function sealCollaborators(): void
	{
		$this
			->repository
			->method('find')
			->seal();

		$this
			->mapper
			->method('map')
			->seal();

		$this
			->requestStack
			->method('getCurrentRequest')
			->seal();

		$this
			->duplicateValidator
			->method('validatePost')
			->seal();

		$this
			->emptyPayloadValidator
			->method('validatePatch')
			->seal();
	}


	/**
	 * POST requests
	 */


	#[Test]
	public function testPostPersistsMappedEntity(): void
	{
		$dto = new TournamentCreateDto();

		$dto->name			= 'VOT88';
		$dto->description	= 'Vietnamese Osu!taiko Tournament 88 (special edition).';

		$dtoPayload = [
			'name'			=> $dto->name,
			'description'	=> $dto->description,
		];

		$resource = new TournamentResourceDto();

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
					callback: static fn (TournamentEntity $entity): bool
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
				self::isInstanceOf(className: TournamentEntity::class),
				TournamentResourceDto::class,
			)
			->willReturn($resource);

		$this->sealCollaborators();

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
		$dto = new TournamentCreateDto();

		$dto->name = 'VOT88';

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
			->willReturn(new TournamentResourceDto());

		$this->sealCollaborators();

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
		$dto = new TournamentCreateDto();

		$dto->name = 'VOT88';

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
			->willReturn(new TournamentResourceDto());

		$this->sealCollaborators();

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
		$dto = new TournamentCreateDto();

		$dto->name = 'VOT6';

		$this->stubRawPayload(
			json: json_encode(
				value: ['name' => $dto->name],
			),
		);

		$this
			->duplicateValidator
			->method('validatePost')
			->willThrowException(
				new ConflictHttpException(
					message: 'duplicate tournament name.',
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

		$this->sealCollaborators();

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
		$dto = new TournamentUpdateDto();

		$dto->name = 'VOT88';

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88)
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
		$this->expectExceptionMessage(message: 'Tournament with ID [88] not found.');

		$this->sealCollaborators();

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 88],
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
		$this->expectExceptionMessage(message: 'Tournament with ID [0] not found.');

		$this->sealCollaborators();

		$this
			->processor
			->process(
				data: new TournamentUpdateDto(),
				operation: new Patch(),
				payload: [],
			);
	}

	#[Test]
	public function testPatchWithEmptyPayloadThrowsBadRequest(): void
	{
		$current = $this->mockTournamentEntity();

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(7)
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

		$this->sealCollaborators();

		$this
			->processor
			->process(
				data: new TournamentUpdateDto(),
				operation: new Patch(),
				payload: ['id' => 7],
			);
	}

	#[Test]
	public function testPatchWithNameUpdatesOnlyThatField(): void
	{
		$dto = new TournamentUpdateDto();

		$dto->name = 'VOT88';

		$current	= $this->mockTournamentEntity();
		$resource	= new TournamentResourceDto();
		$payload	= ['name' => $dto->name];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(7)
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
				7,
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

		$this->sealCollaborators();

		$result = $this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 7],
			);

		self::assertSame(
			expected: $resource,
			actual: $result,
		);
		self::assertSame(
			expected: 'VOT88',
			actual: $current->getName(),
		);
		self::assertSame(
			expected: 'Vietnamese Osu!taiko Tournament 6',
			actual: $current->getDescription(),
			message: 'An omitted description must survive a name-only PATCH.',
		);
	}

	#[Test]
	public function testPatchWithDescriptionOnlyKeepsName(): void
	{
		$dto = new TournamentUpdateDto();

		$dto->description = 'Vietnamese Osu!taiko Tournament 88 (special edition).';

		$current	= $this->mockTournamentEntity();
		$payload	= ['description' => $dto->description];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(7)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				7,
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
			->willReturn(new TournamentResourceDto());

		$this->sealCollaborators();

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 7],
			);

		self::assertSame(
			expected: 'VOT6',
			actual: $current->getName()
		);
		self::assertSame(
			expected: $dto->description,
			actual: $current->getDescription()
		);
	}

	#[Test]
	public function testPatchWithNullDescriptionClearsIt(): void
	{
		$dto = new TournamentUpdateDto();

		$dto->description = null;

		$current	= $this->mockTournamentEntity();
		$payload	= ['description' => null];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(7)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				7,
			);

		$this
			->repository
			->expects(self::once())
			->method('save')
			->with($current, true);

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new TournamentResourceDto());

		$this->sealCollaborators();

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 7],
			);

		self::assertNull(actual: $current->getDescription());
	}

	#[Test]
	public function testPatchWithNameAndDescriptionUpdatesBothFields(): void
	{
		$dto = new TournamentUpdateDto();

		$dto->name			= 'VOT88';
		$dto->description	= 'Vietnamese Osu!taiko Tournament 88 (special edition).';

		$current	= $this->mockTournamentEntity();
		$payload	= [
			'name'			=> $dto->name,
			'description'	=> $dto->description,
		];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(7)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this
			->emptyPayloadValidator
			->expects(self::once())
			->method('validatePatch')
			->with($payload);

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				7,
			);

		$this
			->repository
			->expects(self::once())
			->method('save')
			->with($current, true);

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new TournamentResourceDto());

		$this->sealCollaborators();

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 7],
			);

		self::assertSame(
			expected: 'VOT88',
			actual: $current->getName(),
		);
		self::assertSame(
			expected: $dto->description,
			actual: $current->getDescription(),
		);
	}

	#[Test]
	public function testPatchWithUnrelatedPayloadSkipsBothFieldUpdates(): void
	{
		$dto = new TournamentUpdateDto();

		$current	= $this->mockTournamentEntity();
		$payload	= ['rank' => 5];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(7)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this
			->emptyPayloadValidator
			->expects(self::once())
			->method('validatePatch')
			->with($payload);

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				7,
			);

		$this
			->repository
			->expects(self::once())
			->method('save')
			->with($current, true);

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new TournamentResourceDto());

		$this->sealCollaborators();

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 7],
			);

		// Neither 'name' nor 'description' was sent: stored values survive
		self::assertSame(
			expected: 'VOT6',
			actual: $current->getName(),
		);
		self::assertSame(
			expected: 'Vietnamese Osu!taiko Tournament 6',
			actual: $current->getDescription(),
		);
	}

	#[Test]
	public function testPatchWithDuplicateNameDoesNotPersist(): void
	{
		$dto = new TournamentUpdateDto();

		$dto->name = 'VTC3';

		$current	= $this->mockTournamentEntity();
		$payload	= ['name' => $dto->name];

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(7)
			->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this
			->duplicateValidator
			->method('validatePatch')
			->willThrowException(
				new BadRequestHttpException(
					message: 'duplicate tournament name.',
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

		$this->sealCollaborators();

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 7],
			);
	}


	/**
	 * DELETE requests
	 */


	#[Test]
	public function testDeleteRemovesEntityAndReturnsNull(): void
	{
		$current = $this->mockTournamentEntity();

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(7)
			->willReturn($current);

		$this
			->repository
			->expects(self::once())
			->method('remove')
			->with(
				$current,
				true,
			);

		$this->sealCollaborators();

		$result = $this
			->processor
			->process(
				data: null,
				operation: new Delete(),
				payload: ['id' => 7],
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
			->with(88)
			->willReturn(null);

		$this
			->repository
			->expects(self::never())
			->method('remove');

		$this->expectException(exception: NotFoundHttpException::class);
		$this->expectExceptionMessage(message: 'Tournament with ID [88] not found.');

		$this->sealCollaborators();

		$this
			->processor
			->process(
				data: null,
				operation: new Delete(),
				payload: ['id' => 88],
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
		$this->expectExceptionMessage(message: 'Tournament with ID [0] not found.');

		$this->sealCollaborators();

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

		$this->sealCollaborators();

		$this
			->processor
			->process(
				data: new stdClass(),
				operation: new Post(),
			);
	}
}
