<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto\Input\Catalog;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Dto\Input\Catalog\TournamentUpdateDto;


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
class TournamentUpdateDtoTest extends TestCase
{
	/** Validator. */
	private ValidatorInterface $validator;

	/**
	 * Covers: test assigns name and description.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testAssignsNameAndDescription(): void
	{
		$dto = new TournamentUpdateDto();

		$dto->name        = 'VOT88';
		$dto->description = 'Vietnamese Osu!taiko Tournament 88 (special edition).';

		self::assertSame(
			'VOT88',
			$dto->name,
		);
		self::assertSame(
			'Vietnamese Osu!taiko Tournament 88 (special edition).',
			$dto->description,
		);
	}

	/** Covers: test assigns null description. */
	#[Test]
	public function testAssignsNullDescription(): void
	{
		$dto = new TournamentUpdateDto();

		$dto->description = null;

		self::assertNull($dto->description);
	}

	/**
	 * Property defaults & assignment
	 */

	#[Test]
	public function testDefaultsEveryFieldToNull(): void
	{
		$dto = new TournamentUpdateDto();

		self::assertNull($dto->name);
		self::assertNull($dto->description);
	}

	/**
	 * Attribute constraints
	 */

	#[Test]
	/** @mago-expect lint:literal-named-argument */
	public function testEmptyPayloadHasNoViolations(): void
	{
		// An empty PATCH body is rejected earlier (400) by the API, not here
		$violations = $this->validator->validate(value: new TournamentUpdateDto());

		self::assertCount(
			0,
			$violations,
		);
	}

	/**
	 * Covers: test name update has no violations.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testNameUpdateHasNoViolations(): void
	{
		$dto = new TournamentUpdateDto();

		$dto->name = 'VOT88';

		$violations = $this->validator->validate(value: $dto);

		self::assertCount(
			0,
			$violations,
		);
	}

	/**
	 * Covers: test null description has no violations.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testNullDescriptionHasNoViolations(): void
	{
		$dto = new TournamentUpdateDto();

		$dto->description = null;

		$violations = $this->validator->validate(value: $dto);

		self::assertCount(
			0,
			$violations,
			'PATCH must allow clearing the description with NULL.',
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
