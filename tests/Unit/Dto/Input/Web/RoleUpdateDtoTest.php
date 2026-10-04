<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto\Input\Web;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Dto\Input\Web\RoleUpdateDto;


/**
 * NOTE:
 *
 * PATCH is a partial update, so every property is optional and defaults to
 * NULL. "Not sent at all" and "sent as NULL" are therefore indistinguishable
 * inside the DTO itself; the processor tells them apart from the raw payload.
 *
 * The DTO declares no executable lines (only typed properties), so PHPUnit has
 * nothing to attribute coverage to: the class is covered, it simply cannot
 * contribute. `#[CoversNothing]` states that explicitly instead of leaving
 * PHPUnit to flag every test as risky.
 */
#[CoversNothing]
class RoleUpdateDtoTest extends TestCase
{
	private ValidatorInterface $validator;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		$this->validator
		   	= Validation
			::createValidatorBuilder()
			->enableAttributeMapping()
			->getValidator();
	}


	/**
	 * Property defaults & assignment
	 */


	#[Test]
	public function testDefaultsEveryFieldToNull(): void
	{
		$dto = new RoleUpdateDto();

		self::assertNull(actual: $dto->name);
		self::assertNull(actual: $dto->description);
	}

	#[Test]
	public function testAssignsNameAndDescription(): void
	{
		$dto = new RoleUpdateDto();

		$dto->name			= 'Gambler';
		$dto->description	= 'double the pay, double the deal baby. That is what high risk high reward about.';

		self::assertSame(
			expected: 'Gambler',
			actual: $dto->name,
		);
		self::assertSame(
			expected: 'double the pay, double the deal baby. That is what high risk high reward about.',
			actual: $dto->description,
		);
	}

	#[Test]
	public function testAssignsNullDescription(): void
	{
		$dto = new RoleUpdateDto();

		$dto->description = null;

		self::assertNull(actual: $dto->description);
	}


	/**
	 * Attribute constraints
	 */


	#[Test]
	public function testEmptyPayloadHasNoViolations(): void
	{
		// An empty PATCH body is rejected earlier (400) by the API, not here
		$violations
			= $this
				->validator
				->validate(value: new RoleUpdateDto());

		self::assertCount(
			expectedCount: 0,
			haystack: $violations,
		);
	}

	#[Test]
	public function testNameUpdateHasNoViolations(): void
	{
		$dto = new RoleUpdateDto();

		$dto->name = 'Gambler';

		$violations
			= $this
				->validator
				->validate(value: $dto);

		self::assertCount(
			expectedCount: 0,
			haystack: $violations,
		);
	}

	#[Test]
	public function testNullDescriptionHasNoViolations(): void
	{
		$dto = new RoleUpdateDto();

		$dto->description = null;

		$violations
			= $this
				->validator
				->validate(value: $dto);

		self::assertCount(
			expectedCount: 0,
			haystack: $violations,
			message: 'PATCH must allow clearing the description with NULL.',
		);
	}
}
