# VOS Unit Test Guide

Unit tests live under `tests/Unit` and mirror the layout of `src/`:

```md
tests/Unit
├── Dto
├── Entity
├── Repository
├── Service
└── State
```

Each test covers a single class in isolation: collaborators are replaced with PHPUnit test doubles, so nothing touches the database, the HTTP kernel or the service container. This is the suite that the per-class code coverage targets are measured against.

---
## Running the unit suite

> [!TIP]
> See the [**General Testing Guide**](./README.md) for the shared container setup, the
> `XDEBUG_MODE=coverage` requirement and the coverage reports.

```bash
# Every unit test
XDEBUG_MODE=coverage php vendor/bin/phpunit --testsuite unit

# A single file
XDEBUG_MODE=coverage php vendor/bin/phpunit tests/Unit/Entity/Web/UserEntityTest.php

# Filter by class/method name
XDEBUG_MODE=coverage php vendor/bin/phpunit --testsuite unit --filter UserEntityTest
```

---
## Writing unit tests

> [!TIP]
> `seal()` is a method on the object returned by `->method()` / `->expects()`
> (the invocation stubber), **not** on the mock itself - `$mock->seal()` does not
> exist. It also seals only the mock it belongs to, and it must be called *after*
> all of that mock's expectations are configured: sealing first makes any later
> `expects()` throw. When several mocks are shared across tests, a small private
> helper called after the last expectation is the cleanest way to do this.

Coverage metadata is enforced according to default configs provided in [`phpunit.dist.xml`](../../phpunit.dist.xml). Therfore, any new tests must declare what it covers and what it touches:

1. **`#[CoversClass(className: ...)]`** on the class under test.
2. **`#[UsesClass(className: ...)]`** for every other class the test actually executes. The common case is an entity: constructing `new UserEntity()` executes both `App\Entity\Web\UserEntity` and `App\Entity\Abstract\UserAbstract`, so both have to be listed. Without this, `requireCoverageContribution` marks the test as risky and **drops its coverage**.
3. **`#[CoversNothing]`** when the class under test has no executable lines at all - for example a DTO made only of typed properties and attributes. There is simply nothing for coverage to attribute, so the attribute states that explicitly instead of leaving every test method flagged as risky.
4. **Seal every mock** once its expectations are declared:

   ```php
   $this
       ->repository
       ->expects(self::once())
       ->method('find')
       ->with(88888)
       ->willReturn($entity)
       ->seal();
   ```

   Sealing makes an undeclared call fail instead of silently returning `null`, which is exactly the class of bug `requireSealedMockObjects` is there to catch.
5. **Use the modern exception-message assertions.** `expectExceptionMessage()` is deprecated in PHPUnit 13. Pin the expected message with one of:

   - `expectExceptionMessageIs(message: '...')` when the whole message is known - this is what every test in this project uses;
   - `expectExceptionMessageIsOrContains(message: '...')` when only a fragment matters;
   - `expectExceptionMessageMatches(regularExpression: '...')` for a pattern.
