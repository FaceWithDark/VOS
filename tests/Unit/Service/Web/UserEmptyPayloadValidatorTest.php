<?php

declare(strict_types=1);

namespace App\Tests\Service\Web;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Service\Web\UserEmptyPayloadValidator;


/**
 * NOTE:
 *
 * The validator is domain-scoped but has no collaborators, so it is tested
 * directly. It only asserts the decoded payload is a non-empty array; it never
 * inspects the individual values.
 */
#[CoversClass(className: UserEmptyPayloadValidator::class)]
class UserEmptyPayloadValidatorTest extends TestCase
{
	private UserEmptyPayloadValidator $emptyPayloadValidator;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		$this->emptyPayloadValidator = new UserEmptyPayloadValidator();
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

		$this->emptyPayloadValidator->validatePatch(payload: ['name' => 'Gambler']);
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
