<?php

declare(strict_types=1);

namespace App\Tests\Entity\Abstract;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;
use DateTimeZone;


/// --- Internal namespaces --- ///
use App\Entity\Abstract\TournamentAbstract;


/**
 * NOTE:
 *
 * {@see TournamentAbstract} is a MappedSuperclass shared by every catalog
 * tournament entity. The anonymous subclass lets the test instantiate the
 * abstract directly without dragging a concrete entity into the subject.
 */
#[CoversClass(className: TournamentAbstract::class)]
class TournamentAbstractTest extends TestCase
{
	private function createSubject(): TournamentAbstract
	{
		return new class extends TournamentAbstract {};
	}

	#[Test]
	public function testDefaultsIdToNull(): void
	{
		$subject = $this->createSubject();

		self::assertNull(
			actual: $subject->getId(),
			message: 'A not-yet-persisted entity has no identifier.',
		);
	}

	#[Test]
	public function testSetIdIsFluentAndStoresValue(): void
	{
		$subject = $this->createSubject();

		self::assertSame(
			expected: $subject,
			actual: $subject->setId(id: 7),
			message: 'The setter must support a fluent chain.',
		);
		self::assertSame(expected: 7, actual: $subject->getId());
	}

	#[Test]
	public function testConstructorInitialisesCreateOnInUtc(): void
	{
		$before		= new DateTimeImmutable(datetime: 'now', timezone: new DateTimeZone(timezone: 'UTC'));
		$subject	= $this->createSubject();
		$after		= new DateTimeImmutable(datetime: 'now', timezone: new DateTimeZone(timezone: 'UTC'));

		$createOn = $subject->getCreateOn();

		self::assertNotNull(actual: $createOn);
		self::assertSame(expected: 'UTC', actual: $createOn->getTimezone()->getName());
		self::assertGreaterThanOrEqual(minimum: $before, actual: $createOn);
		self::assertLessThanOrEqual(maximum: $after, actual: $createOn);
	}

	#[Test]
	public function testSetCreateOnIsFluentAndStoresValue(): void
	{
		$createOn	= new DateTimeImmutable(datetime: '2026-01-01T00:00:00+00:00');
		$subject	= $this->createSubject();

		self::assertSame(expected: $subject, actual: $subject->setCreateOn(createOn: $createOn));
		self::assertSame(expected: $createOn, actual: $subject->getCreateOn());
	}
}
