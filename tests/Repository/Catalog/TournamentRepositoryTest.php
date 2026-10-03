<?php

declare(strict_types=1);

namespace App\Tests\Repository\Catalog;


/// --- Main namespaces --- ///
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Entity\Catalog\TournamentEntity;
use App\Repository\Catalog\TournamentRepository;


/**
 * NOTE:
 *
 * {@see TournamentRepository} only wraps the Doctrine EntityManager, so the
 * write methods are tested against a mocked manager instead of a live database.
 * `ServiceEntityRepository` lazily resolves its inner repository, which means
 * the mocked manager must also answer `getClassMetadata()`.
 */
#[CoversClass(className: TournamentRepository::class)]
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

		$this->repository->remove(
			entity: $entity,
			flush: false,
		);
	}
}
