# Kuryer.net — backend

Laravel 13 API on PHP 8.5 with PostgreSQL 18. Development runs entirely in
Docker; no PHP or PostgreSQL is needed on the host.

## Run

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

| Service | Container | Host address |
|---|---|---|
| Nginx → PHP-FPM | `web`, `app` | http://localhost:8010 (`APP_PORT`) |
| PostgreSQL 18 | `db` | `localhost:5433`, db/user `kuryer`, password `secret` (dev only) |

`vendor/` lives in a Docker volume for speed on Windows, so run Composer and
Artisan with `docker compose exec app …`.

## Scope

The admin panel and REST API follow the spec
*Kuryer.net — Vahid Admin Panel üçün Texniki Tapşırıq* (§12.2 API contract,
§13.2 build order).
