# Breco v2.0.0

> Carpooling application for the Brittany region, France

Modern web platform allowing users to offer and search for carpooling trips in Brittany.

---

## Quick Start

**New developer?** → [Getting Started Guide](docs/getting-started.md)

### Launch the application locally

```bash
# Start all services
docker compose up --build -d

# Check everything is working
curl http://localhost:8081/api/health
```

Frontend: http://localhost:3001

API: http://localhost:8081/api (JSON only, use Postman for other routes)

Mailhog: http://localhost:8025

---

## Documentation

| Document | Description |
| -------- | ----------- |
| **[Documentation Index](docs/README.md)** | Complete overview |
| **[Getting Started](docs/getting-started.md)** | Quick setup (5 min) |

---

## Tech Stack

- **Frontend**: Vue.js 3, TypeScript, Tailwind CSS v4, Zod
- **Backend**: CakePHP 5.x, MySQL 8.0, Firebase JWT
- **API Docs**: SwaggerBake 3.x (OpenAPI / PHP 8 attributes)
- **Testing**: Vitest, PHPUnit, Selenium
- **DevOps**: Docker, Jenkins CI/CD, Nginx, Bun
- **Monitoring**: Prometheus, Grafana, cAdvisor
- **Security**: OWASP ZAP, Fail2ban

---

## Development

### Dev mode with hot reload (recommended)

**Terminal 1** - Backend services:

```bash
docker compose up -d backend mysql nginx mailhog
```

**Terminal 2** - Frontend with hot reload:

```bash
cd frontend/breco
bun install
bun run dev
```

Frontend available at: http://localhost:5173 (Vite dev server)

### Local staging mode (testing)

```bash
docker compose up --build -d
```

Frontend available at: http://localhost:3001

---

## Useful Docker Commands

### Service management

```bash
# View logs
docker logs breco_backend
docker logs breco_frontend
docker logs breco_nginx

# Restart a service
docker compose restart backend
docker compose restart nginx

# Stop all services
docker compose down

# Stop and remove volumes (⚠️ deletes database)
docker compose down -v

# Start monitoring stack
docker compose --profile monitoring up -d

# Stop monitoring stack
docker compose --profile monitoring down
```

### Rebuild images

```bash
docker compose build frontend
docker compose build backend
docker compose build nginx
docker compose up -d
```

**Force rebuild (no cache)**:

```bash
docker build --no-cache -t local/breco-frontend:latest -f frontend/breco/Dockerfile-frontend frontend/breco
docker build --no-cache -t local/breco-backend:latest -f backend/breco/Dockerfile-backend backend/breco
docker build --no-cache -t local/breco-nginx:latest -f Dockerfile.nginx .
docker compose up -d
```

### Cleanup old images

```bash
docker images --format "{{.Repository}}:{{.Tag}}" \
  | grep -E "(breco_v2_0_0|djrofr/breco)-(backend|frontend):[0-9]+" \
  | grep -v ":latest" \
  | sort -t: -k2 -n \
  | head -n -2 \
  | xargs docker rmi
```

---

## Database

### Migrations

```bash
# Run migrations (Windows/Git Bash)
docker exec -it breco_backend //app/bin/cake migrations migrate

# Run migrations (Linux/VPS)
docker exec -it breco_backend /app/bin/cake migrations migrate

# Seeds
docker exec -it breco_backend //app/bin/cake migrations seed --seed TownsSeed
docker exec -it breco_backend //app/bin/cake migrations seed --seed LocationsSeed

# Rollback
docker exec -it breco_backend //app/bin/cake migrations rollback

# Create new migration
docker exec -it breco_backend //app/bin/cake bake migration MigrationName
```

### MySQL access

```bash
# Connect to MySQL
docker exec -it breco_mysql mysql -u root -p breco_db
```

### Useful MySQL commands

```sql
-- List all users
SELECT * FROM users;

-- Empty users table
TRUNCATE TABLE users;

-- Delete specific user
DELETE FROM users WHERE id = 4;

-- View table structure
DESCRIBE users;
```

---

## Testing & Development

### Test account

⚠️ **Remove in production**

```text
Email    : test@test.com
Password : Password123
```

### Test API (Postman)

**Registration**:

```http
POST http://localhost:8081/api/auth/register
Content-Type: application/json

{
  "email": "test@example.com",
  "password": "Test1234!",
  "firstName": "Jean",
  "lastName": "Dupont",
  "phone": "0607080910"
}
```

**Login**:

```http
POST http://localhost:8081/api/auth/login
Content-Type: application/json

{
  "email": "test@example.com",
  "password": "Test1234!"
}
```

**Health Check**:

```bash
curl http://localhost:8081/api/health
```

### Running Tests

```bash
# Frontend - Unit tests
cd frontend/breco
npm run test:unit

# Frontend - Integration tests
npm run test:integration

# Frontend - UI tests
npm run test:ui

# Frontend - All tests
npm run test:all

# Frontend - Coverage
npm run test:coverage

# Frontend - Linting
npm run lint
```

