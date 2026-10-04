<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository\Catalog;


/// --- Main namespaces --- ///
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Entity\Abstract\TournamentAbstract;
use App\Entity\Catalog\TournamentEntity;
use App\Repository\Catalog\TournamentRepository;


/**
 * NOTE:
 *
 * {@see TournamentRepository} only wraps the Doctrine EntityManager, so the
 * write methods are tested against a mocked manager instead of a live database.
 * `ServiceEntityRepository` lazily resolves its inner repository, which means
 * the mocked manager must also answer `getClassMetadata()`.
 *
 * The manager is sealed once a test has declared every interaction it expects:
 * PHPUnit then turns any undeclared call into a failure instead of a silent
 * `null`, and `#[UsesClass]` accounts for the entity code instantiated here.
 */
#[CoversClass(className: TournamentRepository::class)]
#[UsesClass(className: TournamentEntity::class)]
#[UsesClass(className: TournamentAbstract::class)]
class TournamentRepositoryTest extends TestCase
{
	private EntityManagerInterface&MockObject	$entityManager;
	private TournamentRepository				$repository;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		$this->entityManager = $this->createMock(type: EntityManagerInterface::class);
		$this->entityManager
			->method('getClassMetadata')
			->willReturn(new ClassMetadata(name: TournamentEntity::class));

		$registry = $this->createStub(type: ManagerRegistry::class);
		$registry
			->method('getManagerForClass')
			->willReturn($this->entityManager);

		$this->repository = new TournamentRepository(registry: $registry);
	}

	private function sealEntityManager(): void
	{
		$this
			->entityManager
			->method('getClassMetadata')
			->seal();
	}


	/**
	 * save()
	 */


	#[Test]
	public function testSavePersistsAndFlushesByDefault(): void
	{
		$entity = new TournamentEntity();

		$this
			->entityManager
			->expects(self::once())
			->method('persist')
			->with($entity);

		$this
			->entityManager
			->expects(self::once())
			->method('flush');

		$this->sealEntityManager();

		$this->repository->save(entity: $entity);
	}

	#[Test]
	public function testSaveWithoutFlushSkipsFlush(): void
	{
		$entity = new TournamentEntity();

		$this
			->entityManager
			->expects(self::once())
			->method('persist')
			->with($entity);

		$this
			->entityManager
			->expects(self::never())
			->method('flush');

		$this->sealEntityManager();

		$this->repository->save(
			entity: $entity,
			flush: false,
		);
	}


	/**
	 * remove()
	 */


	#[Test]
	public function testRemoveRemovesAndFlushesByDefault(): void
	{
		$entity = new TournamentEntity();

		$this
			->entityManager
			->expects(self::once())
			->method('remove')
			->with($entity);

		$this
			->entityManager
			->expects(self::once())
			->method('flush');

		$this->sealEntityManager();

		$this->repository->remove(entity: $entity);
	}

	#[Test]
	public function testRemoveWithoutFlushSkipsFlush(): void
	{
		$entity = new TournamentEntity();

		$this
			->entityManager
			->expects(self::once())
			->method('remove')
			->with($entity);

		$this
			->entityManager
			->expects(self::never())
			->method('flush');

		$this->sealEntityManager();

		$this->repository->remove(
			entity: $entity,
			flush: false,
		);
	}
}
