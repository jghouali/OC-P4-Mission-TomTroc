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
- [Author](#author)

---

## Overview

TomTroc is a books application.

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

Recommended `.gitignore` entries:

```gitignore
/config/custom.php
/vendor/
/public/uploads/
```

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

## Author

Project created by Jeremy Ghouali as part of my web development learning.