```bash
# Backend - PHPUnit tests (Windows/Git Bash)
docker exec -it breco_backend vendor/bin/phpunit --testdox --display-phpunit-notices

# Backend - PHPUnit local (without Docker)
cd backend/breco
vendor/bin/phpunit --testdox --display-phpunit-notices

# Backend - PHPUnit specific test file
vendor/bin/phpunit tests/TestCase/Service/Auth/AuthServiceTest.php --testdox --display-phpunit-notices
```

---

## Environments

### Local (development)

| Service | URL |
| ------- | --- |
| Frontend (prod) | http://localhost:3001 |
| Frontend (dev) | http://localhost:5173 |
| API (nginx) | http://localhost:8081 |
| API (direct) | http://localhost:8765 |
| Mailhog | http://localhost:8025 |
| MySQL | localhost:3307 |
| Swagger UI | http://localhost:8081/swagger |

### VPS (staging)

| Service | URL |
| ------- | --- |
| Frontend | http://37.59.101.232:3001 |
| API | http://37.59.101.232:8081 |
| Jenkins | http://37.59.101.232:8080 |
| Mailhog | http://37.59.101.232:8025 |
| Swagger UI | http://37.59.101.232:8081/swagger |
| SonarQube | http://37.59.101.232:9000 |
| Grafana | SSH tunnel only (port 3002) - see [monitoring.md](monitoring/monitoring.md) |

**SSH Access**: `ssh ubuntu@37.59.101.232 -p NUMERO_DE_PORT`

---

## Quick Troubleshooting

### Frontend cannot connect to backend

The API URL is resolved dynamically in `frontend/breco/src/shared/api/axiosInstance.ts` based on `window.location.hostname`.

In local development, it always points to `http://localhost:8081/api`.

If the frontend cannot reach the backend:

```bash
docker logs breco_backend
curl http://localhost:8081/api/health
```

### CORS error

```bash
# Check nginx
docker ps | grep nginx
docker logs breco_nginx

# Restart nginx
docker compose restart nginx
```

### Docker image errors (build or runtime)

`local/breco-frontend`, `local/breco-backend` and `local/breco-nginx` are built locally
(via `DOCKER_USERNAME=local` in `.env`) and do not exist on Docker Hub, `docker compose pull`
will return an error for these, which is expected.

If you encounter errors related to these images, rebuild with:

```bash
docker compose up --build -d
```

For third-party images (mysql, mailhog, sonarqube), a standard pull is sufficient:

```bash
docker compose pull mysql mailhog sonarqube
```

### Complete database reset

```bash
docker compose down -v
docker compose up --build -d
# Then run migrations and seeds (see Database section above)
```

### npm issues

```bash
cd frontend/breco
rm -rf node_modules package-lock.json
npm cache clean --force
npm install
```

### fgetcsv deprecated warning (PHP 8.1+)

Ensure the `$escape` parameter is explicitly provided in `TownsSeed.php` and `LocationsSeed.php`:

```php
fgetcsv($file, 0, ',', '"', '\\');
```

---

## Implemented Features

- JWT authentication (custom `JwtAuthTrait` - Firebase JWT)
- Email verification (Mailhog)
- DDD / Clean Architecture (frontend + backend)
- OpenAPI documentation (SwaggerBake 3.x, PHP 8 attributes)
- Zod validation (presentation layer - `useTownSearch.ts`)
- Jenkins CI/CD pipeline (deployed on OVH VPS)
- Docker Compose (multi-service)
- Health check endpoint (backend + frontend)
- SonarQube code quality analysis
- Test plan: test cases (Vitest / PHPUnit / Selenium pyramid)
- Security hardening (SSH key auth, Fail2ban IPS, custom SSH port)
- Prometheus + Grafana monitoring (cAdvisor, nginx-exporter, SSH tunnel)
- OWASP ZAP baseline scan (integrated in Jenkins pipeline)
- Automatic Docker cleanup (image/cache pruning after each build)

## TODO Before Production

See [Production Checklist](docs/todo-prod.md) for complete list.

Immediate priorities:

- [ ] Disable Mailhog on VPS
- [ ] Implement rate limiting
- [ ] Complete test suite (Stories 3–10)
- [ ] Configure HTTPS

---

## Contributing

### Branches

```text
main         # Stable / staging (production branch to be created at launch)
develop      # Development
feature/*    # New features
fix/*        # Bug fixes
```

### Commits

```text
feat:     new feature
fix:      bug fix
docs:     documentation
refactor: refactoring
test:     add tests
chore:    maintenance tasks
```

---

## Support

- [Complete documentation](docs/README.md)
- [GitHub Issues](https://github.com/djro-fr/breco_v2.0.0/issues)
- Contact: syl.gi@laposte.net

---

## Project Status

- **Version**: 2.0.0
- **Status**: In development
- **Last updated**: April 25, 2026
