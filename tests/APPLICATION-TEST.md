# VOS Application Test Guide

Application tests live under `tests/Application`. They exercise the app the way a
browser would: each test extends Symfony's `WebTestCase`, boots the kernel and
sends a real HTTP request through it, then asserts on the response.


## Running the application suite

```bash
# Every application test
XDEBUG_MODE=coverage php vendor/bin/phpunit --testsuite application

# A single file ...
XDEBUG_MODE=coverage php vendor/bin/phpunit tests/Application/Controller/HomeControllerTest.php

# ... or filter by class/method name
XDEBUG_MODE=coverage php vendor/bin/phpunit --testsuite application --filter HomeControllerTest
```

See the [**Testing Guide**](./README.md) for the shared container setup, the
`XDEBUG_MODE=coverage` requirement and the coverage reports.


## Writing application tests

```php
final class HomeControllerTest extends WebTestCase
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
```

> [!NOTE]
> These tests carry `#[CoversNothing]`. `WebTestCase` drives the controller
> through the HTTP kernel rather than calling it directly, and it does not expose
> per-test coverage targets — leaving the class unannotated would make
> `requireCoverageMetadata` report every application test as risky.
