# VOS Testing Guide

This directory holds every automated test in the project. Each suite has its own dedicated guide, while this page covers the things they all share: how to run them and how to read the coverage they produce.

| Suite | Directory | Guide | What it holds |
| :--- | :--- | :--- | :--- |
| `unit` | `tests/Unit` | [**Unit Test Guide**](./UNIT-TEST.md) | DTOs, entities, repositories, validators, state processors & providers |
| `application` | `tests/Application` | [**Application Test Guide**](./APPLICATION-TEST.md) | Functional HTTP tests that boot the Symfony kernel (`WebTestCase`) |
| `integration` | `tests/Integration` | [**Integration Test Guide**](./INTEGRATION-TEST.md) | Reserved for tests that need the real database/services |


The suites and their paths are declared in [`phpunit.dist.xml`](../../phpunit.dist.xml).

---
## Running the tests

> [!IMPORTANT]
> Run the tests through the Composer scripts below. They set
> `XDEBUG_MODE=coverage` themselves; running PHPUnit directly without it fails
> with *"XDEBUG_MODE=coverage ... has to be set"* because the PHPUnit
> configuration enables coverage reports.

Get inside the `vos-symfony` container first, then pick the command you need:

```bash
# Get inside `vos-symfony` Docker container
docker exec -it vos-symfony sh

# Run every suite
composer test

# Run a single suite by name
composer test:unit
composer test:application
composer test:integration

# Run a single file, or filter by class/method name (note the `--`)
composer test:unit -- tests/Unit/Entity/Web/UserEntityTest.php
composer test:unit -- --filter UserEntityTest

# List the available suites without running anything
composer test -- --list-suites
```

---
## Reading the code coverage

> [!IMPORTANT]
> [`phpunit.dist.xml`](../../phpunit.dist.xml) is deliberately strict:
> `requireCoverageMetadata`, `requireCoverageContribution` and
> `requireSealedMockObjects` are all on. Concretely, that means a test must
> declare what it covers (`#[CoversClass]`, or `#[CoversNothing]` for pure value
> objects with no executable lines) and must seal its mocks (`->seal()`), or
> PHPUnit reports the test as *risky* **and drops its coverage**. When adding
> tests, check the run output for `Risky:` entries.

> [!TIP]
> Test-only changes use the dedicated `test` commit tag (e.g.,
> `test(tournaments): cover provider not-found branch`). See the
> [**Git Commit Convention**](../conventions/GIT.md) for the full tag list.

Every run already writes an HTML report to `var/coverage/html`. For a terminal report, use `--coverage-text` (or `--only-summary-for-coverage-text` for the totals only):

```bash
composer test:unit -- --only-summary-for-coverage-text

# Full per-class text report
composer test:unit -- --coverage-text

# Then open the HTML report from your browser: var/coverage/html/index.html
```
