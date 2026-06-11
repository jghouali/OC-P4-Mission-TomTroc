### TomTroc
Tomtroc

## Development Architecture
This project is developed with PHP 8.4.

It follows the MVC (Model-View-Controller) architecture and adheres to PSR coding standards.

No external PHP libraries are used in the application itself.
Composer dependencies are only used for CI, code quality checks, and unit testing.

A pre-commit hook is provided in `.githooks/pre-commit` to run local checks before committing,
such as PHP syntax validation and code-style checks.

This hook uses scripts from the `scripts/` directory.

"phpunit/phpunit", "squizlabs/php_codesniffer", and "friendsofphp/php-cs-fixer" are installed by Composer.

A Test-Driven Development (TDD) approach is used whenever possible.
Tests are written before the implementation in order to guide the design and validate the expected behavior.

PHPUnit is used as a development tool for unit testing.

## Development Workflow
Every development task must be linked to an issue and performed in its own issue branch.
Every issue branch must be created from the `dev` branch.

Every merge into `dev` must go through a Pull Request.
Issue branches must be rebased on the latest `dev` before opening a pull request.
The CI workflow `verify-dev-pull-request.yml` ensures this.
The pull request must pass all CI checks, including PHP syntax validation and PHPUnit tests.

Direct pushes and force pushes are forbidden on the `dev` and `main` branches.

On issue branches, pushing, force-pushing, and rebasing are allowed.
Issue branch pushes must also pass the CI checks.


## Activate pre-commit hook locally

A pre-commit hook can be enabled locally to check PHP syntax before each commit.

```bash
git config core.hooksPath .githooks
chmod +x .githooks/pre-commit
```

This hook is a local aid. The official check remains the GitHub Actions CI.
