<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Web;


/// --- Main namespaces --- ///
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;


/// --- Type hint namespaces --- ///
use ReflectionProperty;


/// --- Internal namespaces --- ///
use App\Entity\Abstract\RoleAbstract;
use App\Entity\Abstract\UserAbstract;
use App\Entity\Web\RoleEntity;
use App\Entity\Web\UserEntity;


#[CoversClass(UserEntity::class)]
#[UsesClass(RoleAbstract::class)]
#[UsesClass(RoleEntity::class)]
#[UsesClass(UserAbstract::class)]
class UserEntityTest extends TestCase
{
	#[Test]
	public function testSetRoleIdIsFluentAndStoresValue(): void
	{
		$entity	= new UserEntity();
		$role	= new RoleEntity();

		self::assertSame(
			$entity,
			$entity->setRoleId(roleId: $role),
		);
		self::assertSame(
			$role,
			$entity->getRoleId(),
		);
	}

	#[Test]
	public function testSetNameIsFluentAndStoresValue(): void
	{
		$entity = new UserEntity();

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
	public function testSetAvatarIsFluentAndStoresValue(): void
	{
		$entity = new UserEntity();

		self::assertSame(
			$entity,
			$entity->setAvatar(avatar: 'https://a.ppy.sh/88?88.png'),
		);
		self::assertSame(
			'https://a.ppy.sh/88?88.png',
			$entity->getAvatar(),
		);
	}

	#[Test]
	public function testSetRankIsFluentAndStoresValue(): void
	{
		$entity = new UserEntity();

		self::assertSame(
			$entity,
			$entity->setRank(rank: 88),
		);
		self::assertSame(
			88,
			$entity->getRank(),
		);
	}

	#[Test]
	public function testSetCountryFlagIsFluentAndStoresValue(): void
	{
		$entity = new UserEntity();

		self::assertSame(
			$entity,
			$entity->setCountryFlag(countryFlag: 'ZW'),
		);
		self::assertSame(
			'ZW',
			$entity->getCountryFlag(),
		);
	}

	#[Test]
	public function testInheritsIdentityAndTimestampBehaviour(): void
	{
		$entity = new UserEntity();

		$entity->setId(id: 88888);

		self::assertSame(
			88888,
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
	 * N:1 association with roles
	 */


	#[Test]
	#[CoversNothing]
	public function testRoleAssociationIsManyToOne(): void
	{
		$property = new ReflectionProperty(
			class: UserEntity::class,
			property: 'roleId',
		);
		$attributes = $property->getAttributes(name: ManyToOne::class);

		self::assertCount(
			1,
			$attributes,
			'A user must own a ManyToOne association to its role.',
		);

		$association = $attributes[0]->newInstance();

		self::assertSame(
			'users',
			$association->inversedBy,
		);
	}

	#[Test]
	#[CoversNothing]
	public function testRoleJoinColumnIsRequiredAndNotUnique(): void
	{
		$attributes = (new ReflectionProperty(
			class: UserEntity::class,
			property: 'roleId',
		))->getAttributes(name: JoinColumn::class);

		self::assertCount(
			1,
			$attributes,
		);

		$joinColumn = $attributes[0]->newInstance();

		self::assertSame(
			'role_id',
			$joinColumn->name,
		);
		self::assertFalse($joinColumn->nullable);
		self::assertFalse(
			$joinColumn->unique,
			'A non-unique FK is what makes the relation 1:N.',
		);
	}
}
