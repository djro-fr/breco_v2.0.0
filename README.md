# Breco v2.0.0

> Carpooling application for the Brittany region, France

Modern web platform allowing users to offer and search for carpooling trips in Brittany.

---

## Quick Start

**New developer?** → [Getting Started Guide (5 min)](docs/getting-started.md)

### Launch the application locally

```bash
# Start all services
docker-compose up -d

# Check everything is working
curl http://localhost:8081/api/health
```

Frontend: http://localhost:3001  
API: http://localhost:8081/api  
Mailhog: http://localhost:8025

---

## Documentation

| Document | Description |
| -------- | ----------- |
| **[Documentation Index](docs/README.md)** | Complete overview |
| **[Getting Started](docs/getting-started.md)** | Quick setup (5 min) |
| [Architecture](docs/architecture.md) | DDD architecture |
| [API](docs/api.md) | Routes and authentication |
| [Endpoints](docs/endpoints.md) | Ports and services |
| [Error Handling](docs/error-handling.md) | Error management |
| [Production Checklist](docs/todo-prod.md) | Before deployment |

---

## Tech Stack

- **Frontend**: Vue.js 3, TypeScript, Tailwind CSS v4, Zod
- **Backend**: CakePHP 5.x, MySQL 8.0, Firebase JWT
- **API Docs**: SwaggerBake 3.x (OpenAPI / PHP 8 attributes)
- **Testing**: Vitest, PHPUnit, Selenium
- **DevOps**: Docker, Jenkins CI/CD, Nginx

---

## Development

### Dev mode with hot reload (recommended)

**Terminal 1** - Backend services:

```bash
# Windows
docker-compose up -d backend mysql nginx mailhog

# Linux
docker-compose -f docker-compose.linux.yml up -d backend mysql nginx mailhog
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
# Windows
docker-compose up -d

# Linux
docker-compose -f docker-compose.linux.yml up -d
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

**Frontend**:

```bash
docker build --build-arg BUILD_NUMBER=1 -t local/breco-frontend:latest -f frontend/breco/Dockerfile-frontend frontend/breco
docker-compose down
docker-compose up -d
```

**Backend**:

```bash
docker build --build-arg BUILD_NUMBER=1 -t local/breco-backend:latest -f frontend/breco/Dockerfile-backend backend/breco
docker-compose down
docker-compose up -d
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

### Test API (Postman/Insomnia)

**Registration**:

```http
POST http://localhost:8081/api/auth/register
Content-Type: application/json

{
  "email": "test@example.com",
  "password": "Test1234!",
  "password_confirmation": "Test1234!",
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
# Frontend unit tests (Vitest)
cd frontend/breco
npm run test

# Backend unit tests (PHPUnit)
docker-compose exec backend vendor/bin/phpunit

# E2E tests (Selenium)
# See docs/testing.md for setup
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
| Swagger UI | http://localhost:8081/api/swagger |

### VPS (staging)

| Service | URL |
| ------- | --- |
| Frontend | http://37.59.101.232:3001 |
| API | http://37.59.101.232:8081 |
| Jenkins | http://37.59.101.232:8080 |
| Mailhog | http://37.59.101.232:8025 |

**SSH Access**: `ssh ubuntu@37.59.101.232`

---

## Quick Troubleshooting

### Port already in use

```bash
docker-compose down
lsof -ti:3001 | xargs kill -9  # Linux/Mac
# Windows: Task Manager → Kill process
docker-compose up -d
```

### Frontend cannot connect to backend

Check `frontend/breco/src/services/api.ts`:

```typescript
const API_BASE_URL = 'http://localhost:8081/api'
```

### CORS error

```bash
# Check nginx
docker ps | grep nginx
docker logs breco_nginx

# Restart nginx
docker-compose restart nginx
```

### Registration returns "Tous les champs sont requis"

⚠️ **Known issue** — Verify that `AuthController.php` correctly parses the JSON body.  
Add debug logs to inspect received data before validation.

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

- JWT authentication (custom `JwtAuthTrait` — Firebase JWT)
- Email verification (Mailhog)
- DDD / Clean Architecture (frontend + backend)
- OpenAPI documentation (SwaggerBake 3.x, PHP 8 attributes)
- Zod validation (presentation layer — `useTownSearch.ts`)
- Jenkins CI/CD pipeline (deployed on OVH VPS)
- Docker Compose (multi-service)
- Health check endpoint
- Test plan: 53+ test cases (Vitest / PHPUnit / Selenium pyramid)

### TODO before production

See [Production Checklist](docs/todo-prod.md) for complete list.

Immediate priorities:

- [ ] Fix registration bug (`AuthController.php` — JSON parsing)
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
- **Last updated**: March 25, 2026
