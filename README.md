# JNTO
- A website for tourism in Japan. It has a quiz feature and a blog feature.

# Custom PHP MVC Docker Application

A small content management application built with:

- PHP 8.1 FPM
- Nginx
- PostgreSQL 15
- Docker Compose
- PDO (with pgsql driver)
- Custom PHP MVC framework (Core routing, controller, models, repositories, views, middleware, CSRF protection, session & validator helpers)

## Configuration

Copy `.env.example` to `.env` and edit the values.

## Setup Docker:

### 1. Run Docker

- You need to install docker and docker compose on your machine.

- The first time when setup source code need to run this:
```bash
$ docker compose up -d --build
```

- And then only run:
```bash
$ docker compose up -d
```

- After setup docker, open:
    - Frontend: http://localhost:8086
    - User Login: http://localhost:8086/login
    - Admin Login: http://localhost:8086/admin/login
    - Admin Dashboard: http://localhost:8086/admin/posts

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

## Run Composer

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
