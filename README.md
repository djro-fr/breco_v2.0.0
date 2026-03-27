# Breco v2.0.0

> Carpooling application for the Brittany region, France

Modern web platform allowing users to offer and search for carpooling trips in Brittany.

---

## Quick Start

**New developer?** → [Getting Started Guide](docs/getting-started.md)

### Launch the application locally

```bash
# Start all services
docker-compose up -d

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

---

## Development

### Dev mode with hot reload (recommended)

**Terminal 1** - Backend services:

```bash
docker-compose up -d backend mysql nginx mailhog
```

**Terminal 2** - Frontend with hot reload:

```bash
cd frontend/breco
npm install
npm run dev
```

Frontend available at: http://localhost:5173 (Vite dev server)

### Local production mode (testing)

```bash
docker-compose up -d
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
docker-compose restart backend
docker-compose restart nginx

# Stop all services
docker-compose down

# Stop and remove volumes (⚠️ deletes database)
docker-compose down -v
```

### Rebuild images

```bash
docker-compose build frontend
docker-compose build backend
docker-compose up -d
```

**Force rebuild (no cache)**:

```bash
docker build --no-cache -t local/breco-frontend:latest -f frontend/breco/Dockerfile-frontend frontend/breco
docker build --no-cache -t local/breco-backend:latest -f backend/breco/Dockerfile-backend backend/breco
docker-compose up -d
```

### Cleanup old images

```bash
# Remove old build versions (keeps :latest and current version)
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
# Run migrations
docker-compose exec backend bin/cake migrations migrate

# Rollback
docker-compose exec backend bin/cake migrations rollback

# Create new migration
docker-compose exec backend bin/cake bake migration MigrationName
```

### MySQL access

```bash
# Connect to MySQL
docker exec -it breco_mysql mysql -u root -p breco_db
# Password: root
```

### Useful MySQL commands

```sql
-- List all users
SELECT * FROM users;

-- Empty users table
TRUNCATE TABLE users;

-- Delete specific user
DELETE FROM users WHERE id='4';

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

# Backend - PHPUnit tests
docker-compose exec backend vendor/bin/phpunit

# Backend - PHPUnit local (without Docker)
cd backend/breco
vendor/bin/phpunit

# Backend - PHPUnit specific test file with notices (local)
vendor/bin/phpunit tests/TestCase/Service/Auth/AuthServiceTest.php --display-phpunit-notices
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

**SSH Access**: `ssh ubuntu@37.59.101.232`

---

## Quick Troubleshooting

### Frontend cannot connect to backend

The API URL is resolved dynamically in `frontend/breco/src/shared/api/axiosInstance.ts`
based on `window.location.hostname`.
In local development, it always points to `http://localhost:8081/api`.

If the frontend cannot reach the backend, check that the backend container is running:

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
docker-compose restart nginx
```

### Docker image errors (build or runtime)

`local/breco-frontend` and `local/breco-backend` are built locally (via `DOCKER_USERNAME=local` in `.env`)
and do not exist on Docker Hub - `docker-compose pull` will return an error for these, which is expected.

If you encounter errors related to these images, pull their base images then rebuild:

```bash
# Pull base images from Docker Hub
docker pull djrofr/breco-backend-builder:8.4
docker pull oven/bun:1-alpine
docker pull nginx:alpine

# Rebuild local images
docker build --no-cache -t local/breco-backend:latest -f backend/breco/Dockerfile-backend backend/breco
docker build --no-cache -t local/breco-frontend:latest -f frontend/breco/Dockerfile-frontend frontend/breco

docker-compose up -d
```

For third-party images (mysql, mailhog, nginx, sonarqube), a standard pull is sufficient:

```bash
docker-compose pull
```

### Complete database reset

```bash
docker-compose down -v
docker-compose up -d
docker-compose exec backend bin/cake migrations migrate
```

### npm issues

```bash
cd frontend/breco
rm -rf node_modules package-lock.json
npm cache clean --force
npm install
```

---

### Implemented features

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

### TODO before production

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
main         # Production
develop      # Development
feature/*    # New features
fix/*        # Bug fixes
```

### Commits

```text
feat: new feature
fix: bug fix
docs: documentation
refactor: refactoring
test: add tests
chore: maintenance tasks
```

---

## Support

- [Complete documentation](docs/README.md)
- [GitHub Issues](https://github.com/djro-fr/breco_v2.0.0/issues)
- Contact: [syl.gi@laposte.net]

---

## Project Status

- **Version**: 2.0.0
- **Status**: In development
- **Last updated**: March 27, 2026
