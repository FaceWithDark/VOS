<?php

declare(strict_types=1);

namespace App\Tests\Dto\Main\Web;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ObjectMapper\Attribute\Map;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;
use ReflectionClass;
use ReflectionProperty;


/// --- Internal namespaces --- ///
use App\Dto\Main\Web\UserResourceDto;
use App\Entity\Web\UserEntity;


/**
 * NOTE:
 *
 * This is an output-only DTO: it has public properties and #[Map] attributes
 * that tell API Platform's ObjectMapper how an entity becomes a response body.
 * The tests pin down both the value object behaviour and that mapping contract.
 *
 * `roleId` is flattened from the entity's RoleEntity association, hence the
 * dotted `roleId.id` source path.
 */
#[CoversClass(className: UserResourceDto::class)]
class UserResourceDtoTest extends TestCase
{
	#[Test]
	public function testAssignsEveryField(): void
	{
		$timestamp = new DateTimeImmutable(datetime: '2026-01-01T00:00:00+00:00');

		$dto = new UserResourceDto();

		$dto->id			= 88888;
		$dto->roleId		= 1;
		$dto->name			= 'Gambler';
		$dto->avatar		= 'https://a.ppy.sh/88?88.png';
		$dto->rank			= 88;
		$dto->countryFlag	= 'ZW';
		$dto->timestamp		= $timestamp;

		self::assertSame(expected: 88888, actual: $dto->id);
		self::assertSame(expected: 1, actual: $dto->roleId);
		self::assertSame(expected: 'Gambler', actual: $dto->name);
		self::assertSame(expected: 'https://a.ppy.sh/88?88.png', actual: $dto->avatar);
		self::assertSame(expected: 88, actual: $dto->rank);
		self::assertSame(expected: 'ZW', actual: $dto->countryFlag);
		self::assertSame(expected: $timestamp, actual: $dto->timestamp);
	}

	#[Test]
	public function testClassMapsFromUserEntity(): void
	{
		$attribute = self::classMapAttribute(className: UserResourceDto::class);

		self::assertSame(expected: UserEntity::class, actual: $attribute->source);
	}

	#[Test]
	public function testRoleIdMapsFromTheAssociationId(): void
	{
		$attribute = self::propertyMapAttribute(
			className: UserResourceDto::class,
			propertyName: 'roleId',
		);

		self::assertSame(expected: 'roleId.id', actual: $attribute->source);
	}

	#[Test]
	public function testTimestampMapsFromCreateOn(): void
	{
		$attribute = self::propertyMapAttribute(
			className: UserResourceDto::class,
			propertyName: 'timestamp',
		);

		self::assertSame(expected: 'createOn', actual: $attribute->source);
	}


	/**
	 * Shared reflection helpers
	 */


	private static function classMapAttribute(string $className): Map
	{
		$attributes = (new ReflectionClass(objectOrClass: $className))
			->getAttributes(name: Map::class);

		self::assertCount(
			expectedCount: 1,
			haystack: $attributes,
			message: sprintf('%s must declare exactly one #[Map] on the class.', $className),
		);

		return $attributes[0]->newInstance();
	}

	private static function propertyMapAttribute(
		string	$className,
		string	$propertyName,
	): Map
	{
		$attributes = (new ReflectionProperty(
			class: $className,
			property: $propertyName,
		))->getAttributes(name: Map::class);

		self::assertCount(
			expectedCount: 1,
			haystack: $attributes,
			message: sprintf('%s::$%s must declare exactly one #[Map].', $className, $propertyName),
		);

		return $attributes[0]->newInstance();
	}
}
