<?php

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
use App\Dto\Input\Web\RoleCreateDto;
use App\Dto\Input\Web\RoleUpdateDto;
use App\Dto\Main\Web\RoleResourceDto;
use App\Entity\Web\RoleEntity;
use App\Interface\Web\RoleDuplicateValidatorInterface;
use App\Repository\Web\RoleRepository;
use App\State\Processor\Web\RoleProcessor;


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
#[CoversClass(className: RoleProcessor::class)]
class RoleProcessorTest extends TestCase
{
	private RoleRepository&MockObject					$repository;
	private ObjectMapperInterface&MockObject			$mapper;
	private RequestStack&MockObject						$requestStack;
	private RoleDuplicateValidatorInterface&MockObject	$duplicateValidator;
	private RoleProcessor								$processor;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository			= $this->createMock(type: RoleRepository::class);
		$this->mapper				= $this->createMock(type: ObjectMapperInterface::class);
		$this->requestStack			= $this->createMock(type: RequestStack::class);
		$this->duplicateValidator	= $this->createMock(type: RoleDuplicateValidatorInterface::class);

		$this->processor = new RoleProcessor(
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
		$dto = new RoleCreateDto();

		$dto->name			= 'Gambler';
		$dto->description	= 'double the pay, double the deal baby. That is what high risk high reward about.';

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
				self::isInstanceOf(className: RoleEntity::class),
				true
			);

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new RoleResourceDto());

		$resource
			= $this
			->processor
			->process(
				data: $dto,
				operation: new Post(),
			);

		self::assertInstanceOf(
			expected: RoleResourceDto::class,
			actual: $resource,
		);
    }

	#[Test]
	public function testValidatePostWhenNotPersistData(): void
	{
		$dto = new RoleCreateDto();

		$dto->name = 'Admin';

		$testPayload = ['name' => $dto->name];

		$this->stubRawPayload(json: json_encode(value: $testPayload));

		$this
			->duplicateValidator
			->method('validatePost')
			->willThrowException(new ConflictHttpException(message: 'duplicate role name.'));

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
		$dto = new RoleUpdateDto();

		$dto->name = 'Gambler';

		$testPayload = ['id' => 3];

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
		$this->expectExceptionMessage(message: "Role with ID [{$testPayload['id']}] not found.");

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
		$testPayload = ['name' => 'Gambler'];
		$roleEntity = new RoleEntity();
		$roleCurrentData
			= $roleEntity
			->setId(id: 2)
			->setName(name: 'Admin')
			->setDescription(description: 'can take control of the whole website both internally and externally.');

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(2)
			->willReturn($roleCurrentData);

		$this->stubRawPayload(json: json_encode(value: $testPayload));

		// The validator gets (payload, id) with 'id' being the URL ID
		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$testPayload,
				2
			);

		$this
			->repository
			->expects(self::once())
			->method('save');

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new RoleResourceDto());

		$dto = new RoleUpdateDto();

		$dto->name = $testPayload['name'];

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 2],
			);

		self::assertSame(
			expected: $testPayload['name'],
			actual: $roleEntity->getName(),
		);
	}

	#[Test]
	public function testValidatePatchWhenOnlyDescriptionData(): void
	{
		$testPayload = ['description' => 'double the pay, double the deal baby. That is what high risk high reward about.'];
		$roleEntity = new RoleEntity();
		$roleCurrentData
			= $roleEntity
			->setId(id: 2)
			->setName(name: 'Admin')
			->setDescription(description: 'can take control of the whole website both internally and externally.');

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(2)
			->willReturn($roleCurrentData);

		// Only provide the optional 'description' field
		$this->stubRawPayload(json: json_encode(value: $testPayload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$testPayload,
				2
			);

		$this
			->repository
			->expects(self::once())
			->method('save');

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new RoleResourceDto());

		$dto = new RoleUpdateDto();

		$dto->description = $testPayload['description'];

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 2],
			);

		self::assertSame(
			expected: 'Admin',
			actual: $roleEntity->getName(),
			message: 'Role name must NOT change.',
		);
		self::assertSame(
			expected: $testPayload['description'],
			actual: $roleEntity->getDescription(),
		);
	}

	#[Test]
	public function testValidatePatchWhenNullDescriptionData(): void
	{
		$testPayload = ['description' => null];
		$roleEntity = new RoleEntity();
		$roleCurrentData
			= $roleEntity
			->setId(id: 2)
			->setName(name: 'Admin')
			->setDescription(description: 'can take control of the whole website both internally and externally.');

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(2)
			->willReturn($roleCurrentData);

		// Optional 'description' field provided but NULL value
		$this->stubRawPayload(json: json_encode(value: $testPayload));

		$this
			->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$testPayload,
				2,
			);

		$this
			->repository
			->expects(self::once())
			->method('save');

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->willReturn(new RoleResourceDto);

		$dto = new RoleUpdateDto();

		$dto->description = $testPayload['description'];

		$this
			->processor
			->process(
				data: $dto,
				operation: new Patch(),
				payload: ['id' => 2],
			);

		self::assertNull(actual: $roleCurrentData->getDescription());
	}

	#[Test]
	public function testValidatePatchWhenSameData(): void
	{
		$testPayload = ['name' => 'User'];
		$roleEntity = new RoleEntity();
		$roleCurrentData
			= $roleEntity
			->setId(id: 2)
			->setName(name: 'Admin')
			->setDescription(description: 'can take control of the whole website both internally and externally.');

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(2)
			->willReturn($roleCurrentData);

		$this->stubRawPayload(json: json_encode(value: $testPayload));

		$this
			->duplicateValidator
			->method('validatePatch')
			->willThrowException(new BadRequestHttpException(message: 'duplicate role name.'));

		$this
			->repository
			->expects(self::never())
			->method('save');

		$this->expectException(exception: BadRequestHttpException::class);

		$dto = new RoleUpdateDto();

		$dto->name = $testPayload['name'];

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
    public function testValidateDeleteWhenRemoveEntity(): void
    {
		$testPayload = ['id' => 2];
		$roleEntity = new RoleEntity();
		$roleCurrentData
			= $roleEntity
			->setId(id: $testPayload['id']);

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with($testPayload['id'])
			->willReturn($roleCurrentData);

		$this
			->repository
            ->expects(self::once())
            ->method('remove')
			->with(
				$roleCurrentData,
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
		$testPayload = ['id' => 3];

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
