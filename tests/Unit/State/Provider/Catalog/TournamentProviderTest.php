<?php

declare(strict_types=1);

namespace App\Tests\Unit\State\Provider\Catalog;


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
use App\Dto\Main\Catalog\TournamentResourceDto;
use App\Entity\Abstract\TournamentAbstract;
use App\Entity\Catalog\TournamentEntity;
use App\Repository\Catalog\TournamentRepository;
use App\State\Provider\Catalog\TournamentProvider;


#[CoversClass(className: TournamentProvider::class)]
#[UsesClass(className: TournamentAbstract::class)]
class TournamentProviderTest extends TestCase
{
	private TournamentRepository&MockObject		$repository;
	private ObjectMapperInterface&MockObject	$mapper;
	private TournamentProvider					$provider;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository	= $this->createMock(type: TournamentRepository::class);
		$this->mapper		= $this->createMock(type: ObjectMapperInterface::class);
		$this->provider		= new TournamentProvider(
			repository:	$this->repository,
			mapper:		$this->mapper,
		);
	}

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
			new TournamentEntity(),
			new TournamentEntity()
		];
		$first = new TournamentResourceDto();
		$second = new TournamentResourceDto();

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
		$entity		= new TournamentEntity();
		$resource	= new TournamentResourceDto();

		$this
			->repository
			->expects(self::once())
			->method('find')
			->with(7)
			->willReturn($entity);

		$this
			->mapper
			->expects(self::once())
			->method('map')
			->with(
				$entity,
				TournamentResourceDto::class,
			)
			->willReturn($resource);

		$this->sealCollaborators();

		$result
		   	= $this
				->provider
				->provide(
					operation:		new Get(),
					uriVariables:	['id' => 7],
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

		$result = $this
				->provider
				->provide(
					operation:		new Get(),
					uriVariables:	['id' => 404],
				);

		self::assertNull(actual: $result);
	}
}
