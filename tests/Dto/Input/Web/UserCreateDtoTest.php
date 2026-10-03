<?php

declare(strict_types=1);

namespace App\Tests\Dto\Input\Web;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
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
 */
#[CoversClass(className: UserCreateDto::class)]
class UserCreateDtoTest extends TestCase
{
	private ValidatorInterface $validator;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		$this->validator = Validation
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

		self::assertSame(expected: 88888, actual: $dto->id);
		self::assertSame(expected: 1, actual: $dto->roleId);
		self::assertSame(expected: 'Gambler', actual: $dto->name);
		self::assertSame(expected: 'https://a.ppy.sh/88?88.png', actual: $dto->avatar);
		self::assertSame(expected: 88, actual: $dto->rank);
		self::assertSame(expected: 'ZW', actual: $dto->countryFlag);
	}


	/**
	 * Attribute constraints
	 */


	#[Test]
	public function testUninitializedFieldsViolateEveryConstraint(): void
	{
		// A POST payload that omits fields leaves the typed properties uninitialized
		$violations = $this->validator->validate(value: new UserCreateDto());

		self::assertSame(
			expected: ['id', 'roleId', 'name', 'avatar', 'rank', 'countryFlag'],
			actual: self::propertyPaths(violations: $violations),
		);
	}

	#[Test]
	public function testFullyPopulatedPayloadHasNoViolations(): void
	{
		$violations = $this->validator->validate(value: $this->createValidDto());

		self::assertCount(expectedCount: 0, haystack: $violations);
	}

	#[Test]
	public function testZeroRankIsValid(): void
	{
		// `NotBlank` must not reject an integer 0
		$dto = $this->createValidDto();

		$dto->rank = 0;

		$violations = $this->validator->validate(value: $dto);

		self::assertCount(expectedCount: 0, haystack: $violations);
	}

	#[Test]
	public function testNegativeRankViolatesRange(): void
	{
		$dto = $this->createValidDto();

		$dto->rank = -1;

		$violations = $this->validator->validate(value: $dto);

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

		$violations = $this->validator->validate(value: $dto);

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

		$violations = $this->validator->validate(value: $dto);

		// '' is blank AND the wrong length, hence two violations for the flag
		self::assertSame(
			expected: ['name', 'avatar', 'countryFlag', 'countryFlag'],
			actual: self::propertyPaths(violations: $violations),
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
			$paths[] = (string) $violation->getPropertyPath();
		}

		return $paths;
	}

	private static function assertSingleViolation(
		ConstraintViolationListInterface	$violations,
		string								$propertyPath,
		string								$message,
	): void
	{
		self::assertCount(expectedCount: 1, haystack: $violations);

		self::assertSame(
			expected: $propertyPath,
			actual: $violations->get(0)->getPropertyPath(),
		);

		self::assertSame(
			expected: $message,
			actual: $violations->get(0)->getMessage(),
		);
	}
}
