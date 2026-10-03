<?php

declare(strict_types=1);

namespace App\Tests\Dto\Input\Catalog;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Dto\Input\Catalog\TournamentUpdateDto;


/**
 * NOTE:
 *
 * PATCH is a partial update, so every property is optional and defaults to
 * NULL. "Not sent at all" and "sent as NULL" are therefore indistinguishable
 * inside the DTO itself; the processor tells them apart from the raw payload.
 */
#[CoversClass(className: TournamentUpdateDto::class)]
class TournamentUpdateDtoTest extends TestCase
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
		$dto = new TournamentUpdateDto();

		self::assertNull(actual: $dto->name);
		self::assertNull(actual: $dto->description);
	}

	#[Test]
	public function testAssignsNameAndDescription(): void
	{
		$dto = new TournamentUpdateDto();

		$dto->name			= 'VOT88';
		$dto->description	= 'Vietnamese Osu!taiko Tournament 88 (special edition).';

		self::assertSame(
			expected: 'VOT88',
			actual: $dto->name,
		);
		self::assertSame(
			expected: 'Vietnamese Osu!taiko Tournament 88 (special edition).',
			actual: $dto->description,
		);
	}

	#[Test]
	public function testAssignsNullDescription(): void
	{
		$dto = new TournamentUpdateDto();

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
				->validate(value: new TournamentUpdateDto());

		self::assertCount(
			expectedCount: 0,
			haystack: $violations,
		);
	}

	#[Test]
	public function testNameUpdateHasNoViolations(): void
	{
		$dto = new TournamentUpdateDto();

		$dto->name = 'VOT88';

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
		$dto = new TournamentUpdateDto();

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
