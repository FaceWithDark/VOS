<?php

namespace App\Tests\State\Processor\Catalog;


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
use App\Dto\Input\Catalog\TournamentCreateDto;
use App\Dto\Input\Catalog\TournamentUpdateDto;
use App\Dto\Main\Catalog\TournamentResourceDto;
use App\Entity\Catalog\TournamentEntity;
use App\Interface\Catalog\TournamentDuplicateValidatorInterface;
use App\Repository\Catalog\TournamentRepository;
use App\State\Processor\Catalog\TournamentProcessor;


/**
 * ---------------------------------------------------------------------------
 *				NOTE ON `#[AllowMockObjectsWithoutExpectations]`
 * ---------------------------------------------------------------------------
 * All processor collaborators are declared once in `setUp()` as mocks so that
 * individual test methods stay short. However, not every test verifies every
 * collaborator. For example, the DELETE tests don't touch `$mapper`,
 * `$requestStack`, or `$duplicateValidator` at all. PHPUnit 12.5+ emits a
 * notice for each such "mock without expectations" to nudge towards
 * `createStub()`.
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
#[CoversClass(className: TournamentProcessor::class)]
class TournamentProcessorTest extends TestCase
{
	private TournamentRepository&MockObject						$repository;
	private ObjectMapperInterface&MockObject					$mapper;
	private RequestStack&MockObject								$requestStack;
	private TournamentDuplicateValidatorInterface&MockObject	$duplicateValidator;
	private TournamentProcessor									$processor;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository			= $this->createMock(type: TournamentRepository::class);
		$this->mapper				= $this->createMock(type: ObjectMapperInterface::class);
		$this->requestStack			= $this->createMock(type: RequestStack::class);
		$this->duplicateValidator	= $this->createMock(type: TournamentDuplicateValidatorInterface::class);

		$this->processor = new TournamentProcessor(
			repository:			$this->repository,
			mapper:				$this->mapper,
			requestStack:		$this->requestStack,
			duplicateValidator: $this->duplicateValidator,
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


	/**
	 * POST requests
	 */


	#[Test]
    public function testValidatePostWhenPersistData(): void
    {
		$dto = new TournamentCreateDto();

		$dto->name			= 'VOT88';
		$dto->description	= 'Vietnamese Osu!taiko Tournament 88 (special edition).';

		$testPayload = [
			'name'			=> $dto->name,
			'description'	=> $dto->description,
		];

		$this->stubRawPayload(json: json_encode(value: $testPayload));

		// The validator MUST be consulted exactly once with the raw payload
		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePost')
			->with($testPayload);

		$this
			->repository
			->expects(self::once())
			->method('save')
			->with(
				self::isInstanceOf(className: TournamentEntity::class),
				true
			);

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new TournamentResourceDto());

		$resource
			= $this
			->processor
			->process(
				data: $dto,
				operation: new Post(),
			);

		self::assertInstanceOf(
			expected: TournamentResourceDto::class,
			actual: $resource,
		);
    }

	#[Test]
	public function testValidatePostWhenNotPersistData(): void
	{
		$dto = new TournamentCreateDto();

		$dto->name = 'VOT6';

		$testPayload = ['name' => $dto->name];

		$this->stubRawPayload(json: json_encode(value: $testPayload));

		$this
			->duplicateValidator
			->method('validatePost')
			->willThrowException(new ConflictHttpException(message: 'duplicate tournament name.'));

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
		$dto = new TournamentUpdateDto();

		$dto->name = 'VOT88';

		$testPayload = ['id' => 88];

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
		$this->expectExceptionMessage(message: "Tournament with ID [{$testPayload['id']}] not found.");

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
		$testPayload = ['name' => 'VOT88'];
		$tournamentEntity = new TournamentEntity();
		$tournamentCurrentData
			= $tournamentEntity
			->setId(id: 7)
			->setName(name: 'VOT6')
			->setDescription(description: 'Vietnamese Osu!taiko Tournament 6');

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(7)
			->willReturn($tournamentCurrentData);

		$this->stubRawPayload(json: json_encode(value: $testPayload));

		// The validator gets (payload, id) with 'id' being the URL ID
		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$testPayload,
				7
			);

		$this
			->repository
			->expects(self::once())
			->method('save');

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new TournamentResourceDto());

		$dto = new TournamentUpdateDto();

		$dto->name = $testPayload['name'];

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 7],
			);

		self::assertSame(
			expected: $testPayload['name'],
			actual: $tournamentEntity->getName(),
		);
	}

	#[Test]
	public function testValidatePatchWhenOnlyDescriptionData(): void
	{
		$testPayload = ['description' => 'Vietnamese Osu!taiko Tournament 88 (special edition).'];
		$tournamentEntity = new TournamentEntity();
		$tournamentCurrentData
			= $tournamentEntity
			->setId(id: 7)
			->setName(name: 'VOT6')
			->setDescription(description: 'Vietnamese Osu!taiko Tournament 6');

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(7)
			->willReturn($tournamentCurrentData);

		// Only provide the optional 'description' field
		$this->stubRawPayload(json: json_encode(value: $testPayload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$testPayload,
				7
			);

		$this
			->repository
			->expects(self::once())
			->method('save');

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new TournamentResourceDto());

		$dto = new TournamentUpdateDto();

		$dto->description = $testPayload['description'];

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 7],
			);

		self::assertSame(
			expected: 'VOT6',
			actual: $tournamentEntity->getName(),
			message: 'Tournament name must NOT change.',
		);
		self::assertSame(
			expected: $testPayload['description'],
			actual: $tournamentEntity->getDescription(),
		);
	}

	#[Test]
	public function testValidatePatchWhenNullDescriptionData(): void
	{
		$testPayload = ['description' => null];
		$tournamentEntity = new TournamentEntity();
		$tournamentCurrentData
			= $tournamentEntity
			->setId(id: 7)
			->setName(name: 'VOT6')
			->setDescription(description: 'Vietnamese Osu!taiko Tournament 6');

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(7)
			->willReturn($tournamentCurrentData);

		// Optional 'description' field provided but NULL value
		$this->stubRawPayload(json: json_encode(value: $testPayload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$testPayload,
				7,
			);

		$this
			->repository
			->expects(self::once())
			->method('save');

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new TournamentResourceDto);

		$dto = new TournamentUpdateDto();

		$dto->description = $testPayload['description'];

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 7],
			);

		self::assertNull(actual: $tournamentCurrentData->getDescription());
	}

	#[Test]
	public function testValidatePatchWhenSameData(): void
	{
		$testPayload = ['name' => 'VTC3'];
		$tournamentEntity = new TournamentEntity();
		$tournamentCurrentData
			= $tournamentEntity
			->setId(id: 7)
			->setName(name: 'VOT6')
			->setDescription(description: 'Vietnamese Osu!taiko Tournament 6');

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(7)
			->willReturn($tournamentCurrentData);

		$this->stubRawPayload(json: json_encode(value: $testPayload));

		$this
			->duplicateValidator
			->method('validatePatch')
			->willThrowException(new BadRequestHttpException(message: 'duplicate tournament name.'));

		$this
			->repository
			->expects(self::never())
			->method('save');

		$this->expectException(exception: BadRequestHttpException::class);

		$dto = new TournamentUpdateDto();

		$dto->name = $testPayload['name'];

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
    public function testValidateDeleteWhenRemoveEntity(): void
    {
		$testPayload = ['id' => 7];
		$tournamentEntity = new TournamentEntity();
		$tournamentCurrentData
			= $tournamentEntity
			->setId(id: $testPayload['id']);

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with($testPayload['id'])
			->willReturn($tournamentCurrentData);

		$this
			->repository
            ->expects(self::once())
            ->method('remove')
			->with(
				$tournamentCurrentData,
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
		$testPayload = ['id' => 88];

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
