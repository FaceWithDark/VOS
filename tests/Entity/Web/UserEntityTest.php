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


#[CoversClass(className: UserEntity::class)]
class UserEntityTest extends TestCase
{
	#[Test]
	public function testDefaultsEveryFieldToNull(): void
	{
		$entity = new UserEntity();

		self::assertNull(actual: $entity->getRoleId());
		self::assertNull(actual: $entity->getName());
		self::assertNull(actual: $entity->getAvatar());
		self::assertNull(actual: $entity->getRank());
		self::assertNull(actual: $entity->getCountryFlag());
	}

	#[Test]
	public function testSetRoleIdIsFluentAndStoresValue(): void
	{
		$entity	= new UserEntity();
		$role	= new RoleEntity();

		self::assertSame(expected: $entity, actual: $entity->setRoleId(roleId: $role));
		self::assertSame(expected: $role, actual: $entity->getRoleId());
	}

	#[Test]
	public function testSetNameIsFluentAndStoresValue(): void
	{
		$entity = new UserEntity();

		self::assertSame(expected: $entity, actual: $entity->setName(name: 'Gambler'));
		self::assertSame(expected: 'Gambler', actual: $entity->getName());
	}

	#[Test]
	public function testSetAvatarIsFluentAndStoresValue(): void
	{
		$entity = new UserEntity();

		self::assertSame(
			expected: $entity,
			actual: $entity->setAvatar(avatar: 'https://a.ppy.sh/88?88.png'),
		);
		self::assertSame(expected: 'https://a.ppy.sh/88?88.png', actual: $entity->getAvatar());
	}

	#[Test]
	public function testSetRankIsFluentAndStoresValue(): void
	{
		$entity = new UserEntity();

		self::assertSame(expected: $entity, actual: $entity->setRank(rank: 88));
		self::assertSame(expected: 88, actual: $entity->getRank());
	}

	#[Test]
	public function testSetCountryFlagIsFluentAndStoresValue(): void
	{
		$entity = new UserEntity();

		self::assertSame(expected: $entity, actual: $entity->setCountryFlag(countryFlag: 'ZW'));
		self::assertSame(expected: 'ZW', actual: $entity->getCountryFlag());
	}

	#[Test]
	public function testInheritsIdentityAndTimestampBehaviour(): void
	{
		$entity = new UserEntity();

		$entity->setId(id: 88888);

		self::assertSame(expected: 88888, actual: $entity->getId());
		self::assertNotNull(actual: $entity->getCreateOn());
		self::assertSame(
			expected: 'UTC',
			actual: $entity->getCreateOn()->getTimezone()->getName(),
		);
	}
}
