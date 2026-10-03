<?php

declare(strict_types=1);

namespace App\Tests\Entity\Catalog;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Catalog\TournamentEntity;


#[CoversClass(className: TournamentEntity::class)]
class TournamentEntityTest extends TestCase
{
	#[Test]
	public function testDefaultsNameAndDescriptionToNull(): void
	{
		$entity = new TournamentEntity();

		self::assertNull(actual: $entity->getName());
		self::assertNull(actual: $entity->getDescription());
	}

	#[Test]
	public function testSetNameIsFluentAndStoresValue(): void
	{
		$entity = new TournamentEntity();

		self::assertSame(expected: $entity, actual: $entity->setName(name: 'VOT88'));
		self::assertSame(expected: 'VOT88', actual: $entity->getName());
	}

	#[Test]
	public function testSetDescriptionIsFluentAndStoresValue(): void
	{
		$entity = new TournamentEntity();

		self::assertSame(
			expected: $entity,
			actual: $entity->setDescription(
				description: 'Vietnamese Osu!taiko Tournament 88 (special edition).',
			),
		);
		self::assertSame(
			expected: 'Vietnamese Osu!taiko Tournament 88 (special edition).',
			actual: $entity->getDescription(),
		);
	}

	#[Test]
	public function testSetDescriptionAcceptsNull(): void
	{
		$entity = new TournamentEntity();

		$entity->setDescription(description: 'Vietnamese Osu!taiko Tournament 88 (special edition).');
		$entity->setDescription(description: null);

		self::assertNull(
			actual: $entity->getDescription(),
			message: 'Clearing the nullable description must be possible.',
		);
	}

	#[Test]
	public function testInheritsIdentityAndTimestampBehaviour(): void
	{
		$entity = new TournamentEntity();

		$entity->setId(id: 7);

		self::assertSame(expected: 7, actual: $entity->getId());
		self::assertNotNull(actual: $entity->getCreateOn());
		self::assertSame(
			expected: 'UTC',
			actual: $entity->getCreateOn()->getTimezone()->getName(),
		);
	}
}
