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
use PHPUnit\Framework\Attributes\DataProvider;
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
use function array_fill_keys;
use function array_intersect_key;
use function count;
use function in_array;
use function json_encode;
use InvalidArgumentException;
use Override;
use RuntimeException;
use stdClass;


/// --- Internal namespaces --- ///
use App\Dto\Input\Web\UserCreateDto;
use App\Dto\Input\Web\UserUpdateDto;
use App\Dto\Main\Web\UserResourceDto;
use App\Entity\Abstract\RoleAbstract;
use App\Entity\Abstract\UserAbstract;
use App\Entity\Web\RoleEntity;
use App\Entity\Web\UserEntity;
use App\Interface\Web\UserDuplicateValidatorInterface;
use App\Interface\Web\UserEmptyPayloadValidatorInterface;
use App\Interface\Web\UserForeignKeyValidatorInterface;
use App\Repository\Web\RoleRepository;
use App\Repository\Web\UserRepository;
use App\Service\Web\UserPayloadHandler;
use App\State\Processor\Web\UserProcessor;


// @mago-format-ignore-end
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
#[CoversClass(UserProcessor::class)]
#[UsesClass(RoleAbstract::class)]
#[UsesClass(RoleEntity::class)]
#[UsesClass(UserAbstract::class)]
#[UsesClass(UserEntity::class)]
#[UsesClass(UserPayloadHandler::class)]
class UserProcessorTest extends TestCase
{
	/** Repository. */
	protected UserRepository&MockObject $repository;

	/** Role repository. */
	protected RoleRepository&MockObject $roleRepository;

	/** Mapper. */
	protected ObjectMapperInterface&MockObject $mapper;

	/** Request stack. */
	protected RequestStack&MockObject $requestStack;

	/** Duplicate validator. */
	protected UserDuplicateValidatorInterface&MockObject $duplicateValidator;

	/** Foreign key validator. */
	protected UserForeignKeyValidatorInterface&MockObject $foreignKeyValidator;

	/** Empty payload validator. */
	protected UserEmptyPayloadValidatorInterface&MockObject $emptyPayloadValidator;

	/** Payload handler. */
	protected UserPayloadHandler $handler;

	/** Processor. */
	protected UserProcessor $processor;

	/**
	 * POST requests
	 */

	/**
	 * PATCH requests
	 */

	/**
	 * Every combination of the five optional PATCH fields. `handlePatch()` owns
	 * one independent guard per field, so exercising the full power set is the
	 * only way to reach every path through its decision table.
	 *
	 * @return array<array-key, array{0: list<string>}>
	 */
	public static function providePatchFieldCombinations(): array
	{
		$fields = ['name', 'avatar', 'rank', 'countryFlag', 'roleId'];

		$combinations = [];

		for ($mask = 0; $mask < (1 << count(value: $fields)); ++$mask) {
			/** @var list<string> $presentFields */
			$presentFields = [];

			foreach ($fields as $index => $field) {
				if (($mask & (1 << $index)) === 0) {
					continue;
				}

				$presentFields[] = $field;
			}

			$combinations["mask {$mask}"] = [$presentFields];
		}

		return $combinations;
	}

