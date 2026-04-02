# Getting Started - Breco v2.0.0

> Quick start guide for new developers

---

## Quick Setup (5 minutes)

### Prerequisites

Check that you have installed:

```bash
# Docker & Docker Compose
docker --version          # 20.10+
docker-compose --version  # 2.0+

# Node.js & npm + bun
node --version           # 18.0+
npm --version            # 9.0+
bun --version            # 1.3+

# Git
git --version            # 2.0+
```

**No Docker?** → [Install Docker Desktop](https://www.docker.com/products/docker-desktop)  
**No Node.js?** → [Install Node.js](https://nodejs.org/)

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/djro-fr/breco_v2_0_0.git
cd breco_v2_0_0
```

### 2. Configure environment

```bash
# Copy .env files
cp .env.example .env
```

### 3. Start Docker services

```bash
docker-compose up -d
```

**Check everything is running**:

```bash
docker ps
```

You should see:

- breco_frontend (port 3001)
- breco_backend (port 8765)
- breco_nginx (port 8081)
- breco_mysql (port 3307)
- breco_mailhog (ports 1025, 8025)
- breco_sonarqube (port 9000)
- breco_sonarqube_db
- breco-jenkins (port 8080)
- breco_prometheus
- breco_grafana (port 3002, SSH tunnel only)
- breco_nginx_exporter
- breco_cadvisor

---

## Test Installation

### Backend

```bash
# Health check
curl http://localhost:8081/api/health

# Should return:
# {"status":"ok","service":"breco-backend","timestamp":"..."}
```

### Frontend

Open your browser: http://localhost:3001  
You should see the Breco home page.

### Mailhog (test emails)

Open: http://localhost:8025  
Interface to view emails sent during development.

---

## Local Development with Hot Reload

To develop with automatic frontend reload:

### Terminal 1: Backend Docker (without frontend)

```bash
docker-compose up -d backend mysql nginx mailhog
```

### Terminal 2: Frontend Dev (hot reload)

```bash
cd frontend/breco
bun install
bun run dev
```

Frontend will be available at: http://localhost:5173 (Vite dev server)

**Why 5173?** This is Vite's development server port with hot reload.  
**In production**: Frontend is on port 3001.

---

## Project Structure

```text
breco_v2_0_0/
├── frontend/
│   └── breco/
│       ├── src/
│       │   ├── main.ts                    # Entry point
│       │   ├── domain/
│       │   │   ├── entities/              # User.ts, Town.ts
│       │   │   ├── repositories/          # IAuthRepository.ts, ITownRepository.ts, IUserRepository.ts
│       │   │   ├── schemas/               # townSearch.schema.ts (Zod)
│       │   │   └── exceptions/            # AppException.ts
│       │   ├── application/
│       │   │   └── usecases/
│       │   │       ├── auth/              # Login, Logout, Register, VerifyToken
│       │   │       ├── town/              # SearchTownsUseCase.ts
│       │   │       ├── profile/
│       │   │       └── trip/
│       │   ├── data/
│       │   │   ├── datasources/remote/    # AuthRemoteDataSource.ts, TownRemoteDataSource.ts
│       │   │   ├── models/                # UserModel.ts, TownModel.ts
│       │   │   └── repositories/          # AuthRepositoryImpl.ts, TownRepositoryImpl.ts
│       │   ├── presentation/
│       │   │   ├── app/
│       │   │   │   ├── App.vue
│       │   │   │   ├── pages/             # HomePage, DashboardPage, SearchPage, NotFoundPage
│       │   │   │   └── stores/
│       │   │   └── features/
│       │   │       ├── auth/
│       │   │       │   ├── pages/         # LoginPage, RegisterPage, VerifyEmailPage
│       │   │       │   ├── router/        # authRoutes.ts
│       │   │       │   └── stores/        # authStore.ts
│       │   │       ├── carTrip/
│       │   │       ├── reservation/
│       │   │       └── search/
│       │   └── shared/
│       │       └── api/                   # axiosInstance.ts
│       ├── package.json
│       └── vite.config.ts
│
├── backend/
│   └── breco/
│       ├── src/
│       │   ├── Controller/Api/            # AuthController.php, TownsController.php, HealthController.php
│       │   ├── Service/                   # Auth, Town, Location, User
│       │   ├── Repository/                # TownRepository.php
│       │   ├── Dto/                       # Auth, Town, Location
│       │   └── Model/
│       │       ├── Entity/                # User.php, Town.php
│       │       └── Table/                 # UsersTable.php, TownsTable.php
│       ├── config/
│       │   ├── Migrations/                # Versioned tables
│       │   ├── Seeds/                     # TownsSeed.php, LocationsSeed.php
│       │   ├── routes.php
│       │   ├── swagger.yml
│       │   └── swagger_bake.php
│       ├── phpunit.xml.dist
│       └── Dockerfile-backend
│
├── nginx/
│   └── default.conf
├── mysql/
│   └── init.sql
├── jenkins/
│   ├── Dockerfile-jenkins
│   └── JENKINS.md
├── docker-compose.yml
├── Jenkinsfile
└── docs/
    ├── ARCHITECTURE.md
    ├── API.md
    ├── GETTING_STARTED.md
    └── tests/
        ├── breco - plan de test.odt
        └── breco - Test cases.xlsx
```

---

## Useful Commands

### Docker

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View logs
docker logs breco_backend
docker logs breco_frontend
docker logs breco_nginx

# Rebuild an image
docker-compose build backend
docker-compose build frontend

# Remove volumes (deletes database)
docker-compose down -v
```

### Database

```bash
# Connect to MySQL
docker exec -it breco_mysql mysql -u root -p breco_db
# With root password
```

```bash
# Migrations
docker exec -it breco_backend /app/bin/cake migrations migrate
```

```bash
# Empty users table
docker exec -it breco_mysql mysql -u root -p breco_db
```

```sql
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE drivers;
TRUNCATE TABLE passengers;
TRUNCATE TABLE bookings;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;
```

```bash
# Check empty users table
docker exec -it breco_mysql mysql -u root -p breco_db
```

```sql
SELECT COUNT(*) FROM users;
```

### Tests

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
docker exec -it breco_backend vendor/bin/phpunit --testdox --display-phpunit-notices

# Backend - PHPUnit local (without Docker)
cd backend/breco
vendor/bin/phpunit --testdox --display-phpunit-notices

# Backend - PHPUnit specific test file with notices (local)
vendor/bin/phpunit tests/TestCase/Service/Auth/AuthServiceTest.php --testdox --display-phpunit-notices
```

---

## CI/CD Pipeline (Jenkins)

The project uses a Jenkins pipeline hosted on the VPS.

| URL | Description |
| --- | --- |
| http://37.59.101.232:8080 | Jenkins dashboard |
| http://37.59.101.232:9000 | SonarQube (code quality) |

The pipeline runs automatically on each push to GitHub
and executes the following stages in order:
Checkout → Lint → Tests (Unit, Integration, UI, PHPUnit)
→ SonarQube Analysis → Seed → E2E → Cleanup → Copy Results
→ Swagger Bake → Build → Deploy → Verify
→ OWASP ZAP Security Scan → JMeter Performance → Cleanup Docker

For Jenkins update procedure, see [jenkins/JENKINS.md](../jenkins/JENKINS.md).

---

## OWASP ZAP Security Scan

The pipeline includes an automated security scan using **OWASP ZAP** (Zed Attack Proxy) after each deployment.

ZAP runs a passive baseline scan on the deployed application
(`http://37.59.101.232:8081`) and generates an HTML report archived in Jenkins.

To view the report: **Jenkins → Build → OWASP ZAP Security Report** (left menu).

---

## First Development

### 1. Create a user account

**Via Postman**:

```http
POST http://localhost:8081/api/auth/register
Content-Type: application/json

{
  "email": "dev@test.com",
  "password": "DevPass123!",
  "firstName": "Dev",
  "lastName": "Test",
  "phone": "0607080910"
}
```

**Via the interface**:

1. Open http://localhost:5173
2. Go to "Inscription (register)"
3. Fill in the form

### 2. Verify email

1. Open Mailhog: http://localhost:8025
2. Click on the received email
3. Click on the verification link

### 3. Login

```http
POST http://localhost:8081/api/auth/login
Content-Type: application/json

{
  "email": "dev@test.com",
  "password": "DevPass123!"
}
```

You will receive a JWT token to use in subsequent requests.

---

## Next Steps

Now that your environment is configured:

1. **Read the architecture** → [DDD Architecture](ARCHITECTURE.md)
2. **Understand the API** → [API Documentation](API.md)

---

## Common Issues

### Port already in use

```bash
# Error: "port 3001 is already allocated"
docker-compose down
lsof -ti:3001 | xargs kill -9  # Linux/Mac
# Windows: Task Manager → Kill process
docker-compose up -d
```

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

If you see a CORS error in the console:

1. Check `nginx/default.conf`
2. Verify nginx is running: `docker ps | grep nginx`
3. Restart nginx: `docker-compose restart nginx`

### Empty database after restart

MySQL data is persisted in a Docker volume.

To completely reset the database:

```bash
docker-compose down -v  # ⚠️ Removes volumes
docker-compose up -d
docker exec -it breco_backend /app/bin/cake migrations migrate
```

### npm install fails

```bash
# Clean npm cache
cd frontend/breco
rm -rf node_modules package-lock.json
npm cache clean --force
npm install
```

---

## Test Account

A test account already exists:

```text
Email    : test@test.com
Password : Password123
```

**Remove in production!**

---

## Used Ports

| Service | Local Port | Description |
| --- | --- | --- |
| Frontend | 3001 | Vue.js (production) |
| Frontend Dev | 5173 | Vite dev server (hot reload) |
| Backend | 8765 | API PHP-FPM (direct) |
| Nginx | 8081 | Reverse proxy |
| MySQL | 3307 | Database |
| Mailhog SMTP | 1025 | SMTP test server |
| Mailhog UI | 8025 | Email interface |
| Jenkins | 8080 | CI/CD pipeline |
| SonarQube | 9000 | Code quality analysis |
| Grafana | 3002 | Monitoring dashboards (SSH tunnel only) |

---

## Development Tips

### Recommended VSCode Extensions

- **Volar** (Vue Language Features)
- **ESLint**
- **Prettier**
- **Docker**
- **GitLens**

### VSCode Configuration

Create `.vscode/settings.json`:

```json
{
  "editor.formatOnSave": true,
  "editor.defaultFormatter": "esbenp.prettier-vscode",
  "[vue]": {
    "editor.defaultFormatter": "Vue.volar"
  }
}
```

---

## Development Workflow

### Development mode (recommended)

```bash
# Terminal 1: Docker services
docker-compose up -d backend mysql nginx mailhog

# Terminal 2: Frontend with hot reload
cd frontend/breco
bun run dev
# → http://localhost:5173
```

### Production mode (local testing)

```bash
# Everything via Docker
docker-compose up -d
# → http://localhost:3001
```

---

## Need Help?

- [Architecture](ARCHITECTURE.md)
- [API](API.md)
- [Jenkins](../jenkins/JENKINS.md)

---

**Date**: April 2, 2026
