<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository\Web;

// @mago-format-ignore-start


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
use App\Entity\Abstract\RoleAbstract;
use App\Entity\Web\RoleEntity;
use App\Repository\Web\RoleRepository;


// @mago-format-ignore-end
/**
 * NOTE:
 *
 * {@see RoleRepository} only wraps the Doctrine EntityManager, so the write
 * methods are tested against a mocked manager instead of a live database.
 * `ServiceEntityRepository` lazily resolves its inner repository, which means
 * the mocked manager must also answer `getClassMetadata()`.
 *
 * The manager is sealed once a test has declared every interaction it expects:
 * PHPUnit then turns any undeclared call into a failure instead of a silent
 * `null`, and `#[UsesClass]` accounts for the entity code instantiated here.
 */
#[CoversClass(RoleRepository::class)]
#[UsesClass(RoleEntity::class)]
#[UsesClass(RoleAbstract::class)]
class RoleRepositoryTest extends TestCase
{
	/** Entity manager. */
	private EntityManagerInterface&MockObject $entityManager;
	/** Repository. */
	private RoleRepository $repository;

	/**
	 * remove()
	 */

	#[Test]
	/** @mago-expect lint:literal-named-argument(2) */
	public function testRemoveRemovesAndFlushesByDefault(): void
	{
		$entity = new RoleEntity();

		$this->entityManager->expects(self::once())->method('remove')->with($entity);

		$this->entityManager->expects(self::once())->method('flush');

		$this->sealEntityManager();

		$this->repository->remove(entity: $entity);
	}

	/**
	 * save()
	 */

	#[Test]
	/** @mago-expect lint:literal-named-argument(2) */
	public function testSavePersistsAndFlushesByDefault(): void
	{
		$entity = new RoleEntity();

		$this->entityManager->expects(self::once())->method('persist')->with($entity);

		$this->entityManager->expects(self::once())->method('flush');

		$this->sealEntityManager();

		$this->repository->save(entity: $entity);
	}

	/**
	 * Prepares the test fixtures.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		$this->entityManager = $this->createMock(EntityManagerInterface::class);
		$this->entityManager->method('getClassMetadata')->willReturn(new ClassMetadata(name: RoleEntity::class));

		$registry = self::createStub(ManagerRegistry::class);
		$registry->method('getManagerForClass')->willReturn($this->entityManager);

		$this->repository = new RoleRepository(registry: $registry);
	}

	/**
	 * Seal entity manager.
	 * @mago-expect lint:literal-named-argument
	 */
	private function sealEntityManager(): void
	{
		$this->entityManager->method('getClassMetadata')->seal();
	}
}