	/**
	 * Covers: test delete removes entity and returns null.
	 * @mago-expect lint:literal-named-argument(3)
	 */
	#[Test]
	public function testDeleteRemovesEntityAndReturnsNull(): void
	{
		$current = $this->mockUserEntity();

		$this->repository->expects(self::once())->method('find')->with(88_888)->willReturn($current);

		$this->repository->expects(self::once())->method('remove')->with($current);

		$this->sealCollaborators();

		$result = $this->processor->process(
			data: null,
			operation: new Delete(),
			payload: ['id' => 88_888],
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
		$this->repository->expects(self::once())->method('find')->with(88_888)->willReturn(null);

		$this->repository->expects(self::never())->method('remove');

		$this->expectException(NotFoundHttpException::class);

		$this->expectExceptionMessageIs('User with ID [88888] not found.');

		$this->sealCollaborators();

		$this->processor->process(
			data: null,
			operation: new Delete(),
			payload: ['id' => 88_888],
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

		$this->expectExceptionMessageIs('User with ID [0] not found.');

		$this->sealCollaborators();

		$this->processor->process(
			data: null,
			operation: new Delete(),
			payload: [],
		);
	}

	/**
	 * @param list<string> $presentFields
	 * @mago-expect lint:literal-named-argument(8)
	 */
	#[Test]
	#[DataProvider('providePatchFieldCombinations')]
	public function testPatchAppliesExactlyTheFieldsPresentInThePayload(array $presentFields): void
	{
		$dto = new UserUpdateDto();

		$dto->name = 'DeepInDark';

		$dto->avatar = 'https://a.ppy.sh/19817503?1752731877.png';

		$dto->rank = 5103;

		$dto->countryFlag = 'VN';

		$dto->roleId = 3;

		$current = $this->mockUserEntity(overrides: [
			'name'        => 'Gambler',
			'avatar'      => 'https://a.ppy.sh/88?88.png',
			'rank'        => 88,
			'countryFlag' => 'ZW',
		]);

		$previousRole = $current->getRoleId();

		$newRole = $this->mockRoleEntity(
			id: 3,
			name: 'Gambler',
		);

		$payload = array_intersect_key(
			[
				'name'        => $dto->name,
				'avatar'      => $dto->avatar,
				'rank'        => $dto->rank,
				'countryFlag' => $dto->countryFlag,
				'roleId'      => $dto->roleId,
			],
			array_fill_keys(
				$presentFields,
				value: true,
			),
		);

		$this->repository->expects(self::once())->method('find')->with(88_888)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		// The guards vary per data set; every collaborator is stubbed so the
		// combination under test is the only thing that changes the outcome.
		$this->emptyPayloadValidator->method('validatePatch');

		$this->duplicateValidator->method('validatePatch');

		$this->foreignKeyValidator->method('validatePatch');

		$this->roleRepository->method('find')->willReturn($newRole);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn(new UserResourceDto());

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 88_888],
		);

		self::assertSame(
			in_array(
				needle: 'name',
				haystack: $presentFields,
				strict: true,
			)
				? $dto->name
				: 'Gambler',
			$current->getName(),
		);

		self::assertSame(
			in_array(
				needle: 'avatar',
				haystack: $presentFields,
				strict: true,
			)
				? $dto->avatar
				: 'https://a.ppy.sh/88?88.png',
			$current->getAvatar(),
		);

		self::assertSame(
			in_array(
				needle: 'rank',
				haystack: $presentFields,
				strict: true,
			)
				? $dto->rank
				: 88,
			$current->getRank(),
		);

		self::assertSame(
			in_array(
				needle: 'countryFlag',
				haystack: $presentFields,
				strict: true,
			)
				? $dto->countryFlag
				: 'ZW',
			$current->getCountryFlag(),
		);

		self::assertSame(
			in_array(
				needle: 'roleId',
				haystack: $presentFields,
				strict: true,
			)
				? $newRole
				: $previousRole,
			$current->getRoleId(),
		);
	}

