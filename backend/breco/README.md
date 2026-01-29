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
docker-compose up -d backend mysql

# Run migrations
docker-compose exec backend bin/cake migrations migrate
```

Backend API available at: http://localhost:8765

### Local Development (without Docker)

```bash
composer install
bin/cake server -p 8765
```

## Project Structure

```text
src/
├── Controller/      # API controllers
│   ├── AuthController.php
│   ├── HealthController.php
│   └── UsersController.php
├── Model/           # Entities and Tables
│   ├── Entity/
│   └── Table/
├── Service/         # Business logic services
└── Middleware/      # Custom middleware
config/
├── app.php          # Main configuration
├── app_local.php    # Local environment config
└── routes.php       # API routes
```

## Documentation

For complete documentation, see the main [docs folder](../../docs/):

- [Getting Started](../../docs/getting-started.md)
- [API Documentation](../../docs/api.md)
- [Architecture](../../docs/architecture.md)

## Database

### Run Migrations

```bash
docker-compose exec backend bin/cake migrations migrate
```

### Create Migration

```bash
docker-compose exec backend bin/cake bake migration CreateUsersTable
```

### Rollback

```bash
docker-compose exec backend bin/cake migrations rollback
```

## Testing

### Run Tests

```bash
docker-compose exec backend vendor/bin/phpunit
```

### Run Specific Test

```bash
docker-compose exec backend vendor/bin/phpunit tests/TestCase/Controller/AuthControllerTest.php
```

## Environment Variables

Configuration in `config/app_local.php`:

- Database credentials
- JWT secret
- Email settings (SMTP)

## API Endpoints

### Health Check

```bash
curl http://localhost:8765/health
```

### Authentication

See [API Documentation](../../docs/api.md) for complete endpoint list.

## Common Tasks

### Clear Cache

```bash
docker-compose exec backend bin/cake cache clear_all
```

### Debug Routes

```bash
docker-compose exec backend bin/cake routes
```

### Console Access

```bash
docker-compose exec backend bin/cake console
```

## Debugging

Enable debug mode in `config/app_local.php`:

```php
'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),
```

View logs:

```bash
docker logs breco_backend
tail -f logs/error.log
```

---

**Part of [Breco v2.0.0](../../README.md)**
