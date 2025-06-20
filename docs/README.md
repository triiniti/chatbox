# Triniiti ChatBox

**Triniiti ChatBox** is a web chat application where authenticated users can communicate in real time. User login is handled securely via GitHub OAuth. The backend is built with PHP (using Composer for dependencies), and a PostgreSQL database; everything runs in a modern Docker Compose stack for easy development and deployment.

## Table of Contents

- [Features](#features)
- [Architecture](#architecture)
- [Prerequisites](#prerequisites)
- [Getting Started](#getting-started)
- [Services](#services)
- [Configuration](#configuration)
- [Usage](#usage)
- [Security](#security)
- [Troubleshooting](#troubleshooting)
- [License](#license)

## Features

- GitHub OAuth authentication
- Real-time chat for logged-in users
- Secure by design (CSRF protection, SQL injection prevention)
- Modern OOP PHP codebase (Composer libraries for routing, PostgreSQL, templates, etc.)
- 2-tier architecture: Template and View/Controller
- Fast performance via tuned PHP-FPM child process configuration
- Fully containerized with Docker Compose (Nginx, PHP, Composer, PostgreSQL)
- XS (Cross-Site) compatible

## Architecture

- **Frontend**: HTML templates served by Nginx
- **Backend**: PHP (OOP, 2-tier: Templates + View/Controller)
- **Database**: PostgreSQL
- **Auth**: GitHub OAuth
- **Dependencies**: Managed with Composer

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) (20+)
- [Docker Compose](https://docs.docker.com/compose/) (v1.29+ or Compose v2 plugin)
- [GitHub OAuth App](https://docs.github.com/en/developers/apps/building-oauth-apps/creating-an-oauth-app) (for Client ID/Secret)
- [Git](https://git-scm.com/)

## Getting Started

1. **Clone the repository:**

   ```sh
   git clone https://github.com/yourusername/triniiti-chatbox.git
   cd triniiti-chatbox
   ```

2. **Set up environment variables:**

   - Copy `.env.example` to `.env` and fill in required values:
     - `DB_NAME`
     - `DB_USER`
     - `DB_PASSWORD`
     - `GITHUB_OAUTH_CLIENT_ID`
     - `GITHUB_OAUTH_CLIENT_SECRET`
     - `APP_URL` (e.g., `http://localhost`)

3. **Build and start the containers:**

   ```sh
   docker-compose up --build
   ```

4. **Access the app:**  
   Visit [http://localhost](http://localhost) and log in with your GitHub account.

## Services

### Web (Nginx)

- **Image:** nginx
- **Ports:** 80:80
- **Volumes:**
  - Nginx config: `./.docker/conf/nginx/default.conf:/etc/nginx/conf.d/default.conf`
  - App source: `.:/var/www/html`
- **Depends on:** php, db

### PHP (FPM)

- **Build:** `.docker`
- **Volumes:**
  - PHP config: `./.docker/conf/php/php.ini`
  - FPM config: `./.docker/conf/php/zz-docker.conf`
  - Xdebug config: `./.docker/conf/php/xdebug.ini`
  - App source: `.:/var/www/html`
- **Note:** PHP-FPM max children increased for smooth performance.

### Composer

- **Image:** composer
- **Command:** `install`
- **Volumes:** `.:/app`

### Database (PostgreSQL)

- **Image:** postgres:10.1
- **Ports:** 5432:5432
- **Volumes:** `./.docker/conf/postgres/:/docker-entrypoint-initdb.d/`
- **Environment variables:**
  - `POSTGRES_DB`
  - `POSTGRES_USER`
  - `POSTGRES_PASSWORD`

## Configuration

- Environment variables are set in `.env` (see `.env.example`).
- Customize Nginx, PHP, and PostgreSQL configs in `.docker/conf/`.
- Composer dependencies defined in `composer.json`.

## Usage

- **Start/restart the app:**
  ```sh
  docker-compose up -d
  ```
- **Stop the app:**
  ```sh
  docker-compose down
  ```
- **Install/update PHP dependencies:**
  ```sh
  docker-compose run --rm composer install
  ```

## Security

- **Authentication:** GitHub OAuth, no plain password storage.
- **CSRF:** All forms/tokens use `csrf_token` for cross-site request forgery protection.
- **SQL Injection:** All queries use PostgreSQL prepared statements.
- **XS Compatibility:** Designed to prevent cross-site scripting.

## Troubleshooting

- **Permissions:** Ensure your user has access to project files.
- **Logs:**
  ```sh
  docker-compose logs <service>
  ```
- **Database:** If DB fails to initialize, verify `.env` and volume paths.

## License

[MIT](LICENSE)  
_Replace with your license if different._

# docker-php-nginx-postgres-composer

Docker Compose configuration to run PHP 8.1 with Nginx, PHP-FPM, PostgreSQL 10.1 and Composer.

## Overview

This Docker Compose configuration lets you run easily PHP 8.1 with Nginx, PHP-FPM, PostgreSQL 10.1 and Composer.
It exposes 4 services:

- web (Nginx)
- php (PHP 8.1 with PHP-FPM)
- db (PostgreSQL 10.1)
- composer

The PHP image comes with the most commonly used extensions and is configured with xdebug.
The UUID extension for PostgreSQL has been added.
Nginx default configuration is set up for Symfony 4 (but can be easily changed) and will serve your working directory.
Composer is run at boot time and will automatically install the vendors.

## Install prerequisites

For now the project has been tested on Linux only but should run fine on Docker for Windows and Docker for Mac.

You will need:

- [Docker CE](https://docs.docker.com/engine/installation/)
- [Docker Compose](https://docs.docker.com/compose/install)
- Git (optional)

## How to use it

### Starting Docker Compose

Checkout the repository or download the sources.

Simply run `docker-compose up` and you are done.

Nginx will be available on `localhost:80` and PostgreSQL on `localhost:5432`.

### Using Composer

`docker-compose run composer <cmd>`

Where `cmd` is any of the available composer command.

### Using PostgreSQL

Default connection:

`docker-compose exec db psql -U postgres`

Using .env file default parameters:

`docker-compose exec db psql -U dbuser dbname`

If you want to connect to the DB from another container (from the `php` one for instance), the host will be the service name: `db`.

### Using PHP

You can execute any command on the `php` container as you would do on any docker-compose container:

`docker-compose exec php php -v`

## Change configuration

### Configuring PHP

To change PHP's configuration edit `.docker/conf/php/php.ini`.
Same goes for `.docker/conf/php/xdebug.ini`.

You can add any .ini file in this directory, don't forget to map them by adding a new line in the php's `volume` section of the `docker-compose.yml` file.

### Configuring PostgreSQL

Any .sh or .sql file you add in `./.docker/conf/postgres` will be automatically loaded at boot time.

If you want to change the db name, db user and db password simply edit the `.env` file at the project's root.

If you connect to PostgreSQL from localhost a password is not required however from another container you will have to supply it.

## Adding aliases

To avoid typing over and over again the same commands you can add two useful aliases in your shell's configuration (`.bashrc` or `.zshrc` for instance):

```
alias dcu="docker-compose up"
alias dcr="docker-compose run"
alias dce="docker-compose exec"
```

It then becomes way faster to execute a composer command for instance:

`dcr composer require --dev phpunit/phpunit`

calc
