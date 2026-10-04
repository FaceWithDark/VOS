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


#[CoversClass(className: RoleEntity::class)]
#[UsesClass(className: UserEntity::class)]
#[UsesClass(className: UserAbstract::class)]
class RoleEntityTest extends TestCase
{
	#[Test]
	public function testDefaultsNameAndDescription(): void
	{
		$entity = new RoleEntity();

		self::assertSame(
			expected: 'User',
			actual: $entity->getName(),
		);
		self::assertSame(
			expected: 'can only interact with what exposed to the website.',
			actual: $entity->getDescription(),
		);
	}

	#[Test]
	public function testSetNameIsFluentAndStoresValue(): void
	{
		$entity = new RoleEntity();

		self::assertSame(
			expected: $entity,
			actual: $entity->setName(name: 'Gambler'),
		);
		self::assertSame(
			expected: 'Gambler',
			actual: $entity->getName(),
		);
	}

	#[Test]
	public function testSetDescriptionIsFluentAndStoresValue(): void
	{
		$entity = new RoleEntity();

		self::assertSame(
			expected: $entity,
			actual: $entity->setDescription(
				description: 'double the pay, double the deal baby.',
			),
		);
		self::assertSame(
			expected: 'double the pay, double the deal baby.',
			actual: $entity->getDescription(),
		);
	}

	#[Test]
	public function testSetDescriptionAcceptsNull(): void
	{
		$entity = new RoleEntity();

		$entity->setDescription(description: 'double the pay, double the deal baby.');
		$entity->setDescription(description: null);

		self::assertNull(
			actual: $entity->getDescription(),
			message: 'Clearing the nullable description must be possible.',
		);
	}

	#[Test]
	public function testInheritsIdentityAndTimestampBehaviour(): void
	{
		$entity = new RoleEntity();

		$entity->setId(id: 3);

		self::assertSame(
			expected: 3,
			actual: $entity->getId(),
		);
		self::assertNotNull(actual: $entity->getCreateOn());
		self::assertSame(
			expected: 'UTC',
			actual: $entity
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
			expectedCount: 1,
			haystack: $attributes,
			message: 'A role must own a OneToMany association to its users.',
		);

		$association = $attributes[0]->newInstance();

		self::assertSame(
			expected: UserEntity::class,
			actual: $association->targetEntity,
		);
		self::assertSame(
			expected: 'roleId',
			actual: $association->mappedBy,
		);
	}

	#[Test]
	public function testConstructorInitialisesUsersAsEmptyCollection(): void
	{
		$entity = new RoleEntity();

		self::assertInstanceOf(
			expected: Collection::class,
			actual: $entity->getUsers(),
		);
		self::assertCount(
			expectedCount: 0,
			haystack: $entity->getUsers(),
		);
	}

	#[Test]
	public function testAddUserKeepsBothSidesOfTheRelationInSync(): void
	{
		$role	= new RoleEntity();
		$user	= new UserEntity();

		self::assertSame(
			expected: $role,
			actual: $role->addUser(user: $user),
		);

		self::assertCount(
			expectedCount: 1,
			haystack: $role->getUsers(),
		);
		self::assertTrue(
			condition: $role
				->getUsers()
				->contains($user),
		);
		self::assertSame(
			expected: $role,
			actual: $user->getRoleId(),
			message: 'Owning side must be updated when a user joins the collection.',
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
			expectedCount: 1,
			haystack: $role->getUsers(),
			message: 'Adding the same user twice must not duplicate it.',
		);
	}

	#[Test]
	public function testRemoveUserDetachesFromCollection(): void
	{
		$role	= new RoleEntity();
		$user	= new UserEntity();

		$role->addUser(user: $user);

		self::assertSame(
			expected: $role,
			actual: $role->removeUser(user: $user),
		);
		self::assertCount(
			expectedCount: 0,
			haystack: $role->getUsers(),
		);
		self::assertFalse(
			condition: $role
				->getUsers()
				->contains($user),
		);
	}
}
