<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto\Input\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Dto\Input\Web\UserCreateDto;


// @mago-format-ignore-end
/**
 * NOTE:
 *
 * The DTO carries validation constraints but no logic of its own, so these
 * tests exercise it the same way API Platform does: put values on the public
 * properties, then run them through a Symfony Validator built from attributes.
 *
 * `id` is the externally-sourced osu! identifier, so it is required on create.
 *
 * The DTO declares no executable lines (only typed properties and constraint
 * attributes), so PHPUnit has nothing to attribute coverage to: the class is
 * covered, it simply cannot contribute. `#[CoversNothing]` states that
 * explicitly instead of leaving PHPUnit to flag every test as risky.
 */
#[CoversNothing]
class UserCreateDtoTest extends TestCase
{
	/** Validator. */
	protected ValidatorInterface $validator;

	/**
	 * Covers: assert single violation.
	 * @mago-expect lint:literal-named-argument
	 */
	protected static function assertSingleViolation(
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
	 * Property assignment
	 */

	/**
	 * Attribute constraints
	 */

	/**
	 * Shared assertions
	 */

	/**
	 * @return list<string>
	 */
	protected static function propertyPaths(ConstraintViolationListInterface $violations): array
	{
		$paths = [];

		foreach ($violations as $violation) {
			/** @var ConstraintViolation $violation */
			$paths[] = $violation->getPropertyPath();
		}

		return $paths;
	}

	/**
	 * Covers: test assigns every field.
	 * @mago-expect lint:literal-named-argument(6)
	 */
	#[Test]
	public function testAssignsEveryField(): void
	{
		$dto = $this->createValidDto();

		self::assertSame(
			88_888,
			$dto->id,
		);

		self::assertSame(
			1,
			$dto->roleId,
		);

		self::assertSame(
			'Gambler',
			$dto->name,
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

	/** Covers: test blank strings violate not blank. */
	#[Test]
	public function testBlankStringsViolateNotBlank(): void
	{
		$dto = $this->createValidDto();

		$dto->name = '';

		$dto->avatar = '';

		$dto->countryFlag = '';

		$violations = $this->validator->validate(value: $dto);

		// '' is blank AND the wrong length, hence two violations for the flag
		self::assertSame(
			[
				'name',
				'avatar',
				'countryFlag',
				'countryFlag',
			],
			self::propertyPaths(violations: $violations),
		);
	}

	/** Covers: test country flag must be exactly two characters. */
	#[Test]
	public function testCountryFlagMustBeExactlyTwoCharacters(): void
	{
		$dto = $this->createValidDto();

		$dto->countryFlag = 'VNM';

		$violations = $this->validator->validate(value: $dto);

		self::assertSingleViolation(
			violations: $violations,
			propertyPath: 'countryFlag',
			message: 'This value should have exactly 2 characters.',
		);
	}

	/**
	 * Covers: test fully populated payload has no violations.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testFullyPopulatedPayloadHasNoViolations(): void
	{
		$violations = $this->validator->validate(value: $this->createValidDto());

		self::assertCount(
			0,
			$violations,
		);
	}

	/** Covers: test negative rank violates range. */
	#[Test]
	public function testNegativeRankViolatesRange(): void
	{
		$dto = $this->createValidDto();

		$dto->rank = -1;

		$violations = $this->validator->validate(value: $dto);

		self::assertSingleViolation(
			violations: $violations,
			propertyPath: 'rank',
			message: 'This value should be 0 or more.',
		);
	}

	/** Covers: test uninitialized fields violate every constraint. */
	#[Test]
	public function testUninitializedFieldsViolateEveryConstraint(): void
	{
		// A POST payload that omits fields leaves the typed properties uninitialized
		$violations = $this->validator->validate(value: new UserCreateDto());

		// @mago-format-ignore-start
		self::assertSame(
			[
				'id',     'roleId', 'name',
				'avatar', 'rank',   'countryFlag',
			],
			self::propertyPaths(violations: $violations),
		);
		// @mago-format-ignore-end
	}

	/**
	 * Covers: test zero rank is valid.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testZeroRankIsValid(): void
	{
		// `NotBlank` must not reject an integer 0
		$dto = $this->createValidDto();

		$dto->rank = 0;

		$violations = $this->validator->validate(value: $dto);

		self::assertCount(
			0,
			$violations,
		);
	}

	/** Covers: create valid dto. */
	protected function createValidDto(): UserCreateDto
	{
		$dto = new UserCreateDto();

		$dto->id = 88_888;

		$dto->roleId = 1;

		$dto->name = 'Gambler';

		$dto->avatar = 'https://a.ppy.sh/88?88.png';

		$dto->rank = 88;

		$dto->countryFlag = 'ZW';

		return $dto;
	}

	/** Covers: set up. */
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
