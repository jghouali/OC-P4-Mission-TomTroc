# TomTroc

TomTroc is a PHP web application that allows members to exchange books with other members.

Members can create an account, publish books, browse available books, view member profiles, and contact other members through an internal messaging system.

The project is built with object-oriented PHP, without using a full PHP framework. It uses a custom MVC architecture and separates responsabilities into controllers, entities, managers, repositories, services, views, and home core framework components.

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technical Stack](#technical-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database](#database)
- [Running the Project](#running-the-project)
- [Project Structure](#project-structure)
- [Application Architecture](#application-architecture)
- [Entities](#entities)
- [Request Flow](#request-flow)
- [Core](#core)
- [Database](#database)
- [HTTP](#http)
- [Router](#router)
- [Services](#services)
- [Settings](#settings)
- [View](#view)
- [Lib](#lib)
- [Entities](#entities)
- [Enums](#enums)
- [Managers](#managers)
- [Repositories](#repositories)
- [How it work ?](#main-routes)
- [Main Routes](#main-routes)
- [Authentication](#authentication)
- [Book Management](#book-management)
- [Member Management](#member-management)
- [Messaging](#messaging)
- [Data Validation](#data-validation)
- [Security](#security)
- [Development Tooling](#development-tooling)
- [Development Workflow](#development-workflow)
- [Useful Commands](#useful-commands)
- [Deployment](#deployment)
- [Author](#author)

---

## Overview

TomTroc is a books application.

Member can:

- create an account;
- log in and log out;
- browse books shared by other members;
- search for books;
- view a book detail page;
- add their own books;
- edit or delete their own books;
- update book availability;
- view public member profiles;
- manage their own profile;
- send and receive messages through the internal messaging system.

The project was developed for OpenClassRoom Project P4. It is designed to demonstrate:

- object-oriented PHP;
- MVC architecture;
- custom routing;
- HTTP request and response handling;
- database access through PDO;
- entity validation;
- session-based authentication;
- internal messaging;
- separation of responsibilities;
- local and CI-based quality checks.

---

## Features

### Public Features

- Display the home page.
- Search for books.
- Display available books.
- Display a book detail page.
- Display public member profiles.
- Access the registration page.
- Access the login page.

### Member Features

- Create an account.
- Log in.
- Log out.
- Access personal profile data.
- Edit personal profile information.
- Add a book.
- Edit a book.
- Delete a book.
- Update book availability.
- View the member’s books.
- Access the messaging system.
- Send messages.
- Receive messages.
- View conversations with other members.
- Track unread messages through notification counters.

### Technical Features

- Custom request routing.
- Controller-based request handling.
- PHP template rendering.
- Data persistence through repository classes.
- Business logic through manager classes.
- Reusable services for authentication and validation.
- Custom entities for domain objects.
- Enums for domain states and validation types.
- PDO-based storage layer.
- Application settings loader.
- HTTP request and response abstractions.
- Localized date handling.
- Error handling.
- Authentication protection for private pages.
- Unit tests.
- Code quality tooling.
- GitHub Actions CI workflow.

---

## Technical Stack

- PHP 8.4
- MySQL or MariaDB
- Composer
- HTML5
- CSS3
- JavaScript
- PDO
- PHPUnit
- PHP_CodeSniffer
- PHP CS Fixer
- GitHub Actions
- Object-oriented programming
- Custom MVC architecture
- Main PHP namespace: `Green\TomTroc`

The application itself does not use external PHP framework. Composer dependencies are used for development, testing, code quality, and CI checks.

---

## Requirements

Before installing the project, make sure the following tools are available:

```bash
php -v
composer -V
mysql --version
```

Recommended tools:

| Tool | Recommended Version |
|---|---:|
| PHP | 8.4 |
| Composer | 2.x |
| MySQL | 8.x |
| MariaDB | 10.x |
| phpMyAdmin | 5.x |

The project can be run with:

- Apache;
- Nginx;
- the built-in PHP server, depending on routing and server configuration.

Apache is recommended for local development because the project can use URL rewriting through `.htaccess`.

---

## Installation

Clone the repository:

```bash
git clone https://github.com/your-username/tomtroc.git
cd tomtroc
```

Install Composer dependencies:

```bash
composer install
```

Regenerate the Composer autoloader if needed:

```bash
composer dump-autoload
```

Create the database using your preferred database administration tool.

For example, with MySQL:

```sql
CREATE DATABASE tomtroc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Import one of the provided SQL files:

- `tomtroc-no-data.sql`: database structure without demo data;
- `tomtroc-demo-data.sql`: database structure with demo data.

Example with the command line:

```bash
mysql -u tomtroc -p tomtroc < tomtroc-demo-data.sql
```

You can also import the SQL file with phpMyAdmin.

---

## Configuration

The application configuration is stored in the `config/` directory.

The local database configuration can be defined in:

```text
config/custom.php
```

Example configuration:

```php
return [
    'db' => [
        'storage' => 'mysql',
        'dsn' => 'mysql:host=localhost;dbname=tomtroc',
        'username' => 'tomtroc',
        'password' => 'tomtroc',
        'schema' => ROOT_DIR . 'tomtroc.structure.sql',
        'options' => [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION],
        'fetchall_mode' => PDO::FETCH_ASSOC,
        'fetch_mode' => PDO::FETCH_ASSOC,
    ],
];
```

Configuration files are loaded by the `Settings` component:

```php
Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
Settings::initialize();
```

Sensitive credentials should not be committed to a public repository.

Recommended `.gitignore` entries:

```gitignore
/config/custom.php
/vendor/
/public/uploads/
```

If the project needs a sample configuration file, create a file such as:

```text
config/custom.example.php
```

and keep real credentials only in `config/custom.php`.

---

## Database

The database stores members, books, messages, and the relationships between them.

### Tables

```text
members
-- member_id
-- username
-- email
-- password_hash
-- status
-- avatar_path
-- notification_count
-- created_at
-- updated_at

books
-- book_id
-- title
-- author
-- description
-- image_path
-- availability
-- fk_member_id

messages
-- message_id
-- fk_from_member_id
-- fk_to_member_id
-- content
-- is_read
-- sent_at
-- modified_at
```

---

## Running the Project

### With Apache

Configure the virtual host to point to the `public/` directory:

```apache
<VirtualHost *:80>
    ServerName tomtroc.local
    DocumentRoot /path/to/tomtroc/public

    <Directory /path/to/tomtroc/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Then add the local host entry:

```text
127.0.0.1 tomtroc.local
```

On Linux or macOS, edit:

```text
/etc/hosts
```

On Windows, edit:

```text
C:\Windows\System32\drivers\etc\hosts
```

Then open:

```text
http://tomtroc.local
```

### URL Rewriting

If Apache is used, the `.htaccess` file in the `public/` directory can redirect all non-existing files and directories to `index.php`.

Example:

```apache
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^(.+)$ index.php [QSA,L]
```

This allows the custom router to resolve application routes.

### With the Built-in PHP Server

Depending on the routing configuration, the application may also be started with:

```bash
php -S localhost:8000 -t public
```

Then open:

```text
http://localhost:8000
```

If routes are not resolved correctly with the built-in server, use Apache with URL rewriting.

---

## Project Structure

```text
tomtroc/
├── .github/
│   └── workflows/
│   │   ├── php-code-verifier.yaml
│       └── verify-dev-pull-request.yml
├── .githooks/
│   └── pre-commit
├── config/
│   ├── app.settings.php
│   ├── custom.php
│   ├── environment.php
│   └── notification.php
├── public/
│   ├── index.php
│   ├── css/
│   ├── js/
│   ├── images/
│   └── uploads/
├── scripts/
│   ├── php-cs-fixer.sh
│   ├── php-syntax.sh
│   └── phpcs.sh
├── src/
│   ├── Controller/
│   │   ├── BookController.php
│   │   ├── ErrorController.php
│   │   ├── HomeController.php
│   │   ├── MemberController.php
│   │   └── MessageController.php
│   ├── Core/
│   │   ├── Database/
│   │   │   ├── PdoDatabase.php
│   │   │   └── StorageInterface.php
│   │   ├── Http/
│   │   │   ├── Request.php
│   │   │   └── Response.php
│   │   ├── Lib/
│   │   │   └── Locales.php
│   │   ├── Router/
│   │   │   └── Router.php
│   │   ├── Service/
│   │   │   ├── AuthentificationService.php
│   │   │   └── ValidatorService.php
│   │   ├── Settings/
│   │   │   └── Settings.php
│   │   └── View/
│   │       └── View.php
│   ├── Entity/
│   │   ├── AbstractEntity.php
│   │   ├── BookEntity.php
│   │   ├── EntityInterface.php
│   │   ├── MemberEntity.php
│   │   ├── MessageEntity.php
│   │   └── ProfileEntity.php
│   ├── Enum/
│   │   ├── BookStatusEnum.php
│   │   ├── MemberStatusEnum.php
│   │   ├── MessageStatusEnum.php
│   │   └── ValidatorEnum.php
│   ├── Manager/
│   │   ├── BookManager.php
│   │   ├── MemberManager.php
│   │   └── MessageManager.php
│   └── Repository/
│       ├── BookRepository.php
│       ├── MemberRepository.php
│       └── MessageRepository.php
├── templates/
│   ├── available-books.php
│   ├── book-detail.php
│   ├── book-edit.php
│   ├── book-add.php
│   ├── debug.php
│   ├── error.php
│   ├── footer.template.php
│   ├── header.template.php
│   ├── home.php
│   ├── login.php
│   ├── main.template.php
│   ├── message-box.php
│   ├── my-profile.php
│   ├── profile.php
│   └── register.php
├── tests/
├── vendor/
├── composer.json
├── composer.lock
└── README.md
```

### Directory Responsibilities

| Directory | Description |
|---|---|
| `.githooks/` | Local Git hook scripts. |
| `.github/workflows/` | GitHub Actions workflows. |
| `config/` | Application configuration files. |
| `public/` | Public web root and static assets. |
| `scripts/` | Automation and quality-check scripts. |
| `src/` | Application classes under the `Green\TomTroc` namespace. |
| `src/Controller/` | Controllers handling requests |
| `src/Core/` | Custom framework and reusable technical components. |
| `src/Core/Database/` | Database abstraction and PDO implementation. |
| `src/Core/Http/` | HTTP request and response abstractions. |
| `src/Core/Lib/` | Internal utility classes. |
| `src/Core/Router/` | Custom routing system. |
| `src/Core/Service/` | Reusable application services. |
| `src/Core/Settings/` | Application settings loader and registry. |
| `src/Core/View/` | Template rendering layer. |
| `src/Entity/` | Domain entities. |
| `src/Enum/` | Domain and validation enums. |
| `src/Manager/` | Business logic layer. |
| `src/Repository/` | Data persistence layer. |
| `templates/` | PHP view templates. |
| `tests/` | PHPUnit test files. |
| `vendor/` | Composer dependencies. |

---

## Application Architecture

The project follows a custom MVC-inspired architecture.

### Request Flow

```text
Browser
  ↓
public/index.php
  ↓
Request
  ↓
Router
  ↓
Controller
  ↓
Service / Manager
  ↓
Repository
  ↓
StorageInterface / PdoDatabase
  ↓
Entity
  ↓
View
  ↓
Response
  ↓
Browser
```

### Core

The `Core` namespace contains reusable technical components:

- database abstraction;
- HTTP abstractions;
- routing;
- settings management;
- services;
- view rendering;
- localization helpers.

### Database

#### StorageInterface

`StorageInterface` defines the minimum contract required by the storage layer.

```php
public function open();
public function insert(string $entity, array $data): int|false;
public function delete(string $entity, array $data): bool;
public function deleteAll(string $table): bool;
public function findAll(string $table): array;
public function update(string $table, int $id, array $data): bool;
public function queryCustom(string $sql, array $data): array;
```

This interface makes it possible to decouple the application from the concrete database implementation.

#### `PdoDatabase`

`PdoDatabase` implements `StorageInterface` using PHP PDO.

It is responsible for:

- opening the database connection;
- keeping a reusable PDO connection;
- inserting rows;
- updating rows;
- deleting rows;
- deleting all rows from a table;
- retrieving all rows from a table;
- executing custom prepared SQL queries.

Example initialization:

```php
if (self::get(Settings::DB_STORAGE) === 'mysql') {
    self::$dbManager = new PdoDatabase(
        Settings::get(Settings::DB_DSN),
        Settings::get(Settings::DB_USER),
        Settings::get(Settings::DB_PASSWORD),
        Settings::get(Settings::DB_OPTIONS),
        Settings::get(Settings::DB_FETCHALL_MODE),
        Settings::get(Settings::DB_FETCH_MODE),
    );

    self::$dbManager->open();
}
```

Example insert:

```php
$lastId = $this->dbManager->insert('books', $book->toArray());
```

Example update:

```php
$this->dbManager->update('books', $bookId, $book->toArray());
```

Example delete:

```php
$this->dbManager->delete('books', [
    'book_id' => $bookId,
]);
```

Example custom query:

```php
$this->dbManager->queryCustom(
    'SELECT *
     FROM books
     WHERE title = :title',
    [
        'title' => [$title, PDO::PARAM_STR],
    ]
);
```

The expected return type is an array for select operations, a boolean for write and delete operations, or an inserted ID for insert operations.

---

### HTTP

#### `Request`

The `Request` class represents an incoming HTTP request.

Methods :
```php
public function getHttpLocation(): string
public function getHttpMethod(): string
public function getHttpParameters(bool $inArray): string|array
public function setHttpMethod(string $httpMethod)
public function setHttpLocation(string $httpLocation)
public function setHttpParameters(array $httpParameters)
```

It can extract:

- the HTTP method;
- the requested location;
- query parameters;
- submitted form parameters.

Example:

```php
$request = new Request(
    $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'],
    $_POST
);
```

The request is then passed to the router:

```php
$response = $router->resolve($request);
```

#### `Response`

The `Response` class represents an HTTP response.

Methods :
```php
public function getHttpContent()
public function getHttpCode()
public function getHttpHeader()
public function setHttpContent(string $httpContent)
public function setHttpCode(int $httpCode)
public function setHttpHeader(array $httpHeaders)
public function send()
```

It can be used to:

- return HTML content;
- return plain text;
- perform a redirect;
- define a status code;
- define headers;
- centralize browser output.

Example:

```php
return new Response('Success', 303, [
    'Location' => '/',
]);
```

---

### Router

#### `Router`

The `Router` class registers routes and resolves requests :

```php
public function register(string $httpMethod, string $route, Closure $function)
public function resolve(Request $request): Response
```

Example:
Register the home page :
```php
$router->register(
    'GET',
    '/',
    Settings::getHomeController()->showHomePage(...)
);
```

```php
$request = new Request(
    $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'],
    $_POST
);

$response = $router->resolve($request);
```
The router returns the `Response` produced by the matching controller action.

---

### Services

#### `AuthentificationService`

`AuthentificationService` manages authentication-related logic.

Main methods:

```php
public function isLoggedIn(): bool
public function login(string $email, string $password): bool
public function logout(): bool
public function register(string $username, string $email, string $password, string $avatarPath): MemberEntity|false
public function generatePasswordHash(string $password): string
public function getCurrentLoggedMember(): ?MemberEntity
```

Responsibilities:

- check whether a member is logged in;
- retrieve the current logged-in member;
- centralize password hash generation;
- handle login;
- handle logout;
- handle registration.

The service is responsible for validating credentials and storing the authenticated state in the PHP session.

#### `ValidatorService`

`ValidatorService` validates user-submitted data and entity fields.

Main methods:

```php
public static function validateField(string $propertyName, mixed $field, ValidatorEnum $validator): mixed
public static function uploadFile(array $fileArray, string $uploadDir): string|false
```

It can check:

- required fields;
- empty values;
- email format;
- date validity;
- character policy;
- text length;
- integer counters;
- uploaded files;
- image paths;
- password hash format.

The validation rules are represented by `ValidatorEnum`.

---

### Settings

#### `Settings`

The `Settings` class stores application configuration and service instances.

Main methods:

```php
public static function initialize()

public static function addSettingsFile(string $settingsFile): void
public static function getSettings(): array
public static function get(string $key, mixed $default = null): mixed

public static function getDbManager(): StorageInterface

public static function getBookRepository(): BookRepository
public static function getMessageRepository(): MessageRepository
public static function getMemberRepository(): MemberRepository

public static function getAuthentificationService(): AuthentificationService

public static function getBookManager(): BookManager
public static function getMessageManager(): MessageManager
public static function getMemberManager(): MemberManager

public static function getRouter(): Router

public static function getErrorController(): ErrorController
public static function getHomeController(): HomeController
public static function getBookController(): BookController
public static function getMemberController(): MemberController
public static function getMessageController(): MessageController
```

It can load configuration files:

```php
Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
Settings::initialize();
```

It can also expose application-level objects such as:

```php
$router = Settings::getRouter();
```

The settings layer centralizes configuration access and avoids scattering configuration logic across the codebase.

---

### View

#### `View`

The `View` class loads and renders PHP templates.

Main methods:

```php
public function renderPage(string $header, string $content, string $footer)
public function header()
public function footer()
public function render(array $data, string $template)
```

Example:

```php
$data = [
    'profile' => $member,
];

$profileView = new View($member->getUsername());
$profileView->render($data, TEMPLATE_DIR . '/profile.php');
```

Purpose:

- centralize template rendering;
- pass structured data to views;
- reuse a main layout;
- avoid repeated manual `include` logic in controllers.

---

### Lib

#### `Locales`

The `Locales` class provides static helper methods for date and time formatting.

Main methods:

```php
public static function getLocalDateTime(?string $datetime = null): \DateTime
public static function getLocalFormattedDateTime(?string $datetime = null): string
```

Example:

```php
$dateFormatted = Locales::getLocalFormattedDateTime();
$datetime = Locales::getLocalDateTime($dateFormatted);
```

It is useful for converting between `DateTime` objects and localized strings.

---

### Entities

Entities represent domain objects and provide conversion methods for persistence.

#### `EntityInterface`

```php
interface EntityInterface
{
    public function toArray(): array;

    public static function getStorageIdName(): string;

    public function getId(): ?int;
}
```

Responsibilities:

- `toArray()` returns a storage-compatible representation of the object.
- `getStorageIdName()` returns the primary key name used by the child entity.
- `getId()` returns a storage-compatible identifier of the object.

#### `AbstractEntity`
`AbstractEntity` provides common behavior for all entities.

```php
abstract class AbstractEntity implements EntityInterface
```

Main methods:

```php
abstract public static function getStorageIdName(): string;
public function getId(): ?int;
public function setId(int $id): void;
public function __call(string $method, array $args): mixed;
public function securePrintText(string $string): string;
```

Responsibilities:

- store and expose the entity ID;
- provide dynamic getter calls;
- provide safe text rendering through `htmlspecialchars()` and `nl2br()`;
- define the shared entity contract.

#### `MemberEntity`

`MemberEntity` represents a registered member child of `AbstractEntity`.

Main properties:

```php
private string $username;
private string $email;
private string $passwordHash;
private string $avatarPath;
private DateTime $createdAt;
private DateTime $updatedAt;
private int $notificationCount;
private MemberStatusEnum $status;
```

Constructors and setters use `ValidatorService::validateField()` to validate attributes.

`toArray()` returns:

```php
[
    'username' => $this->username,
    'email' => $this->email,
    'password_hash' => $this->passwordHash,
    'avatar_path' => $this->avatarPath,
    'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
    'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
    'notification_count' => "$this->notificationCount",
    'status' => $this->status->value,
    $this->getStorageIdName() => $this->getId(),
]
```

#### `BookEntity`

`BookEntity` represents a book shared by a member child of `AbstractEntity`.

Main properties:

```php
private string $title;
private string $author;
private string $imagePath;
private string $description;
private BookStatusEnum $availability;
private MemberEntity $fromMember;
```

The entity exposes getters and setters and validates its properties.

`toArray()` returns:

```php
[
    'title' => $this->title,
    'author' => $this->author,
    'image_path' => $this->imagePath,
    'description' => $this->description,
    'availability' => $this->availability->value,
    'fk_member_id' => $this->fromMember->getId(),
    $this->getStorageIdName() => $this->getId(),
]
```

#### `MessageEntity`

`MessageEntity` represents a message exchanged between two members child of `AbstractEntity`.

Main properties:

```php
private string $content;
private MessageStatusEnum $isRead;
private DateTime $sentAt;
private DateTime $modifiedAt;
private MemberEntity $fromMember;
private MemberEntity $toMember;
```

Constructors and setters use `ValidatorService::validateField()` to validate attributes.

`toArray()` returns:

```php
[
    'content' => $this->content,
    'sent_at' => $this->sentAt->format('Y-m-d H:i:s'),
    'modified_at' => $this->modifiedAt->format('Y-m-d H:i:s'),
    'fk_from_member_id' => $this->fromMember->getId(),
    'fk_to_member_id' => $this->toMember->getId(),
    'is_read' => $this->isRead->value,
    $this->getStorageIdName() => $this->getId(),
]
```

#### `ProfileEntity`

`ProfileEntity` represents the public information of a member.

It is used to expose profile data without exposing sensitive data such as the password hash.

It may provide:

- public member information;
- avatar path;
- registration date;
- member status;
- notification count when appropriate;
- number of books shared by the member;
- formatted member seniority.

Methods include:

```php
memberSince(DateTime $date): string
getBookCount(): string
getBooks(): array
getUsername(): string
getEmail(): string
getAvatarPath(): string
getId(): int
```

Unlike `MemberEntity`, `ProfileEntity` should not expose:

```php
getPasswordHash()
```

---

### Enums

Enums centralize business values and validation types.

#### `BookStatusEnum`

Represents book availability.

```php
enum BookStatusEnum: string
{
    case AVAILABLE = 'AVAILABLE';
    case NOTAVAILABLE = 'NOT-AVAILABLE';
}
```

#### `MemberStatusEnum`

Represents member account status.

```php
enum MemberStatusEnum: string
{
    case VALIDATED = 'VALIDATED';
    case NOTVALIDATED = 'NOT-VALIDATED';
}
```

#### `MessageStatusEnum`

Represents message read state.

```php
enum MessageStatusEnum: int
{
    case READ = 1;
    case NOTREAD = 0;
}
```

#### `ValidatorEnum`

Represents available validation types.

```php
enum ValidatorEnum: string
{
    case bcryptHash = 'bcryptHash';
    case uploadFile = 'uploadFile';
    case imagePath = 'imagePath';
    case email = 'email';
    case humanDate = 'humanDate';
    case intCounter = 'intCounter';
    case textContent50 = 'textContent50';
    case textContent150 = 'textContent150';
    case textContent2000 = 'textContent2000';
}
```

---

### Managers

Managers coordinate business operations between controllers and repositories.

A controller should ask a manager to perform an application operation. The manager can then use repositories, services, entities, and enums to complete the operation.

Managers should not render templates and should not directly handle HTTP responses.

#### `BookManager`

`BookManager` handles book-related business logic.

Responsibilities:

- create a book from validated input;
- retrieve books owned by a member;
- convert repository results into `BookEntity` objects.
- retrieve all books;
- retrieve a book detail;
- update a book;
- delete a book;

Methods:

```php
public function addBook(string $title, string $author, string $imagePath, string $description, BookStatusEnum $availability): BookEntity|false
public function getMyLibrary(): array
public function listBooks(?string $search = ''): array|bool
public function listAvailableBook(): array|bool
public function listLastBook(int $count): array|bool
public function getBookDetail(BookEntity|int $book): BookEntity|false
public function updateBook(BookEntity $book): BookEntity|false
public function deleteBook(BookEntity $book): bool
```

#### `MemberManager`

`MemberManager` handles member-related business logic.

Responsibilities:

- modify profile of an authentified member;
- verify if a member exist by email;
- verify if a member exist and is validated by email;
- retrieve public profile

Methods:

```php
public function modifyMyProfile(string $username, string $email, string $password, string $avatarPath): MemberEntity|false
public function emailAlreadyRegistered(string $email): bool
public function usernameAlreadyRegistered(string $email): bool
public function memberExistAndValidated(string $email): bool
public function getProfileData(int $id): ProfileEntity|false
public function getMyProfileData(): array
```

#### `MessageManager`

`MessageManager` handles internal messaging logic.

Responsibilities:

- send a message;
- retrieve messages
- mark messages as read;
- count unread messages;

Methods:

```php
public function sendMessage(MemberEntity $from, MemberEntity $to, string $content): int|false
public function myMessageBox(): array
public function getNotificationCount(): int
public function setReadtoAllMessageByUser(MemberEntity|int $fromMember): bool
```

---

### Repositories

Repositories manage persistence and SQL queries.

They should contain database-specific logic and rely on `StorageInterface` or `PdoDatabase` to execute queries.

Managers use repositories so that controllers do not need to know anything about SQL or table structure.

#### `BookRepository`

Responsible for persistence operations related to the `books` table.

Typical responsibilities:

- select all books;
- select books by availability or id or title;
- select one book by ID;
- select books owned by a member;
- insert a new book;
- update an existing book;
- delete a book;
- delete all books;
- search books by title or author.
- find last n books .

Methods:

```php
public function oneToBook(array $array): BookEntity|false
public function arrayToBook(array $array): array
public function insert(BookEntity $book): BookEntity|false
public function update(int $bookId, BookEntity $book): BookEntity|false
public function delete(BookEntity $book): bool
public function deleteAll(): bool
public function findOneById(int $id): BookEntity|false
public function findOneByTitle(string $title): BookEntity|null
public function findAll(): array
public function findAllByMember(int|MemberEntity $member): array
public function findAllByAvailability(BookStatusEnum $availability): array
public function findAllLast(int $count): array
public function findAllFilter(string $search): array
```

#### `MemberRepository`

Responsible for persistence operations related to the `members` table.

Typical responsibilities:

- select a member by ID;
- select a member by email;
- select a member by username;
- select all member;
- insert a member;
- delete a member;
- delete all member;
- update member data;
- retrieve public profile data.

Methods:

```php
public function oneToMember(array $array): MemberEntity|false
public function arrayToMember(array $array): array
public function insert(MemberEntity $member): MemberEntity|false
public function update(int $memberId, MemberEntity $member): MemberEntity|false
public function delete(MemberEntity $member): bool
public function deleteAll(): bool
public function findOneById(int $id): MemberEntity|false
public function findOneByUsername(string $username): MemberEntity
public function findOneByEmail(string $email): MemberEntity|null
public function findAll(): array
```

#### `MessageRepository`

Responsible for persistence operations related to the `messages` table.

Typical responsibilities:

- insert a new message;
- update a new message;
- delete a new message;
- delete all message;
- retrieve all message;
- retrieve messages by id, recipient, member, status, sender;
- mark messages as read;

Methods:

```php
public function oneToMessage(array $array): MessageEntity
public function arrayToMessage(array $array): array
public function insert(MessageEntity $message): MessageEntity|false
public function update(int $messageId, MessageEntity $message): bool
public function delete(MessageEntity $message): bool
public function deleteAll(): bool
public function findOneById(int $id): MessageEntity|null
public function findAll(): array
public function findAllByRecipient(int|MemberEntity $recipient): array
public function findAllByMemberSorted(int|MemberEntity $member): array
public function findAllByMemberNotRead(int|MemberEntity $member): array
public function findAllBySender(int|MemberEntity $sender): array
public function findAllByIsRead(MessageStatusEnum $status): array
public function setReadtoAllMessageByUser(MemberEntity $toMember, MemberEntity $fromMember)
```

---

## How it work ?

### Main Routes

The exact routes may vary depending on the implementation, but the application can be organized as follows.

| Method | URL | Description | Access |
|---|---|---|---|
| GET | `/` | Home page | Public |
| GET | `/available-books` | Book catalogue or available books page | Public |
| GET | `/book-detail?bookId={id}` | Book detail page | Public |
| GET | `/book-edit?bookId={id}` | Book edit form | Member |
| POST | `/book-edit` | Book update action | Member |
| GET | `/book-add` | Show book add form | Member |
| POST | `/book-add` | Book add | Member |
| GET | `/book-delete?bookId={id}` | Book deletion | Member |
| GET | `/register` | Registration form | Public |
| POST | `/register` | Account creation | Public |
| GET | `/login` | Login form | Public |
| POST | `/login` | Login action | Public |
| GET | `/logout` | Logout | Member |
| GET | `/my-profile` | Authenticated member profile | Member |
| POST | `/my-profile` | Profile update | Member |
| GET | `/profile?memberId={id}` | Public member profile | Public |
| GET | `/my-box` | Conversation list | Member |
| GET | `/my-box?memberId={id}` | Go to a specific Conversation | Member |
| POST | `/my-box?memberId={id}` | Send a message to a member | Member |
| GET | `/message-read?memberId={id}` | Set message to read from this member | Member |

Private routes check the authenticated member before performing the requested action.

---

## Authentication

Authentication is based on PHP sessions.

When a member logs in:

1. The user submits an email address and password.
2. The application searches for the corresponding member.
3. The submitted password is verified against the stored hash.
4. The member ID is stored in session.
5. The user is redirected to their profile or to the requested page.

Example session logic:

```php
if (password_verify($password, $hash)) {
    $_SESSION['id'] = $member->getId();
    $_SESSION['avatarPath'] = $member->getAvatarPath();
    $_SESSION['username'] = $member->getUserName();
    return true;
}
return false;
```

On logout:

```php
unset($_SESSION['member_id']);
unset($_SESSION['avatarPath']);
unset($_SESSION['username']);
session_destroy();
```

Private pages must check that the user is authenticated before granting access.

The authentication service centralizes this logic through methods such as:

```php
isLoggedIn()
getCurrentLoggedMember()
login()
logout()
register()
```

---

## Book Management

A book contains:

- a title;
- an author;
- a description;
- an image path;
- an availability status;
- an owner.

Book availability is handled by `BookStatusEnum`.

### Adding a Book

General process:

1. The member opens the book creation form.
2. The member fills in book information.
3. Submitted data is validated.
4. The uploaded image is validated if upload is used.
5. A `BookEntity` is created.
6. The `BookManager` asks the `BookRepository` to persist it.
7. The member is redirected to the book detail page.

### Editing a Book

Before editing, the application check that:

- the book exists;
- the user is authenticated;
- the authenticated member owns the book;
- the submitted data is valid.

### Deleting a Book

Before deletion, the application check that:

- the book exists;
- the user is authenticated;
- the authenticated member owns the book;

### Book Ownership

A member cant edit or delete another member’s book.

---

## Member Management

A member represents a registered user.

A member has:

- an ID;
- a username;
- an email;
- a password hash;
- an avatar path;
- a status;
- a notification count;
- a creation date;
- an update date.

A public profile should expose only safe information.

A profile page can display:

- username;
- avatar;
- registration duration;
- number of shared books;
- list of shared books;
- contact button.

Sensitive information, especially `password_hash`, must never be exposed in public profile data.

---

## Messaging

The messaging system allows two members to exchange messages.

A message contains:

- sender member ID;
- recipient member ID;
- content;
- read status;
- sent date;
- modified date.

Logical flow:

1. A member visits another member’s profile or a book page.
2. The member opens the messaging interface.
3. The member writes a message.
4. The message content is validated.
5. A `MessageEntity` is created.
6. The `MessageManager` persists the message through `MessageRepository`.
7. The recipient notification count can be updated.
8. The conversation is displayed in chronological order.

Unread messages are represented by `MessageStatusEnum`.

---

## Data Validation

All form-submitted data must be validated server-side.

Validation is centralized through `ValidatorService` and `ValidatorEnum`.

### Validation Examples

| Field | Validation |
|---|---|
| Username | required, text length policy |
| Email | required, valid email format |
| Password | required, compatible with password policy |
| Password hash | valid bcrypt hash |
| Avatar path | valid image path |
| Uploaded image | valid upload |
| Book title | required, maximum length |
| Book author | required, maximum length |
| Book description | maximum length |
| Book availability | valid enum value |
| Message content | required, maximum length |
| Date | valid human-readable date |
| Counter | valid integer counter |

### Validation Enum

Validation types include:

```php
ValidatorEnum::bcryptHash
ValidatorEnum::uploadFile
ValidatorEnum::imagePath
ValidatorEnum::email
ValidatorEnum::humanDate
ValidatorEnum::intCounter
ValidatorEnum::textContent50
ValidatorEnum::textContent150
ValidatorEnum::textContent2000
```

Validation happen before entity creation or inside entity setters.

---

## Security

The project handle several security concerns.

### Passwords

Passwords is never stored in plain text.

Use:

```php
$this->authentificationService->generatePasswordHash($password);
```

Verify with:

```php
$this->authentificationService->login(
            $email,
            $password
        );
```

### SQL Queries

Database queries use prepared statements whithin the PdoDatabase.

Example:

```php
    public function insert(string $entity, array $data): int|false
    {
        $params = [];
        $columns = [];
        $valuesArray = [];

        // pop the entity_id since we insert()
        array_pop($data);

        foreach ($data as $column => $value) {
            $valuesArray[] = ":$column";
            $columns[] = $column;
            $params[":$column"] = $value;
        }

        $columns = implode(', ', $columns);
        $valuesString = implode(', ', $valuesArray);

        $sql = "INSERT INTO $entity ($columns) VALUES ($valuesString)";

        $statement = self::$pdo->prepare("$sql");

        if ($statement->execute($params)) {
            return (int) self::$pdo->lastInsertId();
        }
        return false;
    }

        public function update(string $entity, int $id, array $data): bool
    {
        $setArray = [];
        $params = [];

        foreach ($data as $column => $value) {
            $setArray[] = "$column = :$column";
            $params[":$column"] = $value;
        }

        $primary = substr($entity, 0, strlen($entity) - 1) . '_id';
        $setString = implode(', ', $setArray);

        $sql = "UPDATE $entity SET $setString WHERE $primary = :primary_id";

        $params[':primary_id'] = $id;

        $statement = self::$pdo->prepare($sql);
        return $statement->execute($params);
    }

        public function delete(string $entity, array $data): bool
    {
        $whereArray = [];
        $params = [];

        foreach ($data as $column => $value) {
            $whereArray[] = "$column = :$column";
            $params[":$column"] = $value;
        }

        $whereString = implode(' AND ', $whereArray);

        $sql = "DELETE FROM $entity WHERE $whereString";

        $statement = self::$pdo->prepare("$sql");

        return $statement->execute($params);
    }

        public function queryCustom(string $sql, array $data): array
    {
        preg_match_all('/:([\w]+)/', $sql, $matches);

        foreach ($matches[1] as $key) {
            if (!array_key_exists($key, $data)) {
                throw new RuntimeException("Bind value :$key not present in data");
            }
        }

        $statement = self::$pdo->prepare($sql);
        $allowedType = [
            PDO::PARAM_BOOL,
            PDO::PARAM_INT,
            PDO::PARAM_STR,
            PDO::PARAM_NULL,
        ];

        foreach ($data as $key => [$value, $type]) {
            if (!in_array($type, $allowedType, true)) {
                throw new RuntimeException("Type :$type not allowed for key $key");
            }
            $statement->bindValue(':' . $key, $value, $type);
        }

        $statement->execute();
        return $statement->fetchAll($this->fetchAllMode);
    }
```

This reduces the risk of SQL injection.

### HTML Escaping

Data displayed in views must be escaped.

Example:

```php
htmlspecialchars($book->getTitle(), ENT_QUOTES, 'UTF-8');
```

The project also provides `securePrintText()` in `AbstractEntity`.

### Access Control

Sensitive actions must check user permissions.

Examples:

- a member can only edit their own books;
- a member can only delete their own books;
- an unauthenticated visitor must not access the messaging system;
- an unauthenticated visitor must not add a book;
- public profile data must not expose password hashes.

### Image Uploads

On upload image the application check:

- the MIME type;
- limit the file size;
- rename the file;
- avoid using the original filename directly;
- store files in a dedicated directory;
- reject dangerous extensions;
- store only the final image path in the database.

### Production Error Display

In production, avoid:

```ini
display_errors = On
```

Prefer:

```ini
display_errors = Off
log_errors = On
```

---

## Development Tooling

No external PHP framework is used by the application itself.

Composer dependencies are used for:

- unit testing;
- code style checks;
- syntax validation;
- CI checks.

Development dependencies include:

- `phpunit/phpunit`;
- `squizlabs/php_codesniffer`;
- `friendsofphp/php-cs-fixer`.

A pre-commit hook is provided in:

```text
.githooks/pre-commit
```

It runs local checks before committing, such as PHP syntax validation and code style checks.

The hook uses scripts from the `scripts/` directory.

### Enable the Pre-commit Hook Locally

```bash
git config core.hooksPath .githooks
chmod +x .githooks/pre-commit
```

This hook is a local aid. The official verification remains the GitHub Actions CI workflow.

---

## Development Workflow

Every development task should be linked to an issue and implemented in its own issue branch.

Rules:

- issue branches must be created from `dev`;
- merges into `dev` must go through a pull request;
- issue branches must be rebased on the latest `dev` before opening a pull request;
- the CI workflow must pass before merging;
- direct pushes to `dev` and `main` are forbidden;
- force-pushes to `dev` and `main` are forbidden;
- rebasing and force-pushing are allowed on issue branches when needed.

The CI workflow:

```text
verify-dev-pull-request.yml
```

checks that pull requests are valid before merging into `dev`.

```text
php-code-verifier.yaml
```

verify:

- PHP syntax;
- coding standards;

### PHPUnit
Some Unit test are provided, since I try to use TDD methodology on this project

```text
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

---

## Useful Commands

Install dependencies:

```bash
composer install
```

Update the autoloader:

```bash
composer dump-autoload
```

Run the local server:

```bash
php -S localhost:8000 -t public
```

Check PHP syntax for one file:

```bash
php -l src/Controller/BookController.php
```

Check PHP syntax for all PHP files:

```bash
find . -name "*.php" -not -path "./vendor/*" -print0 | xargs -0 -n1 php -l
```

Run PHPUnit tests:

```bash
XDEBUG_MODE=off vendor/bin/phpunit tests/ --colors=always --testdox
```

Run PHP_CodeSniffer:

```bash
vendor/bin/phpcs src tests
```

Run PHP CS Fixer in dry-run mode:

```bash
vendor/bin/php-cs-fixer fix --dry-run --diff
```

Connect to MySQL:

```bash
mysql -u tomtroc -p
```

Import the database:

```bash
mysql -u tomtroc -p < tomtroc-demo-data.sql
```

Export the database:

```bash
mysqldump -u tomtroc -p > tomtroc.sql
```

Enable the local Git hook:

```bash
git config core.hooksPath .githooks
chmod +x .githooks/pre-commit
```

---

## Deployment

To deploy the project on a server:

1. Copy the project files.
2. Install Composer dependencies.
3. Configure the database.
4. Import the SQL file.
5. Configure the web server to point to `public/`.
6. Enable URL rewriting if needed.
7. Disable PHP error display.
8. Enable PHP error logging.
9. Check upload directory permissions.
10. Configure application settings.
11. Test public pages.
12. Test registration and login.
13. Test authenticated pages.
14. Test book creation and editing.
15. Test messaging.

Recommended Composer command for production:

```bash
composer install --no-dev --optimize-autoloader
```

Then optimize the autoloader:

```bash
composer dump-autoload --optimize
```

Make sure sensitive configuration files are not publicly accessible.

---

## Author

Project created by Jeremy Ghouali as part of my web development learning.
