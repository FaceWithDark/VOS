# VOS Integration Test Guide

Integration tests live under `tests/Integration`. Anything that needs the real
database, the service container or another external service — instead of a test
double — belongs here.


## Running the integration suite

```bash
XDEBUG_MODE=coverage php vendor/bin/phpunit --testsuite integration
```

See the [**Testing Guide**](./README.md) for the shared container setup, the
`XDEBUG_MODE=coverage` requirement and the coverage reports.

> [!NOTE]
> The suite is currently empty, and
> [`phpunit.dist.xml`](../phpunit.dist.xml) enables `failOnEmptyTestSuite`, so
> running `--testsuite integration` on its own exits with an error until the
> first integration test is added. Running every suite
> (`php vendor/bin/phpunit` with no `--testsuite`) is unaffected.
