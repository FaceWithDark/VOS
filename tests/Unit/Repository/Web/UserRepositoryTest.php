<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository\Web;


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
use App\Entity\Abstract\UserAbstract;
use App\Entity\Web\UserEntity;
use App\Repository\Web\UserRepository;


/**
 * NOTE:
 *
 * {@see UserRepository} only wraps the Doctrine EntityManager, so the write
 * methods are tested against a mocked manager instead of a live database.
 * `ServiceEntityRepository` lazily resolves its inner repository, which means
 * the mocked manager must also answer `getClassMetadata()`.
 *
 * The manager is sealed once a test has declared every interaction it expects:
 * PHPUnit then turns any undeclared call into a failure instead of a silent
 * `null`, and `#[UsesClass]` accounts for the entity code instantiated here.
 */
#[CoversClass(className: UserRepository::class)]
#[UsesClass(className: UserEntity::class)]
#[UsesClass(className: UserAbstract::class)]
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

		$this->sealEntityManager();

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

		$this->sealEntityManager();

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

		$this->sealEntityManager();

		$this->repository->remove(
			entity: $entity,
			flush: false,
		);
	}
}
