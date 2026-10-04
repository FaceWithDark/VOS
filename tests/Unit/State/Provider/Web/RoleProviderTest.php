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
use App\Dto\Main\Web\RoleResourceDto;
use App\Entity\Abstract\RoleAbstract;
use App\Entity\Web\RoleEntity;
use App\Repository\Web\RoleRepository;
use App\State\Provider\Web\RoleProvider;


#[CoversClass(className: RoleProvider::class)]
#[UsesClass(className: RoleEntity::class)]
#[UsesClass(className: RoleAbstract::class)]
class RoleProviderTest extends TestCase
{
	private RoleRepository&MockObject			$repository;
	private ObjectMapperInterface&MockObject	$mapper;
	private RoleProvider						$provider;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository	= $this->createMock(type: RoleRepository::class);
		$this->mapper		= $this->createMock(type: ObjectMapperInterface::class);
		$this->provider		= new RoleProvider(
			repository:	$this->repository,
			mapper:		$this->mapper,
		);
	}

	#[Test]
	public function testProvideCollectionMapsEveryEntity(): void
	{
		$entities = [
			new RoleEntity(),
			new RoleEntity()
		];
		$first = new RoleResourceDto();
		$second = new RoleResourceDto();

		$this
			->repository
			->expects(self::once())
			->method('findAll')
			->willReturn($entities)
			->seal();

		$this
			->mapper
			->expects(self::exactly(2))
			->method('map')
			->willReturnOnConsecutiveCalls(
				$first,
				$second,
			)
			->seal();

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
			->willReturn([])
			->seal();

		$this
			->mapper
			->expects(self::never())
			->method('map')
			->seal();

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
		$entity		= new RoleEntity();
		$resource	= new RoleResourceDto();

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(3)
			->willReturn($entity)
			->seal();

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->with(
				$entity,
				RoleResourceDto::class,
			)
			->willReturn($resource)
			->seal();

		$result
		   	= $this
				->provider
				->provide(
					operation:		new Get(),
					uriVariables:	['id' => 3],
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
			->willReturn(null)
			->seal();

		$this
			->mapper
			->expects(self::never())
			->method('map')
			->seal();

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
