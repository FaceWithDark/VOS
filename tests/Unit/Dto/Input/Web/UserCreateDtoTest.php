<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto\Input\Web;


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

	private function createValidDto(): UserCreateDto
	{
		$dto = new UserCreateDto();

		$dto->id			= 88888;
		$dto->roleId		= 1;
		$dto->name			= 'Gambler';
		$dto->avatar		= 'https://a.ppy.sh/88?88.png';
		$dto->rank			= 88;
		$dto->countryFlag	= 'ZW';

		return $dto;
	}


	/**
	 * Property assignment
	 */


	#[Test]
	public function testAssignsEveryField(): void
	{
		$dto = $this->createValidDto();

		self::assertSame(
			88888,
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


	/**
	 * Attribute constraints
	 */


	#[Test]
	public function testUninitializedFieldsViolateEveryConstraint(): void
	{
		// A POST payload that omits fields leaves the typed properties uninitialized
		$violations
			= $this
				->validator
				->validate(value: new UserCreateDto());

		self::assertSame(
			[
				'id',
				'roleId',
				'name',
				'avatar',
				'rank',
				'countryFlag',
			],
			self::propertyPaths(violations: $violations),
		);
	}

	#[Test]
	public function testFullyPopulatedPayloadHasNoViolations(): void
	{
		$violations
			= $this
				->validator
				->validate(value: $this->createValidDto());

		self::assertCount(
			0,
			$violations,
		);
	}

	#[Test]
	public function testZeroRankIsValid(): void
	{
		// `NotBlank` must not reject an integer 0
		$dto = $this->createValidDto();

		$dto->rank = 0;

		$violations
			= $this
				->validator
				->validate(value: $dto);

		self::assertCount(
			0,
			$violations,
		);
	}

	#[Test]
	public function testNegativeRankViolatesRange(): void
	{
		$dto = $this->createValidDto();

		$dto->rank = -1;

		$violations
			= $this
				->validator
				->validate(value: $dto);

		self::assertSingleViolation(
			violations:		$violations,
			propertyPath:	'rank',
			message:		'This value should be 0 or more.',
		);
	}

	#[Test]
	public function testCountryFlagMustBeExactlyTwoCharacters(): void
	{
		$dto = $this->createValidDto();

		$dto->countryFlag = 'VNM';

		$violations
			= $this
				->validator
				->validate(value: $dto);

		self::assertSingleViolation(
			violations:		$violations,
			propertyPath:	'countryFlag',
			message:		'This value should have exactly 2 characters.',
		);
	}

	#[Test]
	public function testBlankStringsViolateNotBlank(): void
	{
		$dto = $this->createValidDto();

		$dto->name			= '';
		$dto->avatar		= '';
		$dto->countryFlag	= '';

		$violations
			= $this
				->validator
				->validate(value: $dto);

		// '' is blank AND the wrong length, hence two violations for the flag
		self::assertSame(
			[
				'name',
				'avatar',
				'countryFlag',
				'countryFlag'
			],
			self::propertyPaths(violations: $violations),
		);
	}


	/**
	 * Shared assertions
	 */


	/**
	 * @return list<string>
	 */
	private static function propertyPaths(ConstraintViolationListInterface $violations): array
	{
		$paths = [];

		foreach ($violations as $violation) {
			/** @var ConstraintViolation $violation */
			$paths[] = $violation->getPropertyPath();
		}

		return $paths;
	}

	private static function assertSingleViolation(
		ConstraintViolationListInterface	$violations,
		string								$propertyPath,
		string								$message,
	): void
	{
		self::assertCount(
			1,
			$violations,
		);

		self::assertSame(
			$propertyPath,
			$violations
				->get(0)
				->getPropertyPath(),
		);

		self::assertSame(
			$message,
			$violations
				->get(0)
				->getMessage(),
		);
	}
}
