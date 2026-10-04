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
use App\Entity\Web\UserEntity;
use App\Repository\Web\UserRepository;


/**
 * NOTE:
 *
 * {@see UserRepository} only wraps the Doctrine EntityManager, so the write
 * methods are tested against a mocked manager instead of a live database.
 * `ServiceEntityRepository` lazily resolves its inner repository, which means
 * the mocked manager must also answer `getClassMetadata()`.
 */
#[CoversClass(className: UserRepository::class)]
class UserRepositoryTest extends TestCase
{
	private EntityManagerInterface&MockObject	$entityManager;
	private UserRepository						$repository;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		$this->entityManager = $this->createMock(type: EntityManagerInterface::class);
		$this->entityManager
			->method('getClassMetadata')
			->willReturn(new ClassMetadata(name: UserEntity::class));

		$registry = $this->createStub(type: ManagerRegistry::class);
		$registry
			->method('getManagerForClass')
			->willReturn($this->entityManager);

		$this->repository = new UserRepository(registry: $registry);
	}


	/**
	 * save()
	 */


	#[Test]
	public function testSavePersistsAndFlushesByDefault(): void
	{
		$entity = new UserEntity();

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
		$entity = new UserEntity();

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
		$entity = new UserEntity();

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
		$entity = new UserEntity();

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
