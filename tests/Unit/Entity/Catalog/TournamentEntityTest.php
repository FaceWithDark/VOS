<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Catalog;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Catalog\TournamentEntity;


#[CoversClass(TournamentEntity::class)]
class TournamentEntityTest extends TestCase
{
	#[Test]
	public function testDefaultsDescriptionToNull(): void
	{
		$entity = new TournamentEntity();

		self::assertNull($entity->getDescription());
	}

	#[Test]
	public function testSetNameIsFluentAndStoresValue(): void
	{
		$entity = new TournamentEntity();

		self::assertSame(
			$entity,
			$entity->setName(name: 'VOT88'),
		);
		self::assertSame(
			'VOT88',
			$entity->getName(),
		);
	}

	#[Test]
	public function testSetDescriptionIsFluentAndStoresValue(): void
	{
		$entity = new TournamentEntity();

		self::assertSame(
			$entity,
			$entity->setDescription(
				description: 'Vietnamese Osu!taiko Tournament 88 (special edition).',
			),
		);
		self::assertSame(
			'Vietnamese Osu!taiko Tournament 88 (special edition).',
			$entity->getDescription(),
		);
	}

	#[Test]
	public function testSetDescriptionAcceptsNull(): void
	{
		$entity = new TournamentEntity();

		$entity->setDescription(description: 'Vietnamese Osu!taiko Tournament 88 (special edition).');
		$entity->setDescription(description: null);

		self::assertNull(
			$entity->getDescription(),
			'Clearing the nullable description must be possible.',
		);
	}

	#[Test]
	public function testInheritsIdentityAndTimestampBehaviour(): void
	{
		$entity = new TournamentEntity();

		$entity->setId(id: 7);

		self::assertSame(
			7,
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
}
