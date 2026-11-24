# VOS Application Test Guide

Application tests live under `tests/Application`. They exercise the app the way a browser would: each test extends Symfony's `WebTestCase`, boots the kernel and sends a real HTTP request through it, then asserts on the response.

---
## Running the application suite

> [!TIP]
> See the [**General Testing Guide**](./README.md) for the shared container setup,
> the coverage reports and the Composer scripts that set `XDEBUG_MODE=coverage`.

```bash
# Every application test
composer test:application

# A single file
composer test:application -- tests/Application/Controller/HomeControllerTest.php

# Filter by class/method name
composer test:application -- --filter HomeControllerTest
```

---
## Writing application tests

> [!TIP]
> These tests carry `#[CoversNothing]`. `WebTestCase` drives the controller
> through the HTTP kernel rather than calling it directly, and it does not expose
> per-test coverage targets — leaving the class unannotated would make
> `requireCoverageMetadata` report every application test as risky.

Coverage metadata is enforced according to default configs provided in [`phpunit.dist.xml`](../../phpunit.dist.xml). Therfore, any new tests must declare what it covers and what it touches:

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
