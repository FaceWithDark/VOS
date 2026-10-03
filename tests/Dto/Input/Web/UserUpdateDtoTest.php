<?php

declare(strict_types=1);

namespace App\Tests\Dto\Input\Web;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Dto\Input\Web\UserUpdateDto;


/**
 * NOTE:
 *
 * PATCH is a partial update, so every property is optional and defaults to
 * NULL. "Not sent at all" and "sent as NULL" are therefore indistinguishable
 * inside the DTO itself; the processor tells them apart from the raw payload.
 */
#[CoversClass(className: UserUpdateDto::class)]
class UserUpdateDtoTest extends TestCase
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
		$dto = new UserUpdateDto();

		self::assertNull(actual: $dto->name);
		self::assertNull(actual: $dto->roleId);
		self::assertNull(actual: $dto->avatar);
		self::assertNull(actual: $dto->rank);
		self::assertNull(actual: $dto->countryFlag);
	}

	#[Test]
	public function testAssignsEveryField(): void
	{
		$dto = new UserUpdateDto();

		$dto->name			= 'Gambler';
		$dto->roleId		= 1;
		$dto->avatar		= 'https://a.ppy.sh/88?88.png';
		$dto->rank			= 88;
		$dto->countryFlag	= 'ZW';

		self::assertSame(
			expected: 'Gambler',
			actual: $dto->name,
		);
		self::assertSame(
			expected: 1,
			actual: $dto->roleId,
		);
		self::assertSame(
			expected: 'https://a.ppy.sh/88?88.png',
			actual: $dto->avatar,
		);
		self::assertSame(
			expected: 88,
			actual: $dto->rank,
		);
		self::assertSame(
			expected: 'ZW',
			actual: $dto->countryFlag,
		);
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
				->validate(value: new UserUpdateDto());

		self::assertCount(
			expectedCount: 0,
			haystack: $violations,
		);
	}

	#[Test]
	public function testFullyPopulatedPayloadHasNoViolations(): void
	{
		$dto = new UserUpdateDto();

		$dto->name			= 'Gambler';
		$dto->roleId		= 1;
		$dto->avatar		= 'https://a.ppy.sh/88?88.png';
		$dto->rank			= 88;
		$dto->countryFlag	= 'ZW';

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
	public function testZeroRankIsValid(): void
	{
		$dto = new UserUpdateDto();

		$dto->rank = 0;

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
	public function testNegativeRankViolatesRange(): void
	{
		$dto = new UserUpdateDto();

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
		$dto = new UserUpdateDto();

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


	/**
	 * Shared assertions
	 */


	private static function assertSingleViolation(
		ConstraintViolationListInterface	$violations,
		string								$propertyPath,
		string								$message,
	): void
	{
		self::assertCount(
			expectedCount: 1,
			haystack: $violations,
		);

		self::assertSame(
			expected: $propertyPath,
			actual: $violations
				->get(0)
				->getPropertyPath(),
		);

		self::assertSame(
			expected: $message,
			actual: $violations
				->get(0)
				->getMessage(),
		);
	}
}