	/**
	 * Covers: test patch with duplicate name does not persist.
	 * @mago-expect lint:literal-named-argument(5)
	 */
	#[Test]
	public function testPatchWithDuplicateNameDoesNotPersist(): void
	{
		$dto = new UserUpdateDto();

		$dto->name = 'Gambler';

		$current = $this->mockUserEntity(overrides: ['name' => 'DeepInDark']);

		$payload = ['name' => $dto->name];

		$this->repository->expects(self::once())->method('find')->with(88_888)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->duplicateValidator
			->method('validatePatch')
			->willThrowException(
				new BadRequestHttpException(
					message: 'duplicate user name.',
				),
			);

		$this->repository->expects(self::never())->method('save');

		$this->mapper->expects(self::never())->method('map');

		$this->expectException(BadRequestHttpException::class);

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 88_888],
		);
	}

	/**
	 * Covers: test patch with empty payload throws bad request.
	 * @mago-expect lint:literal-named-argument(7)
	 */
	#[Test]
	public function testPatchWithEmptyPayloadThrowsBadRequest(): void
	{
		$current = $this->mockUserEntity();

		$this->repository->expects(self::once())->method('find')->with(88_888)->willReturn($current);

		// An empty request body decodes to []
		$this->stubRawPayload(json: '');

		$this->emptyPayloadValidator
			->method('validatePatch')
			->willThrowException(new BadRequestHttpException(message: 'Request payload must not be empty.'));

		// The entity exists, so the empty payload is what fails the request
		$this->duplicateValidator->expects(self::never())->method('validatePatch');

		$this->foreignKeyValidator->expects(self::never())->method('validatePatch');

		$this->repository->expects(self::never())->method('save');

		$this->expectException(BadRequestHttpException::class);

		$this->expectExceptionMessageIs('Request payload must not be empty.');

		$this->sealCollaborators();

		$this->processor->process(
			data: new UserUpdateDto(),
			operation: new Patch(),
			payload: ['id' => 88_888],
		);
	}

	/**
	 * Covers: test patch with missing entity throws not found.
	 * @mago-expect lint:literal-named-argument(7)
	 */
	#[Test]
	public function testPatchWithMissingEntityThrowsNotFound(): void
	{
		$dto = new UserUpdateDto();

		$dto->name = 'Gambler';

		$this->repository->expects(self::once())->method('find')->with(88_888)->willReturn(null);

		// Neither validator nor the mapper should be touched
		$this->duplicateValidator->expects(self::never())->method('validatePatch');

		$this->foreignKeyValidator->expects(self::never())->method('validatePatch');

		$this->mapper->expects(self::never())->method('map');

		$this->expectException(NotFoundHttpException::class);

		$this->expectExceptionMessageIs('User with ID [88888] not found.');

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 88_888],
		);
	}

	/**
	 * Covers: test patch with name updates only that field.
	 * @mago-expect lint:literal-named-argument(11)
	 */
	#[Test]
	public function testPatchWithNameUpdatesOnlyThatField(): void
	{
		$dto = new UserUpdateDto();

		$dto->name = 'DeepInDark';

		$current = $this->mockUserEntity();

		$previousRole = $current->getRoleId();

		$resource = new UserResourceDto();

		$payload = ['name' => $dto->name];

		$this->repository->expects(self::once())->method('find')->with(88_888)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		// The empty-payload guard is consulted before the duplicate lookup
		$this->emptyPayloadValidator->expects(self::once())->method('validatePatch')->with($payload);

		// The validators get (decoded payload, URL ID)
		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88_888,
			);

		$this->foreignKeyValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88_888,
			);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn($resource);

		$this->sealCollaborators();

		$result = $this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 88_888],
		);

		self::assertSame(
			$resource,
			$result,
		);

		self::assertSame(
			'DeepInDark',
			$current->getName(),
		);

		self::assertSame(
			$previousRole,
			$current->getRoleId(),
			'A name-only PATCH must not touch the role relation.',
		);
	}

	/**
	 * Covers: test patch with null role id throws bad request.
	 * @mago-expect lint:literal-named-argument(9)
	 */
	#[Test]
	public function testPatchWithNullRoleIdThrowsBadRequest(): void
	{
		$dto = new UserUpdateDto();

		$dto->roleId = null;

		$current = $this->mockUserEntity();

		$payload = ['roleId' => null];

		$this->repository->expects(self::once())->method('find')->with(88_888)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88_888,
			);

		$this->foreignKeyValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88_888,
			);

		// An explicit NULL cannot be resolved, so no Role lookup happens
		$this->roleRepository->expects(self::never())->method('find');

		$this->repository->expects(self::never())->method('save');

		$this->expectException(BadRequestHttpException::class);

		$this->expectExceptionMessageIs('Role ID must not be null.');

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 88_888],
		);
	}

	/**
	 * Covers: test patch with only avatar keeps other fields.
	 * @mago-expect lint:literal-named-argument(11)
	 */
	#[Test]
	public function testPatchWithOnlyAvatarKeepsOtherFields(): void
	{
		$dto = new UserUpdateDto();

		$dto->avatar = 'https://a.ppy.sh/19817503?1752731877.png';

		$current = $this->mockUserEntity(overrides: ['name' => 'DeepInDark', 'rank' => 5103, 'countryFlag' => 'VN']);

		$payload = ['avatar' => $dto->avatar];

		$this->repository->expects(self::once())->method('find')->with(88_888)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88_888,
			);

		$this->foreignKeyValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88_888,
			);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn(new UserResourceDto());

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 88_888],
		);

		self::assertSame(
			$dto->avatar,
			$current->getAvatar(),
		);

		self::assertSame(
			'DeepInDark',
			$current->getName(),
		);

		self::assertSame(
			5103,
			$current->getRank(),
		);

		self::assertSame(
			'VN',
			$current->getCountryFlag(),
		);
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

		$this->expectExceptionMessageIs('User with ID [0] not found.');

		$this->sealCollaborators();

		$this->processor->process(
			data: new UserUpdateDto(),
			operation: new Patch(),
			payload: [],
		);
	}

	/**
	 * Covers: test patch with rank and country flag updates them.
	 * @mago-expect lint:literal-named-argument(11)
	 */
	#[Test]
	public function testPatchWithRankAndCountryFlagUpdatesThem(): void
	{
		$dto = new UserUpdateDto();

		$dto->rank = 5103;

		$dto->countryFlag = 'VN';

		$current = $this->mockUserEntity(overrides: ['name' => 'DeepInDark']);

		$payload = [
			'rank'        => $dto->rank,
			'countryFlag' => $dto->countryFlag,
		];

		$this->repository->expects(self::once())->method('find')->with(88_888)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88_888,
			);

		$this->foreignKeyValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88_888,
			);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn(new UserResourceDto());

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 88_888],
		);

		self::assertSame(
			5103,
			$current->getRank(),
		);

		self::assertSame(
			'VN',
			$current->getCountryFlag(),
		);

		self::assertSame(
			'DeepInDark',
			$current->getName(),
		);
	}

	/**
	 * Covers: test patch with role id resolves new role.
	 * @mago-expect lint:literal-named-argument(10)
	 */
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

		$this->repository->expects(self::once())->method('find')->with(88_888)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88_888,
			);

		$this->foreignKeyValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88_888,
			);

		$this->roleRepository->expects(self::once())->method('find')->with(3)->willReturn($newRole);

		$this->repository->expects(self::once())->method('save')->with($current);

		$this->mapper->expects(self::once())->method('map')->willReturn(new UserResourceDto());

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 88_888],
		);

		self::assertSame(
			$newRole,
			$current->getRoleId(),
		);
	}

	/**
	 * Covers: test patch with unknown role throws not found.
	 * @mago-expect lint:literal-named-argument(11)
	 */
	#[Test]
	public function testPatchWithUnknownRoleThrowsNotFound(): void
	{
		$dto = new UserUpdateDto();

		$dto->roleId = 999;

		$current = $this->mockUserEntity();

		$payload = ['roleId' => $dto->roleId];

		$this->repository->expects(self::once())->method('find')->with(88_888)->willReturn($current);

		$this->stubRawPayload(json: json_encode(value: $payload));

		$this->duplicateValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88_888,
			);

		$this->foreignKeyValidator
			->expects(self::once())
			->method('validatePatch')
			->with(
				$payload,
				88_888,
			);

		$this->roleRepository->expects(self::once())->method('find')->with(999)->willReturn(null);

		$this->repository->expects(self::never())->method('save');

		$this->expectException(NotFoundHttpException::class);

		$this->expectExceptionMessageIs('Role with ID [999] not found.');

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Patch(),
			payload: ['id' => 88_888],
		);
	}

	/**
	 * Covers: test post forwards empty payload to validators.
	 * @mago-expect lint:literal-named-argument(5)
	 */
	#[Test]
	public function testPostForwardsEmptyPayloadToValidators(): void
	{
		$dto = new UserCreateDto();

		$dto->id = 88_888;

		$dto->roleId = 3;

		$dto->name = 'Gambler';

		$dto->avatar = 'https://a.ppy.sh/88?88.png';

		$dto->rank = 88;

		$dto->countryFlag = 'ZW';

		// An empty request body decodes to [], not to a decode error. POST is
		// handled by API Platform's deserialize/validate stage, so the
		// processor simply forwards it.
		$this->stubRawPayload(json: '');

		$this->duplicateValidator->expects(self::once())->method('validatePost')->with([]);

		$this->foreignKeyValidator->expects(self::once())->method('validatePost')->with([]);

		$this->roleRepository->expects(self::once())->method('find')->willReturn($this->mockRoleEntity());

		$this->repository->expects(self::once())->method('save');

		$this->mapper->expects(self::once())->method('map')->willReturn(new UserResourceDto());

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Post(),
		);
	}

	/**
	 * DELETE requests
	 */
	/**
	 * Unsupported input
	 */

	/**
	 * Covers: test post persists mapped entity.
	 * @mago-expect lint:literal-named-argument(5)
	 */
	#[Test]
	public function testPostPersistsMappedEntity(): void
	{
		$roleEntity = $this->mockRoleEntity();

		$dto = new UserCreateDto();

		$dto->id = 88_888;

		$dto->roleId = 3;

		$dto->name = 'Gambler';

		$dto->avatar = 'https://a.ppy.sh/88?88.png';

		$dto->rank = 88;

		$dto->countryFlag = 'ZW';

		$dtoPayload = [
			'id'          => $dto->id,
			'roleId'      => $dto->roleId,
			'name'        => $dto->name,
			'avatar'      => $dto->avatar,
			'rank'        => $dto->rank,
			'countryFlag' => $dto->countryFlag,
		];

		$resource = new UserResourceDto();

		$this->stubRawPayload(json: json_encode(value: $dtoPayload));

		// Both validators MUST be consulted exactly once with the raw payload
		$this->duplicateValidator->expects(self::once())->method('validatePost')->with($dtoPayload);

		$this->foreignKeyValidator->expects(self::once())->method('validatePost')->with($dtoPayload);

		// The FK MUST be resolved against the Role repository before persisting
		$this->roleRepository->expects(self::once())->method('find')->with($dto->roleId)->willReturn($roleEntity);

		// Every DTO field must land on the entity before it is stored
		$this->repository
			->expects(self::once())
			->method('save')
			->with(self::callback(
				static fn(UserEntity $entity): bool => (
					$entity->getId() === $dto->id
					&& $entity->getName() === $dto->name
					&& $entity->getAvatar() === $dto->avatar
					&& $entity->getRank() === $dto->rank
					&& $entity->getCountryFlag() === $dto->countryFlag
					&& $entity->getRoleId() === $roleEntity
					&& $entity->getCreateOn()?->getTimezone()->getName() === 'UTC'
				),
			));

		$this->mapper
			->expects(self::once())
			->method('map')
			->with(
				self::isInstanceOf(UserEntity::class),
				UserResourceDto::class,
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
		$dto = new UserCreateDto();

		$dto->id = 88_888;

		$dto->roleId = 3;

		$dto->name = 'Gambler';

		$dto->avatar = 'https://a.ppy.sh/88?88.png';

		$dto->rank = 88;

		$dto->countryFlag = 'ZW';

		$this->stubRawPayload(json: json_encode(value: ['name' => $dto->name]));

		$this->duplicateValidator
			->method('validatePost')
			->willThrowException(
				new ConflictHttpException(
					message: 'duplicate user name.',
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
	 * @mago-expect lint:literal-named-argument(5)
	 */
	#[Test]
	public function testPostWithoutCurrentRequestForwardsEmptyPayload(): void
	{
		$dto = new UserCreateDto();

		$dto->id = 88_888;

		$dto->roleId = 3;

		$dto->name = 'Gambler';

		$dto->avatar = 'https://a.ppy.sh/88?88.png';

		$dto->rank = 88;

		$dto->countryFlag = 'ZW';

		// No stubbed request at all: getDecodedPayload() must fall back to []
		$this->duplicateValidator->expects(self::once())->method('validatePost')->with([]);

		$this->foreignKeyValidator->expects(self::once())->method('validatePost')->with([]);

		$this->roleRepository->expects(self::once())->method('find')->willReturn($this->mockRoleEntity());

		$this->repository->expects(self::once())->method('save');

		$this->mapper->expects(self::once())->method('map')->willReturn(new UserResourceDto());

		$this->sealCollaborators();

		$this->processor->process(
			data: $dto,
			operation: new Post(),
		);
	}

	/**
	 * Covers: test post with unknown role throws not found.
	 * @mago-expect lint:literal-named-argument(6)
	 */
	#[Test]
	public function testPostWithUnknownRoleThrowsNotFound(): void
	{
		$dto = new UserCreateDto();

		$dto->id = 88_888;

		$dto->roleId = 999;

		$dto->name = 'Gambler';

		$dto->avatar = 'https://a.ppy.sh/88?88.png';

		$dto->rank = 88;

		$dto->countryFlag = 'ZW';

		$dtoPayload = [
			'name'   => $dto->name,
			'roleId' => $dto->roleId,
		];

		$this->stubRawPayload(json: json_encode(value: $dtoPayload));

		$this->duplicateValidator->expects(self::once())->method('validatePost')->with($dtoPayload);

		$this->foreignKeyValidator->expects(self::once())->method('validatePost')->with($dtoPayload);

		// The processor also owns the 404 as a defensive fallback
		$this->roleRepository->expects(self::once())->method('find')->with($dto->roleId)->willReturn(null);

		$this->repository->expects(self::never())->method('save');

		$this->expectException(NotFoundHttpException::class);

		$this->expectExceptionMessageIs('Role with ID [999] not found.');

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

	/** Covers: mock role entity. */
	protected function mockRoleEntity(
		int    $id = 3,
		string $name = 'Gambler',
	): RoleEntity {
		return new RoleEntity()
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
	/**
	 * @param array{id?: int, roleId?: ?RoleEntity, name?: string, avatar?: string, rank?: int, countryFlag?: string} $overrides
	 */
	protected function mockUserEntity(array $overrides = []): UserEntity
	{
		return new UserEntity()
			->setId(id: $overrides['id'] ?? 88_888)
			->setRoleId(
				roleId: $overrides['roleId'] ?? $this->mockRoleEntity(
					id: 1,
					name: 'User',
				),
			)
			->setName(name: $overrides['name'] ?? 'Gambler')
			->setAvatar(avatar: $overrides['avatar'] ?? 'https://a.ppy.sh/88?88.png')
			->setRank(rank: $overrides['rank'] ?? 88)
			->setCountryFlag(countryFlag: $overrides['countryFlag'] ?? 'ZW');
	}

	/**
	 * Seals every collaborator once the test has declared all of its
	 * expectations: any undeclared call now fails instead of returning null.
	 * @mago-expect lint:literal-named-argument(7)
	 */
	protected function sealCollaborators(): void
	{
		$this->repository->method('find')->seal();

		$this->roleRepository->method('find')->seal();

		$this->mapper->method('map')->seal();

		$this->requestStack->method('getCurrentRequest')->seal();

		$this->duplicateValidator->method('validatePost')->seal();

		$this->foreignKeyValidator->method('validatePost')->seal();

		$this->emptyPayloadValidator->method('validatePatch')->seal();
	}

	/** Prepares the test fixtures. */
	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository = $this->createMock(UserRepository::class);

		$this->roleRepository = $this->createMock(RoleRepository::class);

		$this->mapper = $this->createMock(ObjectMapperInterface::class);

		$this->requestStack = $this->createMock(RequestStack::class);

		$this->duplicateValidator = $this->createMock(UserDuplicateValidatorInterface::class);

		$this->foreignKeyValidator = $this->createMock(UserForeignKeyValidatorInterface::class);

		$this->emptyPayloadValidator = $this->createMock(UserEmptyPayloadValidatorInterface::class);

		$this->handler = new UserPayloadHandler(
			requestStack: $this->requestStack,
			roleRepository: $this->roleRepository,
			duplicateValidator: $this->duplicateValidator,
			foreignKeyValidator: $this->foreignKeyValidator,
			emptyPayloadValidator: $this->emptyPayloadValidator,
		);

		$this->processor = new UserProcessor(
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
