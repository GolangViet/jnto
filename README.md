# JNTO
- A website for tourism in Japan. It has a quiz feature and a blog feature.

# Requirements

A small content management application built with:

- PHP 8.1 FPM
- Nginx
- PostgreSQL 15
- Docker Compose
- PDO (with pgsql driver)

## Configuration

Copy `.env.example` to `.env` and edit the values.

## Setup Source Code:

### 1. Initialize Docker:
```bash
$ docker compose up -d --build
```

### 2. Initialize Composer:
```bash
$ docker compose exec app
$ composer install
```

- After initializing composer, open:
    - Frontend: http://localhost:8086
    - User Login: http://localhost:8086/login
    - Admin Login: http://localhost:8086/admin/login
    - Admin Dashboard: http://localhost:8086/admin/posts

## Docker:

### 1. Run Docker

- The first time when setup source code need to run this, if you have already run this command, you can skip this step:
```bash
$ docker compose up -d --build
```

- And then only run:
```bash
$ docker compose up -d
```

### 2. Access Docker Container

- If you need access to the Docker container, run this command:

```bash
$ docker compose exec app bash
```

- The `app` is the name of the PHP container. You can check the name of the container by running this command:

```bash
$ docker ps
```

### 3. Stop Docker

```bash
$ docker compose down
```

## Composer

- To reload all packages from main source, or when you edit/add something in composer.json file, run this command:
```bash
$ docker compose exec app
$ composer dump-autoload
```

- Run composer install to install dependencies:
```bash
$ docker compose exec app
$ composer install
```

- To update dependencies from the main source, run this command:
```bash
$ docker compose exec app
$ composer update
```

## Migrations

- To reload all packages from main source, or when you edit/add something in composer.json file, run this command:
```bash
$ docker compose exec app
$ composer dump-autoload
```

- Run composer install to install dependencies:
```bash
$ docker compose exec app
$ composer install
```

- To update dependencies from the main source, run this command:
```bash
$ docker compose exec app
$ composer update
```
