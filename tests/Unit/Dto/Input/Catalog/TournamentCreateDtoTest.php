<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto\Input\Catalog;


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
use App\Dto\Input\Catalog\TournamentCreateDto;


/**
 * NOTE:
 *
 * The DTO carries validation constraints but no logic of its own, so these
 * tests exercise it the same way API Platform does: put values on the public
 * properties, then run them through a Symfony Validator built from attributes.
 *
 * The DTO declares no executable lines (only typed properties and constraint
 * attributes), so PHPUnit has nothing to attribute coverage to: the class is
 * covered, it simply cannot contribute. `#[CoversNothing]` states that
 * explicitly instead of leaving PHPUnit to flag every test as risky.
 */
#[CoversNothing]
class TournamentCreateDtoTest extends TestCase
{
	private ValidatorInterface $validator;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Standalone validator (no kernel) so the constraint metadata is read
		// straight from the DTO attributes.
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
		$dto = new TournamentCreateDto();

		self::assertNull(
			$dto->description,
			'The optional description field must default to NULL.',
		);
	}

	#[Test]
	public function testAssignsNameAndDescription(): void
	{
		$dto = new TournamentCreateDto();

		$dto->name			= 'VOT88';
		$dto->description	= 'Vietnamese Osu!taiko Tournament 88 (special edition).';

		self::assertSame(
			'VOT88',
			$dto->name,
		);
		self::assertSame(
			'Vietnamese Osu!taiko Tournament 88 (special edition).',
			$dto->description,
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
				->validate(value: new TournamentCreateDto());

		self::assertViolatesNotBlankOnName(violations: $violations);
	}

	#[Test]
	public function testBlankNameViolatesNotBlank(): void
	{
		$dto = new TournamentCreateDto();

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
		$dto = new TournamentCreateDto();

		$dto->name = 'VOT88';

		$violations
			= $this
				->validator
				->validate(value: $dto);

		self::assertCount(
			0,
			$violations,
			'A name alone must satisfy TournamentCreateDto.',
		);
	}

	#[Test]
	public function testValidPayloadWithDescriptionHasNoViolations(): void
	{
		$dto = new TournamentCreateDto();

		$dto->name			= 'VOT88';
		$dto->description	= 'Vietnamese Osu!taiko Tournament 88 (special edition).';

		$violations
			= $this
				->validator
				->validate(value: $dto);

		self::assertCount(
			0,
			$violations,
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
			1,
			$violations,
		);

		self::assertSame(
			'name',
			$violations
				->get(0)
				->getPropertyPath(),
		);

		self::assertSame(
			'This value should not be blank.',
			$violations
				->get(0)
				->getMessage(),
		);
	}
}
