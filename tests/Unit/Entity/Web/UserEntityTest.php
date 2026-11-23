<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Web;

// @mago-format-ignore-start


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


// @mago-format-ignore-end
#[CoversClass(UserEntity::class)]
#[UsesClass(RoleAbstract::class)]
#[UsesClass(RoleEntity::class)]
#[UsesClass(UserAbstract::class)]
class UserEntityTest extends TestCase
{
	/**
	 * Covers: test inherits identity and timestamp behaviour.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testInheritsIdentityAndTimestampBehaviour(): void
	{
		$entity = new UserEntity();

		$entity->setId(id: 88_888);

		self::assertSame(
			88_888,
			$entity->getId(),
		);
		self::assertNotNull($entity->getCreateOn());
		self::assertSame(
			'UTC',
			$entity->getCreateOn()->getTimezone()->getName(),
		);
	}

	/**
	 * N:1 association with roles
	 */

	#[Test]
	#[CoversNothing]
	/** @mago-expect lint:literal-named-argument(3) */
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

	/**
	 * Covers: test role join column is required and not unique.
	 * @mago-expect lint:literal-named-argument(3)
	 */
	#[Test]
	#[CoversNothing]
	public function testRoleJoinColumnIsRequiredAndNotUnique(): void
	{
		$attributes = new ReflectionProperty(
			class: UserEntity::class,
			property: 'roleId',
		)->getAttributes(name: JoinColumn::class);

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

	/**
	 * Covers: test set avatar is fluent and stores value.
	 * @mago-expect lint:literal-named-argument
	 */
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

	/**
	 * Covers: test set country flag is fluent and stores value.
	 * @mago-expect lint:literal-named-argument
	 */
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

	/**
	 * Covers: test set name is fluent and stores value.
	 * @mago-expect lint:literal-named-argument
	 */
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

	/**
	 * Covers: test set rank is fluent and stores value.
	 * @mago-expect lint:literal-named-argument
	 */
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

	/** Covers: test set role id is fluent and stores value. */
	#[Test]
	public function testSetRoleIdIsFluentAndStoresValue(): void
	{
		$entity = new UserEntity();
		$role   = new RoleEntity();

		self::assertSame(
			$entity,
			$entity->setRoleId(roleId: $role),
		);
		self::assertSame(
			$role,
			$entity->getRoleId(),
		);
	}
}
