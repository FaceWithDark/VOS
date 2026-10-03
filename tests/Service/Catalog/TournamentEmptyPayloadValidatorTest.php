<?php

declare(strict_types=1);

namespace App\Tests\Service\Catalog;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Service\Catalog\TournamentEmptyPayloadValidator;


/**
 * NOTE:
 *
 * The validator is domain-scoped but has no collaborators, so it is tested
 * directly. It only asserts the decoded payload is a non-empty array; it never
 * inspects the individual values.
 */
#[CoversClass(className: TournamentEmptyPayloadValidator::class)]
class TournamentEmptyPayloadValidatorTest extends TestCase
{
	private TournamentEmptyPayloadValidator $emptyPayloadValidator;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		$this->emptyPayloadValidator = new TournamentEmptyPayloadValidator();
	}

	#[Test]
	public function testEmptyPayloadThrowsBadRequest(): void
	{
		$this->expectException(exception: BadRequestHttpException::class);
		$this->expectExceptionMessage(message: 'Request payload must not be empty.');

		$this->emptyPayloadValidator->validatePatch(payload: []);
	}

	#[Test]
	public function testNonEmptyPayloadPasses(): void
	{
		// Reaching the end without an exception is the success condition
		self::expectNotToPerformAssertions();

		$this->emptyPayloadValidator->validatePatch(payload: ['name' => 'VOT88']);
	}

	#[Test]
	public function testFalsyValuesStillCountAsAPresentPayload(): void
	{
		// `0`/`''`/`false` are values, not an absent payload
		self::expectNotToPerformAssertions();

		$this->emptyPayloadValidator->validatePatch(
			payload: [
				'name'	=> '',
				'rank'	=> 0,
			],
		);
	}
}
