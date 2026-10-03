<?php

declare(strict_types=1);

namespace App\Tests\Repository\Web;


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
use App\Entity\Web\RoleEntity;
use App\Repository\Web\RoleRepository;


/**
 * NOTE:
 *
 * {@see RoleRepository} only wraps the Doctrine EntityManager, so the write
 * methods are tested against a mocked manager instead of a live database.
 * `ServiceEntityRepository` lazily resolves its inner repository, which means
 * the mocked manager must also answer `getClassMetadata()`.
 */
#[CoversClass(className: RoleRepository::class)]
class RoleRepositoryTest extends TestCase
{
	private EntityManagerInterface&MockObject	$entityManager;
	private RoleRepository						$repository;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		$this->entityManager = $this->createMock(type: EntityManagerInterface::class);
		$this->entityManager
			->method('getClassMetadata')
			->willReturn(new ClassMetadata(name: RoleEntity::class));

		$registry = $this->createStub(type: ManagerRegistry::class);
		$registry
			->method('getManagerForClass')
			->willReturn($this->entityManager);

		$this->repository = new RoleRepository(registry: $registry);
	}


	/**
	 * save()
	 */


	#[Test]
	public function testSavePersistsAndFlushesByDefault(): void
	{
		$entity = new RoleEntity();

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
		$entity = new RoleEntity();

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
		$entity = new RoleEntity();

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
		$entity = new RoleEntity();

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
