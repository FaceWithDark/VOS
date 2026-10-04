<?php

declare(strict_types=1);

namespace App\Tests\Unit\State\Provider\Web;


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


#[CoversClass(className: UserProvider::class)]
#[UsesClass(className: UserAbstract::class)]
#[UsesClass(className: UserEntity::class)]
class UserProviderTest extends TestCase
{
	private UserRepository&MockObject			$repository;
	private ObjectMapperInterface&MockObject	$mapper;
	private UserProvider						$provider;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository	= $this->createMock(type: UserRepository::class);
		$this->mapper		= $this->createMock(type: ObjectMapperInterface::class);
		$this->provider		= new UserProvider(
			repository:	$this->repository,
			mapper:		$this->mapper,
		);
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
	}

	#[Test]
	public function testProvideCollectionMapsEveryEntity(): void
	{
		$entities = [
			new UserEntity(),
			new UserEntity(),
		];
		$first = new UserResourceDto();
		$second = new UserResourceDto();

		$this
			->repository
			->expects(self::once())
			->method('findAll')
			->willReturn($entities);

		$this
			->mapper
			->expects(self::exactly(2))
			->method('map')
			->willReturnOnConsecutiveCalls(
				$first,
				$second,
			);

		$this->sealCollaborators();

		$result
			= $this
				->provider
				->provide(operation: new GetCollection());

		self::assertSame(
			expected: [
				$first,
				$second,
			],
			actual: $result,
		);
	}

	#[Test]
	public function testProvideCollectionMapsEmptyResultToEmptyArray(): void
	{
		$this
			->repository
			->expects(self::once())
			->method('findAll')
			->willReturn([]);

		$this
			->mapper
			->expects(self::never())
			->method('map');

		$this->sealCollaborators();

		$result
			= $this
				->provider
				->provide(operation: new GetCollection());

		self::assertSame(
			expected: [],
			actual: $result,
		);
	}

	#[Test]
	public function testProvideIndividualMapsFoundEntity(): void
	{
		$entity		= new UserEntity();
		$resource	= new UserResourceDto();

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(88888)
			->willReturn($entity);

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->with(
				$entity,
				UserResourceDto::class,
			)
			->willReturn($resource);

		$this->sealCollaborators();

		$result
		   	= $this
				->provider
				->provide(
					operation:		new Get(),
					uriVariables:	['id' => 88888],
				);

		self::assertSame(
			expected: $resource,
			actual: $result,
		);
	}

	#[Test]
	public function testProvideIndividualReturnsNullWhenEntityMissing(): void
	{
		// API Platform turns this NULL into the 404 for a missing ID
		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(404)
			->willReturn(null);

		$this
			->mapper
			->expects(self::never())
			->method('map');

		$this->sealCollaborators();

		$result
		   	= $this
				->provider
				->provide(
					operation:		new Get(),
					uriVariables:	['id' => 404],
				);

		self::assertNull(actual: $result);
	}
}
