# VOS PHPStan Guide

This directory holds the guides for the tooling that runs on top of the project's code. This page covers [PHPStan](https://phpstan.org/), the static analyser that every change to `src/` and `tests/` must pass.

PHPStan reads the code without executing it and reports type errors, dead code, and unsafe assumptions before a single test runs. The project runs it at level 10, with the strict rule set, the Symfony and Doctrine extensions, and every relevant strictness toggle enabled.

---

## Configuration files

| File | Purpose |
| :--- | :--- |
| [`phpstan.dist.neon`](../../phpstan.dist.neon) | The shared configuration. Committed, and the source of truth for every run. |
| `phpstan.neon` | Optional local override. Gitignored; PHPStan prefers it when it exists, so it must include `phpstan.dist.neon` first. |

To override something locally, create `phpstan.neon` at the project root:

```neon
includes:
    - phpstan.dist.neon

parameters:
    level: 6
```

> [!IMPORTANT]
> The committed configuration has no baseline and no `ignoreErrors` entries. A finding must be fixed, not silenced. See [Handling a finding](#handling-a-finding).

---

## What is enabled

### Rule level

`level: 10` is the highest level PHPStan offers. It reports `mixed` values, missing parameter and return types, unsafe array access, too-wide unions, and more.

### Strict rules

The [`phpstan/phpstan-strict-rules`](https://github.com/phpstan/phpstan-strict-rules) set is included. On top of level 10 it forbids loose comparisons (`==`, `!=`), `empty()`, short ternary, backticks, and variable variables, and it requires exact types in conditions, loop variables, switch cases, and inherited method names. Dynamic properties, overwritten loop variables, and useless casts are reported too.

### Strictness toggles

| Parameter | What it enforces |
| :--- | :--- |
| `treatPhpDocTypesAsCertain` | PHPDoc types are authoritative; code that contradicts them is an error. |
| `inferPrivatePropertyTypeFromConstructor` | Private property types are inferred from constructor assignment. |
| `rememberPossiblyImpureFunctionValues` | A function call is not assumed to return the same value twice. |
| `checkBenevolentUnionTypes` | `array-key`-style benevolent unions are checked as strictly as normal unions. |
| `checkTooWideReturnTypesInProtectedAndPublicMethods` | Reports return types that list types the method never returns. |
| `checkTooWideParameterOutInProtectedAndPublicMethods` | Reports too-wide `@param-out` types on public and protected methods. |
| `checkTooWideThrowTypesInProtectedAndPublicMethods` | Reports too-wide `@throws` types on public and protected methods. |
| `reportUnsafeArrayStringKeyCasting: prevent` | Strings PHP would silently cast to integer array keys are rejected, both in annotations and at lookup sites. |
| `exceptions.implicitThrows` | A method without `@throws` is assumed to be able to throw any exception. |
| `exceptions.reportUncheckedExceptionDeadCatch` | A `catch` for an exception the `try` cannot throw is reported. |
| `exceptions.check.missingCheckedExceptionInThrows` | Every checked exception that escapes must appear in the method's `@throws`. |
| `exceptions.check.tooWideImplicitThrowType` | A too-wide implicit `@throws` set is reported. |

The project also declares its unchecked taxonomy: `RuntimeException`, `LogicException`, `ReflectionException`, Doctrine's DBAL and ORM exceptions, and PHPUnit's mock-object exception are unchecked. Only exceptions that inherit `\Exception` directly count as checked, so an application exception must be declared in `@throws`.

### Symfony and Doctrine extensions

| Package | What it adds |
| :--- | :--- |
| [`phpstan/phpstan-symfony`](https://github.com/phpstan/phpstan-symfony) | Types the DI container (`$container->get()`, `getParameter()`), console arguments, `Request::getContent()`, `InputBag::get()`, and reports unregistered or private services. Reads `var/cache/dev/App_KernelDevDebugContainer.xml`. |
| [`phpstan/phpstan-doctrine`](https://github.com/phpstan/phpstan-doctrine) | Understands entity mappings, repository and entity-manager types, and collections, and validates DQL and QueryBuilder. Reads `tests/object-manager.php` to obtain a real entity manager. |

---

## Running PHPStan

> [!IMPORTANT]
> Run it inside the `vos-symfony` container (the `app` compose service) so the analyser sees the same PHP 8.5 runtime, vendor, and compiled dev container as the application. The stack must be up: `docker compose up -d`.

```bash
# Get inside the Symfony container
docker compose exec app sh

# Analyse everything (src + tests)
php vendor/bin/phpstan analyse --no-progress --memory-limit=1G

# Analyse a single path
php vendor/bin/phpstan analyse src/Entity --no-progress
php vendor/bin/phpstan analyse tests/Unit/Service --no-progress
```

> [!NOTE]
> `phpstan analyse` discovers `phpstan.neon` first and `phpstan.dist.neon` second, so no `-c` flag is needed.

> [!TIP]
> The Symfony extension reads the compiled `dev` container. After changing anything under `config/`, rebuild it before analysing, or PHPStan may describe stale services:
>
> ```bash
> php bin/console cache:clear
> ```

Clear the result cache when a run looks stale:

```bash
php vendor/bin/phpstan clear-result-cache
```

---

## Handling a finding

Fix it. That is the whole policy: there is no baseline, and `ignoreErrors` is empty on purpose.

1. **Add the missing type.** Most level-10 findings are fixed by typing an array (`array<string, mixed>`, `list<Foo>`, an array shape), adding a return type, or declaring an `@throws`.
2. **Narrow the value.** Replace a vague annotation with the real type, or add the guard (`instanceof`, `is_string`, `array_key_exists`) that makes the narrowing true.
3. **If the code is wrong, fix the code.** Level 10 tends to expose real bugs: nullable entity properties treated as non-null, offsets that may not exist, impossible `catch` blocks.

Silencing is the last resort, it needs a reason, and it names one identifier:

```php
// @phpstan-ignore argument.type (bound by a compiler pass PHPStan cannot see)
```

- Use the narrowest form: `@phpstan-ignore <identifier> (reason)` on the same line, or on its own line right above the violating line.
- Never use the blanket `@phpstan-ignore-line` or `@phpstan-ignore-next-line`, and never skip the reason.
- `ignoreErrors` in the config is acceptable only for the same false positive repeating across many files, scoped with `paths` and a comment.
- Never add a baseline file. `reportUnmatchedIgnoredErrors` stays on, so an ignore that stops matching fails the run.

---

## References

- [PHPStan documentation](https://phpstan.org/user-guide/getting-started)
- [Config reference](https://phpstan.org/config-reference)
- [Ignoring errors](https://phpstan.org/user-guide/ignoring-errors)
- [Strict rules](https://github.com/phpstan/phpstan-strict-rules)
- [Symfony extension](https://github.com/phpstan/phpstan-symfony)
- [Doctrine extension](https://github.com/phpstan/phpstan-doctrine)
