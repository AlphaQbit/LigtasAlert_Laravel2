# LigtasAlert

LigtasAlert is a Laravel 12 backend for sending and monitoring facility emergency alerts. It exposes a JSON API plus a web admin dashboard.

## Requirements

- PHP 8.2 or newer
- Composer 2

The current alert and facility data store is JSON under `storage/data`; no database service is required to run the application. SQLite support (`pdo_sqlite`) is only needed if you later choose to use Laravel database migrations.

## Setup

From the project directory, run:

```powershell
composer install
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
php artisan serve
```

Open `http://127.0.0.1:8000`. The root path redirects to the admin dashboard at `/admin`.

Alternatively, `composer run setup` installs dependencies and initializes the local environment, and `composer run dev` starts the Laravel server.

## API

- `GET /api/alerts` lists alerts; `status` and `facility` query parameters filter results.
- `POST /api/alerts` creates an alert.
- `PUT /api/alerts/{id}` updates an alert.
- `POST /api/alerts/{id}/respond` records a responder.
- `GET /api/facilities` lists configured facilities.
- `GET /api/stats` returns dashboard statistics.

## Data

Alerts are read from and written to `storage/data/alerts.json`. Facilities use `storage/data/facilities.json` when present and otherwise fall back to the built-in defaults. Keep the `storage` directory writable by the PHP process.

## Tests

```powershell
php artisan test
```
