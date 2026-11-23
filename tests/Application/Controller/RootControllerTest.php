<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
/**
 * NOTE:
 *
 * A functional test drives the controller through the HTTP kernel rather than
 * calling it directly. Symfony's `WebTestCase` does not expose per-test coverage
 * targeting, so the whole test is marked as not contributing to avoid a
 * "does not define a code coverage target" risky warning.
 */
#[CoversNothing]
final class RootControllerTest extends WebTestCase
{
	/** Covers: index. */
	#[Test]
	public function index(): void
	{
		$client = static::createClient();
		$client->request(
			method: 'GET',
			uri: '/home',
		);

		self::assertResponseIsSuccessful();
	}
}
