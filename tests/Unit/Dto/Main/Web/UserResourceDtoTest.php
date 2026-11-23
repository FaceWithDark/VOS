<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto\Main\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ObjectMapper\Attribute\Map;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;
use function sprintf;
use ReflectionClass;
use ReflectionProperty;


/// --- Internal namespaces --- ///
use App\Dto\Main\Web\UserResourceDto;
use App\Entity\Web\UserEntity;


// @mago-format-ignore-end
/**
 * NOTE:
 *
 * This is an output-only DTO: it has public properties and #[Map] attributes
 * that tell API Platform's ObjectMapper how an entity becomes a response body.
 * The tests pin down both the value object behaviour and that mapping contract.
 *
 * `roleId` is flattened from the entity's RoleEntity association, hence the
 * dotted `roleId.id` source path.
 *
 * The DTO declares no executable lines (only typed properties and attributes),
 * so PHPUnit has nothing to attribute coverage to: the class is covered, it
 * simply cannot contribute. `#[CoversNothing]` states that explicitly instead
 * of leaving PHPUnit to flag every test as risky.
 */
#[CoversNothing]
class UserResourceDtoTest extends TestCase
{
	/**
	 * Shared reflection helpers
	 */

	/**
	 * @param class-string $className
	 * @mago-expect lint:literal-named-argument
	 */
	private static function classMapAttribute(string $className): Map
	{
		$attributes = new ReflectionClass(objectOrClass: $className)->getAttributes(name: Map::class);

		self::assertCount(
			1,
			$attributes,
			sprintf(
				'%s must declare exactly one #[Map] on the class.',
				$className,
			),
		);

		return $attributes[0]->newInstance();
	}

	/**
	 * @param class-string $className
	 * @mago-expect lint:literal-named-argument
	 */
	private static function propertyMapAttribute(
		string $className,
		string $propertyName,
	): Map {
		$attributes = new ReflectionProperty(
			class: $className,
			property: $propertyName,
		)->getAttributes(name: Map::class);

		self::assertCount(
			1,
			$attributes,
			sprintf(
				'%s::$%s must declare exactly one #[Map].',
				$className,
				$propertyName,
			),
		);

		return $attributes[0]->newInstance();
	}

	/**
	 * Covers: test assigns every field.
	 * @mago-expect lint:literal-named-argument(6)
	 */
	#[Test]
	public function testAssignsEveryField(): void
	{
		$timestamp = new DateTimeImmutable(datetime: '2026-01-01T00:00:00+00:00');

		$dto = new UserResourceDto();

		$dto->id          = 88_888;
		$dto->roleId      = 1;
		$dto->name        = 'Gambler';
		$dto->avatar      = 'https://a.ppy.sh/88?88.png';
		$dto->rank        = 88;
		$dto->countryFlag = 'ZW';
		$dto->timestamp   = $timestamp;

		self::assertSame(
			88_888,
			$dto->id,
		);
		self::assertSame(
			1,
			$dto->roleId,
		);
		self::assertSame(
			'Gambler',
			$dto->name,
		);
		self::assertSame(
			'https://a.ppy.sh/88?88.png',
			$dto->avatar,
		);
		self::assertSame(
			88,
			$dto->rank,
		);
		self::assertSame(
			'ZW',
			$dto->countryFlag,
		);
		self::assertSame(
			$timestamp,
			$dto->timestamp,
		);
	}

	/** Covers: test class maps from user entity. */
	#[Test]
	public function testClassMapsFromUserEntity(): void
	{
		$attribute = self::classMapAttribute(className: UserResourceDto::class);

		self::assertSame(
			UserEntity::class,
			$attribute->source,
		);
	}

	/**
	 * Covers: test role id maps from the association id.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testRoleIdMapsFromTheAssociationId(): void
	{
		$attribute = self::propertyMapAttribute(
			className: UserResourceDto::class,
			propertyName: 'roleId',
		);

		self::assertSame(
			'roleId.id',
			$attribute->source,
		);
	}

	/**
	 * Covers: test timestamp maps from create on.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testTimestampMapsFromCreateOn(): void
	{
		$attribute = self::propertyMapAttribute(
			className: UserResourceDto::class,
			propertyName: 'timestamp',
		);

		self::assertSame(
			'createOn',
			$attribute->source,
		);
	}
}
