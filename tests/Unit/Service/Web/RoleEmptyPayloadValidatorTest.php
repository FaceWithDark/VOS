<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Service\Web\RoleEmptyPayloadValidator;


// @mago-format-ignore-end
/**
 * NOTE:
 *
 * The validator is domain-scoped but has no collaborators, so it is tested
 * directly. It only asserts the decoded payload is a non-empty array; it never
 * inspects the individual values.
 */
#[CoversClass(RoleEmptyPayloadValidator::class)]
class RoleEmptyPayloadValidatorTest extends TestCase
{
	/** Empty payload validator. */
	private RoleEmptyPayloadValidator $emptyPayloadValidator;

	/**
	 * Covers: test empty payload throws bad request.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testEmptyPayloadThrowsBadRequest(): void
	{
		$this->expectException(BadRequestHttpException::class);
		$this->expectExceptionMessageIs('Request payload must not be empty.');

		$this->emptyPayloadValidator->validatePatch(payload: []);
	}

	/** Covers: test falsy values still count as apresent payload. */
	#[Test]
	public function testFalsyValuesStillCountAsAPresentPayload(): void
	{
		// `0`/`''`/`false` are values, not an absent payload
		self::expectNotToPerformAssertions();

		$this->emptyPayloadValidator->validatePatch(
			payload: [
				'name' => '',
				'rank' => 0,
			],
		);
	}

	/** Covers: test non empty payload passes. */
	#[Test]
	public function testNonEmptyPayloadPasses(): void
	{
		// Reaching the end without an exception is the success condition
		self::expectNotToPerformAssertions();

		$this->emptyPayloadValidator->validatePatch(payload: ['name' => 'Gambler']);
	}

	/** Covers: set up. */
	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		$this->emptyPayloadValidator = new RoleEmptyPayloadValidator();
	}
}
