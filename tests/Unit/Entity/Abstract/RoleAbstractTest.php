<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Abstract;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;
use DateTimeZone;


/// --- Internal namespaces --- ///
use App\Entity\Abstract\RoleAbstract;


/**
 * NOTE:
 *
 * {@see RoleAbstract} is a MappedSuperclass shared by every web role entity.
 * The anonymous subclass lets the test instantiate the abstract directly
 * without dragging a concrete entity into the subject.
 */
#[CoversClass(RoleAbstract::class)]
class RoleAbstractTest extends TestCase
{
	private function createSubject(): RoleAbstract
	{
		return new class extends RoleAbstract {};
	}

	#[Test]
	public function testDefaultsIdToNull(): void
	{
		$subject = $this->createSubject();

		self::assertNull(
			$subject->getId(),
			'A not-yet-persisted entity has no identifier.',
		);
	}

	#[Test]
	public function testSetIdIsFluentAndStoresValue(): void
	{
		$subject = $this->createSubject();

		self::assertSame(
			$subject,
			$subject->setId(id: 3),
			'The setter must support a fluent chain.',
		);
		self::assertSame(
			3,
			$subject->getId(),
		);
	}

	#[Test]
	public function testConstructorInitialisesCreateOnInUtc(): void
	{
		$before = new DateTimeImmutable(
			datetime: 'now',
			timezone: new DateTimeZone(timezone: 'UTC'),
		);
		$subject = $this->createSubject();
		$after = new DateTimeImmutable(
			datetime: 'now',
			timezone: new DateTimeZone(timezone: 'UTC'),
		);

		$createOn = $subject->getCreateOn();

		self::assertNotNull($createOn);
		self::assertSame(
			'UTC',
			$createOn
				->getTimezone()
				->getName(),
		);
		self::assertGreaterThanOrEqual(
			$before,
			$createOn,
		);
		self::assertLessThanOrEqual(
			$after,
			$createOn,
		);
	}

	#[Test]
	public function testSetCreateOnIsFluentAndStoresValue(): void
	{
		$createOn	= new DateTimeImmutable(datetime: '2026-01-01T00:00:00+00:00');
		$subject	= $this->createSubject();

		self::assertSame(
			$subject,
			$subject->setCreateOn(createOn: $createOn),
		);
		self::assertSame(
			$createOn,
			$subject->getCreateOn(),
		);
	}
}
