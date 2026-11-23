<?php

declare(strict_types=1);

namespace App\Tests\Unit\State\Provider\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Dto\Main\Web\UserResourceDto;
use App\Entity\Abstract\UserAbstract;
use App\Entity\Web\UserEntity;
use App\Repository\Web\UserRepository;
use App\State\Provider\Web\UserProvider;


// @mago-format-ignore-end
#[CoversClass(UserProvider::class)]
#[UsesClass(UserAbstract::class)]
#[UsesClass(UserEntity::class)]
class UserProviderTest extends TestCase
{
	/** Repository. */
	private UserRepository&MockObject $repository;
	/** Mapper. */
	private ObjectMapperInterface&MockObject $mapper;
	/** Provider. */
	private UserProvider $provider;

	/**
	 * Covers: test provide collection maps empty result to empty array.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testProvideCollectionMapsEmptyResultToEmptyArray(): void
	{
		$this->repository->expects(self::once())->method('findAll')->willReturn([]);

		$this->mapper->expects(self::never())->method('map');

		$this->sealCollaborators();

		$result = $this->provider->provide(operation: new GetCollection());

		self::assertSame(
			[],
			$result,
		);
	}

	/**
	 * Covers: test provide collection maps every entity.
	 * @mago-expect lint:literal-named-argument(3)
	 */
	#[Test]
	public function testProvideCollectionMapsEveryEntity(): void
	{
		$entities = [
			new UserEntity(),
			new UserEntity(),
		];
		$first  = new UserResourceDto();
		$second = new UserResourceDto();

		$this->repository->expects(self::once())->method('findAll')->willReturn($entities);

		$this->mapper
			->expects(self::exactly(2))
			->method('map')
			->willReturnOnConsecutiveCalls(
				$first,
				$second,
			);

		$this->sealCollaborators();

		$result = $this->provider->provide(operation: new GetCollection());

		self::assertSame(
			[
				$first,
				$second,
			],
			$result,
		);
	}

	/**
	 * Covers: test provide individual maps found entity.
	 * @mago-expect lint:literal-named-argument(3)
	 */
	#[Test]
	public function testProvideIndividualMapsFoundEntity(): void
	{
		$entity   = new UserEntity();
		$resource = new UserResourceDto();

		$this->repository->expects(self::once())->method('find')->with(88_888)->willReturn($entity);

		$this->mapper
			->expects(self::once())
			->method('map')
			->with(
				$entity,
				UserResourceDto::class,
			)
			->willReturn($resource);

		$this->sealCollaborators();

		$result = $this->provider->provide(
			operation: new Get(),
			uriVariables: ['id' => 88_888],
		);

		self::assertSame(
			$resource,
			$result,
		);
	}

	/**
	 * Covers: test provide individual returns null when entity missing.
	 * @mago-expect lint:literal-named-argument(4)
	 */
	#[Test]
	public function testProvideIndividualReturnsNullWhenEntityMissing(): void
	{
		// API Platform turns this NULL into the 404 for a missing ID
		$this->repository->expects(self::once())->method('find')->with(404)->willReturn(null);

		$this->mapper->expects(self::never())->method('map');

		$this->sealCollaborators();

		$result = $this->provider->provide(
			operation: new Get(),
			uriVariables: ['id' => 404],
		);

		self::assertNull($result);
	}

	/** Prepares the test fixtures. */
	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository = $this->createMock(UserRepository::class);
		$this->mapper     = $this->createMock(ObjectMapperInterface::class);
		$this->provider   = new UserProvider(
			repository: $this->repository,
			mapper: $this->mapper,
		);
	}

	/**
	 * Seals every collaborator once the test has declared all of its
	 * expectations: any undeclared call now fails instead of returning null.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	private function sealCollaborators(): void
	{
		$this->repository->method('find')->seal();

		$this->mapper->method('map')->seal();
	}
}
