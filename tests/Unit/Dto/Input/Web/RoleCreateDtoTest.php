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
use App\Dto\Input\Web\RoleCreateDto;


/**
 * NOTE:
 *
 * The DTO carries validation constraints but no logic of its own, so these
 * tests exercise it the same way API Platform does: put values on the public
 * properties, then run them through a Symfony Validator built from attributes.
 */
#[CoversClass(className: RoleCreateDto::class)]
class RoleCreateDtoTest extends TestCase
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
	public function testDefaultsDescriptionToNull(): void
	{
		$dto = new RoleCreateDto();

		self::assertNull(
			actual: $dto->description,
			message: 'The optional description field must default to NULL.',
		);
	}

	#[Test]
	public function testAssignsNameAndDescription(): void
	{
		$dto = new RoleCreateDto();

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


	/**
	 * Attribute constraints
	 */


	#[Test]
	public function testUninitializedNameViolatesNotBlank(): void
	{
		// A POST payload that omits 'name' leaves the typed property uninitialized
		$violations
			= $this
				->validator
				->validate(value: new RoleCreateDto());

		self::assertViolatesNotBlankOnName(violations: $violations);
	}

	#[Test]
	public function testBlankNameViolatesNotBlank(): void
	{
		$dto = new RoleCreateDto();

		$dto->name = '';

		$violations
			= $this
				->validator
				->validate(value: $dto);

		self::assertViolatesNotBlankOnName(violations: $violations);
	}

	#[Test]
	public function testMinimalValidPayloadHasNoViolations(): void
	{
		$dto = new RoleCreateDto();

		$dto->name = 'Gambler';

		$violations
			= $this
				->validator
				->validate(value: $dto);

		self::assertCount(
			expectedCount: 0,
			haystack: $violations,
			message: 'A name alone must satisfy RoleCreateDto.',
		);
	}

	#[Test]
	public function testValidPayloadWithDescriptionHasNoViolations(): void
	{
		$dto = new RoleCreateDto();

		$dto->name			= 'Gambler';
		$dto->description	= 'double the pay, double the deal baby. That is what high risk high reward about.';

		$violations
			= $this
				->validator
				->validate(value: $dto);

		self::assertCount(
			expectedCount: 0,
			haystack: $violations,
		);
	}


	/**
	 * Shared assertions
	 */


	private static function assertViolatesNotBlankOnName(
		ConstraintViolationListInterface $violations,
	): void
	{
		self::assertCount(
			expectedCount: 1,
			haystack: $violations,
		);

		self::assertSame(
			expected: 'name',
			actual: $violations
				->get(0)
				->getPropertyPath(),
		);

		self::assertSame(
			expected: 'This value should not be blank.',
			actual: $violations
				->get(0)
				->getMessage(),
		);
	}
}
