<?php

declare(strict_types=1);

namespace App\Tests\Dto\Main\Catalog;


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
use App\Dto\Main\Catalog\TournamentResourceDto;
use App\Entity\Catalog\TournamentEntity;


/**
 * NOTE:
 *
 * This is an output-only DTO: it has public properties and #[Map] attributes
 * that tell API Platform's ObjectMapper how an entity becomes a response body.
 * The tests pin down both the value object behaviour and that mapping contract.
 */
#[CoversClass(className: TournamentResourceDto::class)]
class TournamentResourceDtoTest extends TestCase
{
	#[Test]
	public function testDefaultsDescriptionToNull(): void
	{
		$dto = new TournamentResourceDto();

		self::assertNull(actual: $dto->description);
	}

	#[Test]
	public function testAssignsEveryField(): void
	{
		$timestamp = new DateTimeImmutable(datetime: '2026-01-01T00:00:00+00:00');

		$dto = new TournamentResourceDto();

		$dto->id			= 7;
		$dto->name			= 'VOT88';
		$dto->description	= 'Vietnamese Osu!taiko Tournament 88 (special edition).';
		$dto->timestamp		= $timestamp;

		self::assertSame(expected: 7, actual: $dto->id);
		self::assertSame(expected: 'VOT88', actual: $dto->name);
		self::assertSame(
			expected: 'Vietnamese Osu!taiko Tournament 88 (special edition).',
			actual: $dto->description,
		);
		self::assertSame(expected: $timestamp, actual: $dto->timestamp);
	}

	#[Test]
	public function testClassMapsFromTournamentEntity(): void
	{
		$attribute = self::classMapAttribute(className: TournamentResourceDto::class);

		self::assertSame(
			expected: TournamentEntity::class,
			actual: $attribute->source,
		);
	}

	#[Test]
	public function testTimestampMapsFromCreateOn(): void
	{
		$attribute = self::propertyMapAttribute(
			className: TournamentResourceDto::class,
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
