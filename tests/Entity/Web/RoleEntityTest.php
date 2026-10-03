<?php

declare(strict_types=1);

namespace App\Tests\Entity\Web;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Web\RoleEntity;
use App\Entity\Web\UserEntity;


#[CoversClass(className: RoleEntity::class)]
class RoleEntityTest extends TestCase
{
	#[Test]
	public function testDefaultsNameAndDescription(): void
	{
		$entity = new RoleEntity();

		self::assertSame(expected: 'User', actual: $entity->getName());
		self::assertSame(
			expected: 'can only interact with what exposed to the website.',
			actual: $entity->getDescription(),
		);
	}

	#[Test]
	public function testSetNameIsFluentAndStoresValue(): void
	{
		$entity = new RoleEntity();

		self::assertSame(expected: $entity, actual: $entity->setName(name: 'Gambler'));
		self::assertSame(expected: 'Gambler', actual: $entity->getName());
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
	public function testSetUsersKeepsBothSidesOfTheRelationInSync(): void
	{
		$role	= new RoleEntity();
		$user	= new UserEntity();

		$role->setUsers(users: $user);

		self::assertSame(expected: $user, actual: $role->getUsers());
		self::assertSame(
			expected: $role,
			actual: $user->getRoleId(),
			message: 'Owning side must be updated when the inverse side is set.',
		);
	}

	#[Test]
	public function testSetUsersDoesNotRewriteAnAlreadyCorrectOwner(): void
	{
		$role	= new RoleEntity();
		$user	= new UserEntity();

		$user->setRoleId(roleId: $role);
		$role->setUsers(users: $user);

		self::assertSame(expected: $role, actual: $user->getRoleId());
		self::assertSame(expected: $user, actual: $role->getUsers());
	}

	#[Test]
	public function testInheritsIdentityAndTimestampBehaviour(): void
	{
		$entity = new RoleEntity();

		$entity->setId(id: 3);

		self::assertSame(expected: 3, actual: $entity->getId());
		self::assertNotNull(actual: $entity->getCreateOn());
		self::assertSame(
			expected: 'UTC',
			actual: $entity->getCreateOn()->getTimezone()->getName(),
		);
	}
}
