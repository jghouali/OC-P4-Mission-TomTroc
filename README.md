### TomTroc
Tomtroc

## Project Architecture
This project is developed with PHP 8.4.

It follows the MVC (Model-View-Controller) architecture and adheres to PSR coding standards.

The project structure is organized as follows:
| Directory | Description |
|-----------|-------------|
| /.githooks    | Local Git hook scripts   |
| /.github/workflows   | GitHub Actions workflows    |
| /public    | Public web root and static assets    |
| /scripts    | Automation and quality-check scripts    |
| /src    | Classes under the `Green\TomTroc` PHP namespace    |
| /src/Controller    | Controllers handle requests, interact with managers, and pass data to views   |
| /src/Core    | Core framework and reusable technical components   |
| /src/Core/Database    | Database-related classes   |
| /src/Core/Http    | HTTP request and response abstractions   |
| /src/Core/Router    | Routes requests to controllers   |
| /src/Core/View    | Renders templates using data provided by controllers   |
| /src/Entity    | Entities    |
| /src/Manager    | Business logic services    |
| /src/Repository    | Data persistence layer    |
| /templates    | PHP view templates    |
| /tests    | PHPUnit test files    |

## Entities
# Member Entity
Properties :
    private string $username;
    private string $email;
    private string $passwordHash;
    private string $avatarPath;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private int $notificationCount;
    private MemberStatusEnum $status;

has standard getter and setter 
And a validatorField() method which check for properties format


## Development Tooling
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

Merges into `dev` must go through a Pull Request.
Issue branches must be rebased on the latest `dev` before opening a pull request.
The CI workflow `verify-dev-pull-request.yml` ensures this.
The pull request must pass all CI checks, including PHP syntax validation and PHPUnit tests.

Here is an example of the first PHPUnit test :
```
❯ XDEBUG_MODE=off vendor/bin/phpunit tests/ --colors=always --testdox
PHPUnit 13.2.0 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.4.21

..                                                                  2 / 2 (100%)

Time: 00:00, Memory: 16.00 MB

Home (Test\Home)
 ✔ Index page send a valid http response with route parameters and values on content
 ✔ Unknown page send a valid http 404 response with route parameters and values on content

OK (2 tests, 4 assertions)
```

Direct pushes and force-pushes are forbidden on the `dev` and `main` branches.

On issue branches, pushing, force-pushing, and rebasing are allowed.
Issue branch pushes must also pass the CI checks.


## Activate pre-commit hook locally

A pre-commit hook can be enabled locally to check PHP syntax before each commit.

```bash
git config core.hooksPath .githooks
chmod +x .githooks/pre-commit
```

This hook is a local aid. The official check remains the GitHub Actions CI.
