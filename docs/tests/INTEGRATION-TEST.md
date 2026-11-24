# VOS Integration Test Guide

Integration tests live under `tests/Integration`. Anything that needs the real database, the service container or another external service instead of a test double belongs here.

---
## Running the integration suite

> [!TIP]
> See the [**General Testing Guide**](./README.md) for the shared container setup,
> the coverage reports and the Composer scripts that set `XDEBUG_MODE=coverage`.

```bash
composer test:integration
```

---
## Writing application tests

> [!TODO]
> The suite is currently empty, and
> [`phpunit.dist.xml`](../../phpunit.dist.xml) enables `failOnEmptyTestSuite`, so
> `composer test:integration` exits with an error until the first integration
> test is added. Running every suite (`composer test`) is unaffected.
