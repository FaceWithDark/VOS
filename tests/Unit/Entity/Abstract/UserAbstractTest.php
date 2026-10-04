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
use App\Entity\Abstract\UserAbstract;


/**
 * NOTE:
 *
 * {@see UserAbstract} is a MappedSuperclass shared by every web user entity.
 * Its `id` is client-supplied (an osu! identifier), so it is a plain column
 * rather than a generated one. The anonymous subclass lets the test
 * instantiate the abstract directly.
 */
#[CoversClass(className: UserAbstract::class)]
class UserAbstractTest extends TestCase
{
	private function createSubject(): UserAbstract
	{
		return new class extends UserAbstract {};
	}

	#[Test]
	public function testDefaultsIdToNull(): void
	{
		$subject = $this->createSubject();

		self::assertNull(
			actual: $subject->getId(),
			message: 'The client-supplied identifier starts unset.',
		);
	}

	#[Test]
	public function testSetIdIsFluentAndStoresValue(): void
	{
		$subject = $this->createSubject();

		self::assertSame(
			expected: $subject,
			actual: $subject->setId(id: 88888),
			message: 'The setter must support a fluent chain.',
		);
		self::assertSame(
			expected: 88888,
			actual: $subject->getId(),
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

		self::assertNotNull(actual: $createOn);
		self::assertSame(
			expected: 'UTC',
			actual: $createOn
				->getTimezone()
				->getName(),
		);
		self::assertGreaterThanOrEqual(
			minimum: $before,
			actual: $createOn,
		);
		self::assertLessThanOrEqual(
			maximum: $after,
			actual: $createOn,
		);
	}

	#[Test]
	public function testSetCreateOnIsFluentAndStoresValue(): void
	{
		$createOn	= new DateTimeImmutable(datetime: '2026-01-01T00:00:00+00:00');
		$subject	= $this->createSubject();

		self::assertSame(
			expected: $subject,
			actual: $subject->setCreateOn(createOn: $createOn),
		);
		self::assertSame(
			expected: $createOn,
			actual: $subject->getCreateOn(),
		);
	}
}
