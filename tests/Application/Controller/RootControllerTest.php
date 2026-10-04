<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversNothing;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


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
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request(
            method: 'GET',
            uri: '/home',
        );

        self::assertResponseIsSuccessful();
    }
}
