<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto\Main\Catalog;

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
use App\Dto\Main\Catalog\TournamentResourceDto;
use App\Entity\Catalog\TournamentEntity;


// @mago-format-ignore-end
/**
 * NOTE:
 *
 * This is an output-only DTO: it has public properties and #[Map] attributes
 * that tell API Platform's ObjectMapper how an entity becomes a response body.
 * The tests pin down both the value object behaviour and that mapping contract.
 *
 * The DTO declares no executable lines (only typed properties and attributes),
 * so PHPUnit has nothing to attribute coverage to: the class is covered, it
 * simply cannot contribute. `#[CoversNothing]` states that explicitly instead
 * of leaving PHPUnit to flag every test as risky.
 */
#[CoversNothing]
class TournamentResourceDtoTest extends TestCase
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
	 * @mago-expect lint:literal-named-argument(3)
	 */
	#[Test]
	public function testAssignsEveryField(): void
	{
		$timestamp = new DateTimeImmutable(datetime: '2026-01-01T00:00:00+00:00');

		$dto = new TournamentResourceDto();

		$dto->id          = 7;
		$dto->name        = 'VOT88';
		$dto->description = 'Vietnamese Osu!taiko Tournament 88 (special edition).';
		$dto->timestamp   = $timestamp;

		self::assertSame(
			7,
			$dto->id,
		);
		self::assertSame(
			'VOT88',
			$dto->name,
		);
		self::assertSame(
			'Vietnamese Osu!taiko Tournament 88 (special edition).',
			$dto->description,
		);
		self::assertSame(
			$timestamp,
			$dto->timestamp,
		);
	}

	/** Covers: test class maps from tournament entity. */
	#[Test]
	public function testClassMapsFromTournamentEntity(): void
	{
		$attribute = self::classMapAttribute(className: TournamentResourceDto::class);

		self::assertSame(
			TournamentEntity::class,
			$attribute->source,
		);
	}

	/** Covers: test defaults description to null. */
	#[Test]
	public function testDefaultsDescriptionToNull(): void
	{
		$dto = new TournamentResourceDto();

		self::assertNull($dto->description);
	}

	/**
	 * Covers: test timestamp maps from create on.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testTimestampMapsFromCreateOn(): void
	{
		$attribute = self::propertyMapAttribute(
			className: TournamentResourceDto::class,
			propertyName: 'timestamp',
		);

		self::assertSame(
			'createOn',
			$attribute->source,
		);
	}
}
