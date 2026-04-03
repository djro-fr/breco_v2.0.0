# Breco Backend

CakePHP 5.x API backend for the Breco carpooling platform.

## Tech Stack

- **Framework**: CakePHP 5.x
- **Database**: MySQL 8.0
- **Authentication**: JWT (JSON Web Tokens)
- **Email**: Mailhog (dev), SMTP (prod)

## Quick Start

### Via Docker (Recommended)

```bash
# From project root
docker compose up --build -d backend mysql nginx
# Run migrations
docker exec -it breco_backend //app/bin/cake migrations migrate
```

Backend API available at: http://localhost:8081/api

### Local Development (without Docker)

```bash
composer install
bin/cake server -p 8765
```

## Project Structure

```text
src/
├── Controller/
│   └── Api/               # AuthController.php, TownsController.php, HealthController.php
├── Model/
│   ├── Entity/            # User.php, Town.php
│   └── Table/             # UsersTable.php, TownsTable.php
├── Service/               # Auth, Town, Location, User
├── Repository/            # TownRepository.php
├── Dto/                   # Auth, Town, Location
└── Middleware/
config/
├── app.php                # Main configuration
├── app_local.php          # Local environment config
├── routes.php             # API routes
├── Migrations/            # Versioned tables
├── Seeds/                 # TownsSeed.php, LocationsSeed.php
├── swagger.yml
└── swagger_bake.php
```

## Documentation

For complete documentation, see the main [docs folder](../../docs/):

- [Getting Started](../../docs/getting-started.md)
- [API Documentation](../../docs/api.md)
- [Architecture](../../docs/architecture.md)

## Database

### Run Migrations

```bash
docker exec -it breco_backend //app/bin/cake migrations migrate
```

### Run Seeds

```bash
docker exec -it breco_backend //app/bin/cake migrations seed --seed TownsSeed
docker exec -it breco_backend //app/bin/cake migrations seed --seed LocationsSeed
```

### Create Migration

```bash
docker exec -it breco_backend //app/bin/cake bake migration CreateUsersTable
```

### Rollback

```bash
docker exec -it breco_backend //app/bin/cake migrations rollback
```

## Testing

### Run All Tests

```bash
docker exec -it breco_backend vendor/bin/phpunit --testdox --display-phpunit-notices
```

### Run Specific Test

```bash
docker exec -it breco_backend vendor/bin/phpunit tests/TestCase/Service/Auth/AuthServiceTest.php --testdox --display-phpunit-notices
```

## Environment Variables

Set in `.env` at project root:

- `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_DB`, `MYSQL_ROOT_PASSWORD`
- `JWT_SECRET`
- `FRONTEND_URL` - used in verification emails (e.g. `http://localhost:3001`)
- `EMAIL_HOST`, `EMAIL_PORT`, `EMAIL_FROM`

## API Endpoints

### Health Check

```bash
curl http://localhost:8081/api/health
```

### Authentication

See [API Documentation](../../docs/api.md) for complete endpoint list.

## Common Tasks

### Clear Cache

```bash
docker exec -it breco_backend //app/bin/cake cache clear_all
```

### Debug Routes

```bash
docker exec -it breco_backend //app/bin/cake routes
```

### Console Access

```bash
docker exec -it breco_backend //app/bin/cake console
```

## Debugging

Enable debug mode in `config/app_local.php`:

```php
'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),
```

View logs:

```bash
docker logs breco_backend
docker exec -it breco_backend tail -f /app/logs/error.log
```

> **Note**: On Windows/Git Bash, prefix `/app/...` paths with `//app/...` to prevent path conversion.
> On Linux/VPS, use `/app/...` directly.

---
**Part of [Breco v2.0.0](../../README.md)**

---
**Last updated**: April 3, 2026
