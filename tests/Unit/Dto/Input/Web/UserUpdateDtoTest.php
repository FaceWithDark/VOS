<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto\Input\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Dto\Input\Web\UserUpdateDto;


// @mago-format-ignore-end
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
class UserUpdateDtoTest extends TestCase
{
	/** Validator. */
	private ValidatorInterface $validator;

	/**
	 * Shared assertions
	 */

	/** @mago-expect lint:literal-named-argument */
	private static function assertSingleViolation(
		ConstraintViolationListInterface $violations,
		string                           $propertyPath,
		string                           $message,
	): void {
		self::assertCount(
			1,
			$violations,
		);

		self::assertSame(
			$propertyPath,
			$violations->get(offset: 0)->getPropertyPath(),
		);

		self::assertSame(
			$message,
			$violations->get(offset: 0)->getMessage(),
		);
	}

	/**
	 * Covers: test assigns every field.
	 * @mago-expect lint:literal-named-argument(5)
	 */
	#[Test]
	public function testAssignsEveryField(): void
	{
		$dto = new UserUpdateDto();

		$dto->name        = 'Gambler';
		$dto->roleId      = 1;
		$dto->avatar      = 'https://a.ppy.sh/88?88.png';
		$dto->rank        = 88;
		$dto->countryFlag = 'ZW';

		self::assertSame(
			'Gambler',
			$dto->name,
		);
		self::assertSame(
			1,
			$dto->roleId,
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
	}

	/** Covers: test country flag must be exactly two characters. */
	#[Test]
	public function testCountryFlagMustBeExactlyTwoCharacters(): void
	{
		$dto = new UserUpdateDto();

		$dto->countryFlag = 'VNM';

		$violations = $this->validator->validate(value: $dto);

		self::assertSingleViolation(
			violations: $violations,
			propertyPath: 'countryFlag',
			message: 'This value should have exactly 2 characters.',
		);
	}

	/**
	 * Property defaults & assignment
	 */

	#[Test]
	public function testDefaultsEveryFieldToNull(): void
	{
		$dto = new UserUpdateDto();

		self::assertNull($dto->name);
		self::assertNull($dto->roleId);
		self::assertNull($dto->avatar);
		self::assertNull($dto->rank);
		self::assertNull($dto->countryFlag);
	}

	/**
	 * Attribute constraints
	 */

	#[Test]
	/** @mago-expect lint:literal-named-argument */
	public function testEmptyPayloadHasNoViolations(): void
	{
		// An empty PATCH body is rejected earlier (400) by the API, not here
		$violations = $this->validator->validate(value: new UserUpdateDto());

		self::assertCount(
			0,
			$violations,
		);
	}

	/**
	 * Covers: test fully populated payload has no violations.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testFullyPopulatedPayloadHasNoViolations(): void
	{
		$dto = new UserUpdateDto();

		$dto->name        = 'Gambler';
		$dto->roleId      = 1;
		$dto->avatar      = 'https://a.ppy.sh/88?88.png';
		$dto->rank        = 88;
		$dto->countryFlag = 'ZW';

		$violations = $this->validator->validate(value: $dto);

		self::assertCount(
			0,
			$violations,
		);
	}

	/** Covers: test negative rank violates range. */
	#[Test]
	public function testNegativeRankViolatesRange(): void
	{
		$dto = new UserUpdateDto();

		$dto->rank = -1;

		$violations = $this->validator->validate(value: $dto);

		self::assertSingleViolation(
			violations: $violations,
			propertyPath: 'rank',
			message: 'This value should be 0 or more.',
		);
	}

	/**
	 * Covers: test zero rank is valid.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testZeroRankIsValid(): void
	{
		$dto = new UserUpdateDto();

		$dto->rank = 0;

		$violations = $this->validator->validate(value: $dto);

		self::assertCount(
			0,
			$violations,
		);
	}

	/** Prepares the test fixtures. */
	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// @mago-format-ignore-start
		$this->validator = Validation::createValidatorBuilder()
			->enableAttributeMapping()
			->getValidator();
		// @mago-format-ignore-end
	}
}
