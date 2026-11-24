# Setup

Composite action shared by the Quality and Tests workflows. It installs PHP 8.5
with the `intl` and `pdo_pgsql` extensions, validates `composer.json`, copies the
example environment files, restores `vendor/` from the cache and installs the
Composer dependencies.

---

## Usage

Inside this repository, reference the local action so pull requests test their
own version:

```yaml
- uses: actions/checkout@v5
- uses: ./.github/actions/setup
  with:
    coverage: xdebug
```

From another repository, reference a released major version:

```yaml
- uses: FaceWithDark/VOS/.github/actions/setup@v1
```

---

## Inputs

| Input | Required | Default | Description |
| :--- | :--- | :--- | :--- |
| `coverage` | no | `none` | Coverage driver passed to `shivammathur/setup-php` (`xdebug` for PHPUnit). |

---

## Releases

The action follows the tag-based release management from the
[GitHub documentation](https://docs.github.com/en/actions/how-tos/create-and-publish-actions/manage-custom-actions#using-release-management-for-actions).

1. Merge the change into `main`.
2. Draft a GitHub release with a semantic version tag, for example `v1.0.1`.
3. Publish it. The `Release action` workflow moves the matching major tag
   (`v1`) to the release commit. It refuses to move the tag backwards and
   ignores non-semantic tags such as `nightly`.
4. Use a new major version (`v2`) when an input or behaviour change breaks
   existing workflows, and leave the previous major tag where it is.
5. Patch and minor releases keep the major tag moving, so consumers of `@v1`
   receive fixes automatically.

Publishing the release is the only manual step; the major tag is maintained by
the workflow. Until the project starts releasing the application, `vX.Y.Z`
tags are reserved for this action.
