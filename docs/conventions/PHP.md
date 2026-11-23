# VOS PHP Coding Convention Guideline

This project follows the [PHP-FIG](https://www.php-fig.org/) specifications as its baseline, then adds a set of deliberate overrides that keep the codebase consistent. Read the baseline first, then the project rules below; where the two disagree, the project rule wins.

This guide covers the hand-written PHP under `src/`, `tests/`, and the bootstrap and configuration files. Files under `vendor/` and anything generated are out of scope.

---

## Baseline

The starting point is the [PHP-FIG](https://www.php-fig.org/) specification:

1. [**PSR-1**](https://www.php-fig.org/psr/psr-1/): basic coding standard.
2. [**PSR-4**](https://www.php-fig.org/psr/psr-4/): autoloading, which maps `App\` to `src/` and `App\Tests\` to `tests/`.
3. [**PER Coding Style**](https://www.php-fig.org/per/): the modern replacement for [**PSR-12**](https://www.php-fig.org/psr/psr-12/).

On top of those, every hand-written PHP file declares strict types immediately after the opening tag:

```php
<?php

declare(strict_types=1);

namespace App\Service\Web;
```

The declaration is mandatory: a file without it accepts implicit type juggling, which the rest of the project does not. Leave one blank line after it and before the namespace.

---

## Deliberate overrides

Seven points where this project intentionally differs from PER. Each one is checked in review.

### 1. Tab indentation, four columns wide

The repository indents with a real tab, rendered four columns wide, as configured in [`.editorconfig`](../../.editorconfig):

```ini
[*]
indent_size = 4
indent_style = tab
```

```php
final class User
{
	public function getName(): string
	{
		return $this->name;
	}
}
```

Configure the editor to honour `.editorconfig` rather than converting the tab to four spaces; the file must keep the tab character. YAML files under `config/`, `proxy/`, and `compose*.yaml` are the exception and use two spaces.

### 2. Labelled import groups

Imports are split into three labelled groups, always in this order, with exactly **two blank lines** between groups:

```php
/// --- Main namespaces --- ///
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Interface\Web\UserDuplicateValidatorInterface;
use App\Repository\Web\UserRepository;
```

| Label | What goes in it |
| :--- | :--- |
| `/// --- Main namespaces --- ///` | Vendor and framework classes the file uses in its body. |
| `/// --- Type hint namespaces --- ///` | Native PHP classes and attribute-only imports, such as `Override`, `DateTimeImmutable`, or `DateTimeZone`. |
| `/// --- Internal namespaces --- ///` | The project's own `App\` classes. |

An empty group **keeps its label**. Two blank lines still separate it from the next group, so the structure stays visible and future imports land in the right place.

### 3. Named arguments wherever PHP allows them

Pass arguments by name, including constructor arguments, framework calls, and the project's own methods:

```php
$this->repository->findOneBy(criteria: ['name' => $userName]);

return $this->render(
	view: 'mains/Home/index.html.twig',
	parameters: [
		'controller_name' => 'HomeController',
	],
);

throw new ConflictHttpException(
	message: sprintf(
		'A user with the name [%s] already exists.',
		$userName,
	),
);
```

The one exception is a **variadic parameter**: its values must stay positional because PHP gives them no names. That is why the `sprintf()` format values, `array_merge()` lists, and similar variadic calls stay in order:

```php
// Correct: `$values` is variadic, so only the fixed arguments could be named.
sprintf(
	'User [%s] with rank [%d] not found.',
	$userName,
	$rank,
);
```

### 4. Multi-line style

When an argument list, parameter list, attribute argument list, or condition does not fit on one line, split it with **one item per line**. Lists end with a **trailing comma** on the last item, so adding an item later touches one line:

```php
$userCurrentNameValue = $this->repository->findOneBy(
	criteria: ['name' => $userName],
);

#[ORM\Column(
	type: Types::STRING,
	length: 2,
	nullable: false,
)]
private string $countryFlag;

if (
	$payload !== null
	&& $payload !== []
	&& \array_key_exists(key: 'name', array: $payload)
) {
	// ...
}
```

Conditions have no trailing comma in PHP; for them, one condition per line is the whole rule.

The exception is a **multi-line signature**. It follows PER: the closing parenthesis, the return type, and the opening brace share one line (`): Type {`), not a brace on its own line:

```php
public function __construct(
	private UserRepository					$repository,
	private RoleRepository					$roleRepository,
	private ObjectMapperInterface			$mapper,
	private UserDuplicateValidatorInterface	$duplicateValidator,
) {}

public function setName(
	string $name,
	bool $normalise = false,
): static {
	// ...
}
```

The signature keeps its trailing comma after the last parameter. Only the brace placement differs from the general multi-line rule.

### 5. Class modifiers

Production classes in `src/` are `final` by default. When a class holds no mutable state, mark it `final readonly`:

```php
final readonly class UserDuplicateValidator implements UserDuplicateValidatorInterface
{
	// ...
}
```

Leave a class non-final only when it is designed for extension: an abstract base (for example `src/Entity/Abstract/`), a Doctrine repository, a fixture, or `Kernel`.

### 6. Fluent setters

A setter that returns the object for chaining returns `static` and `$this`:

```php
public function setName(string $name): static
{
	$this->name = $name;

	return $this;
}
```

### 7. Override attribute

Every method that overrides a parent or an interface method carries `#[Override]`, so a renamed or removed parent method fails loudly instead of silently stopping the override:

```php
#[Override]
public function validatePost(array $payload): void
{
	// ...
}
```

---

## Layout

- One blank line after `declare(strict_types=1);`.
- Two blank lines before the first import label and between import groups (see [Labelled import groups](#2-labelled-import-groups)).
- Two blank lines between the last import and the class docblock or declaration.
- One blank line between methods.

```php
<?php

declare(strict_types=1);

namespace App\Service\Web;


/// --- Main namespaces --- ///

...


/// --- Internal namespaces --- ///
use App\Repository\Web\UserRepository;


final readonly class UserDuplicateValidator
{
	public function __construct(private UserRepository $repository) {}

	#[Override]
	public function validatePost(array $payload): void
	{
		// ...
	}
}
```

---

## Column alignment

Consecutive assignments, property declarations, and parameter lists align their names and values on a column boundary using tabs (not spaces). The alignment resets at a blank line:

```php
$dto->id			= 88888;
$dto->roleId		= 1;
$dto->name			= 'Gambler';
$dto->countryFlag	= 'ZW';

private UserRepository					$repository;
private RoleRepository					$roleRepository;
private UserDuplicateValidatorInterface	$duplicateValidator;
```

---

## NOTE blocks

A longer explanation of *why* code exists goes in a `NOTE:` block inside the PHPDoc, with `NOTE:` followed by a blank ` *` line, then wrapped prose.

A multi-line `NOTE:` block breaks the tab indentation the rest of the file uses, so wrap it in [editorconfig-checker's Excluding Block](https://github.com/editorconfig-checker/editorconfig-checker#excluding-blocks) comments. The checker skips everything between those two comments when it runs in CI:

```php
final class UserCreateDto
{
	// editorconfig-checker-disable
	/**
	 * NOTE:
	 *
	 * Externally sourced osu! user identifier. It is not auto-incremented by the
	 * database (see `VOS_CATALOG.USERS` schema), so the client must provide it.
	 */
	// editorconfig-checker-enable
	#[Assert\NotBlank]
	public int $id;
}
```

Keep the two comments on their own lines, at the same indentation as the docblock, and always as a pair: a block left disabled hides real indentation errors in everything after it. Wrap only the docblock, never the member it explains.

Keep the note about the reason, not a restatement of the code.

---

## PHPDoc types

Collections and generic interfaces are typed with PHPDoc generics and shapes, never with a bare `array`:

```php
/**
 * @var Collection<int, User>
 */
private Collection $users;

/**
 * @implements ProcessorInterface<UserCreateDto|UserUpdateDto|mixed, UserResourceDto|null>
 */
final readonly class UserProcessor implements ProcessorInterface
{
	/**
	 * @return array<array-key, mixed>
	 */
	private function getDecodedPayload(): array
	{
		// ...
	}
}
```

PHPStan runs at level 10 without a baseline, so a missing or too-wide type is a failing check, not a nitpick. See [**PHPStan Guide**](../tools/PHPSTAN.md).
