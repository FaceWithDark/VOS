<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Web;


/// --- Main namespaces --- ///
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\OneToMany;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;


/// --- Type hint namespaces --- ///
use ReflectionProperty;


/// --- Internal namespaces --- ///
use App\Entity\Abstract\UserAbstract;
use App\Entity\Web\RoleEntity;
use App\Entity\Web\UserEntity;


#[CoversClass(RoleEntity::class)]
#[UsesClass(UserEntity::class)]
#[UsesClass(UserAbstract::class)]
class RoleEntityTest extends TestCase
{
	#[Test]
	public function testDefaultsNameAndDescription(): void
	{
		$entity = new RoleEntity();

		self::assertSame(
			'User',
			$entity->getName(),
		);
		self::assertSame(
			'can only interact with what exposed to the website.',
			$entity->getDescription(),
		);
	}

	#[Test]
	public function testSetNameIsFluentAndStoresValue(): void
	{
		$entity = new RoleEntity();

		self::assertSame(
			$entity,
			$entity->setName(name: 'Gambler'),
		);
		self::assertSame(
			'Gambler',
			$entity->getName(),
		);
	}

	#[Test]
	public function testSetDescriptionIsFluentAndStoresValue(): void
	{
		$entity = new RoleEntity();

		self::assertSame(
			$entity,
			$entity->setDescription(
				description: 'double the pay, double the deal baby.',
			),
		);
		self::assertSame(
			'double the pay, double the deal baby.',
			$entity->getDescription(),
		);
	}

	#[Test]
	public function testSetDescriptionAcceptsNull(): void
	{
		$entity = new RoleEntity();

		$entity->setDescription(description: 'double the pay, double the deal baby.');
		$entity->setDescription(description: null);

		self::assertNull(
			$entity->getDescription(),
			'Clearing the nullable description must be possible.',
		);
	}

	#[Test]
	public function testInheritsIdentityAndTimestampBehaviour(): void
	{
		$entity = new RoleEntity();

		$entity->setId(id: 3);

		self::assertSame(
			3,
			$entity->getId(),
		);
		self::assertNotNull($entity->getCreateOn());
		self::assertSame(
			'UTC',
			$entity
				->getCreateOn()
				->getTimezone()
				->getName(),
		);
	}


	/**
	 * 1:N association with users
	 */


	#[Test]
	public function testUsersAssociationIsOneToMany(): void
	{
		// Instantiate the mapped class so this test also executes the entity it
		// introspects (required by `requireCoverageContribution`).
		$entity = new RoleEntity();

		$attributes = (new ReflectionProperty(
			class: $entity::class,
			property: 'users',
		))->getAttributes(name: OneToMany::class);

		self::assertCount(
			1,
			$attributes,
			'A role must own a OneToMany association to its users.',
		);

		$association = $attributes[0]->newInstance();

		self::assertSame(
			UserEntity::class,
			$association->targetEntity,
		);
		self::assertSame(
			'roleId',
			$association->mappedBy,
		);
	}

	#[Test]
	public function testConstructorInitialisesUsersAsEmptyCollection(): void
	{
		$entity = new RoleEntity();

		self::assertInstanceOf(
			Collection::class,
			$entity->getUsers(),
		);
		self::assertCount(
			0,
			$entity->getUsers(),
		);
	}

	#[Test]
	public function testAddUserKeepsBothSidesOfTheRelationInSync(): void
	{
		$role	= new RoleEntity();
		$user	= new UserEntity();

		self::assertSame(
			$role,
			$role->addUser(user: $user),
		);

		self::assertCount(
			1,
			$role->getUsers(),
		);
		self::assertTrue(
			$role
				->getUsers()
				->contains($user),
		);
		self::assertSame(
			$role,
			$user->getRoleId(),
			'Owning side must be updated when a user joins the collection.',
		);
	}

	#[Test]
	public function testAddUserIsIdempotent(): void
	{
		$role	= new RoleEntity();
		$user	= new UserEntity();

		$role->addUser(user: $user);
		$role->addUser(user: $user);

		self::assertCount(
			1,
			$role->getUsers(),
			'Adding the same user twice must not duplicate it.',
		);
	}

	#[Test]
	public function testRemoveUserDetachesFromCollection(): void
	{
		$role	= new RoleEntity();
		$user	= new UserEntity();

		$role->addUser(user: $user);

		self::assertSame(
			$role,
			$role->removeUser(user: $user),
		);
		self::assertCount(
			0,
			$role->getUsers(),
		);
		self::assertFalse(
			$role
				->getUsers()
				->contains($user),
		);
	}
}
