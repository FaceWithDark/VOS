<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Web;

// @mago-format-ignore-start


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


// @mago-format-ignore-end
#[CoversClass(RoleEntity::class)]
#[UsesClass(UserEntity::class)]
#[UsesClass(UserAbstract::class)]
class RoleEntityTest extends TestCase
{
	/**
	 * Covers: test add user is idempotent.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testAddUserIsIdempotent(): void
	{
		$role = new RoleEntity();
		$user = new UserEntity();

		$role->addUser(user: $user);
		$role->addUser(user: $user);

		self::assertCount(
			1,
			$role->getUsers(),
			'Adding the same user twice must not duplicate it.',
		);
	}

	/**
	 * Covers: test add user keeps both sides of the relation in sync.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testAddUserKeepsBothSidesOfTheRelationInSync(): void
	{
		$role = new RoleEntity();
		$user = new UserEntity();

		self::assertSame(
			$role,
			$role->addUser(user: $user),
		);

		self::assertCount(
			1,
			$role->getUsers(),
		);
		self::assertTrue(
			$role->getUsers()->contains($user),
		);
		self::assertSame(
			$role,
			$user->getRoleId(),
			'Owning side must be updated when a user joins the collection.',
		);
	}

	/**
	 * Covers: test constructor initialises users as empty collection.
	 * @mago-expect lint:literal-named-argument
	 */
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

	/**
	 * Covers: test defaults name and description.
	 * @mago-expect lint:literal-named-argument(2)
	 */
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

	/**
	 * Covers: test inherits identity and timestamp behaviour.
	 * @mago-expect lint:literal-named-argument(2)
	 */
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
			$entity->getCreateOn()->getTimezone()->getName(),
		);
	}

	/**
	 * Covers: test remove user detaches from collection.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testRemoveUserDetachesFromCollection(): void
	{
		$role = new RoleEntity();
		$user = new UserEntity();

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
			$role->getUsers()->contains($user),
		);
	}

	/**
	 * Covers: test set description accepts null.
	 * @mago-expect lint:literal-named-argument
	 */
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

	/**
	 * Covers: test set description is fluent and stores value.
	 * @mago-expect lint:literal-named-argument
	 */
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

	/**
	 * Covers: test set name is fluent and stores value.
	 * @mago-expect lint:literal-named-argument
	 */
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

	/**
	 * 1:N association with users
	 */

	#[Test]
	/** @mago-expect lint:literal-named-argument(3) */
	public function testUsersAssociationIsOneToMany(): void
	{
		// Instantiate the mapped class so this test also executes the entity it
		// introspects (required by `requireCoverageContribution`).
		$entity = new RoleEntity();

		$attributes = new ReflectionProperty(
			class: $entity::class,
			property: 'users',
		)->getAttributes(name: OneToMany::class);

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
}
