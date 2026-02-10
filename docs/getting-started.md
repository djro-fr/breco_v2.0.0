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

# Node.js & npm
node --version           # 18.0+
npm --version            # 9.0+

# Git
git --version            # 2.0+
```

**No Docker?** → [Install Docker Desktop](https://www.docker.com/products/docker-desktop)  
**No Node.js?** → [Install Node.js](https://nodejs.org/)

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-username/breco_v2_0_0.git
cd breco_v2_0_0
```

### 2. Configure environment

```bash
# Copy .env files (if needed)
# cp .env.example .env
```

### 3. Start Docker services

**On Windows**:

```bash
docker-compose up -d
```

**On Linux**:

```bash
docker-compose -f docker-compose.linux.yml up -d
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

---

## Test Installation

### Backend

```bash
# Health check
curl http://localhost:8081/api/health

# Should return:
# {"status":"ok","service":"breco-backend","timestamp":"...","version":"2.0.0"}
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
# Windows
docker-compose up -d backend mysql nginx mailhog

# Linux
docker-compose -f docker-compose.linux.yml up -d backend mysql nginx mailhog
```

### Terminal 2: Frontend Dev (hot reload)

```bash
cd frontend/breco
npm install
npm run dev
```

Frontend will be available at: http://localhost:5173 (Vite dev server)

**Why 5173?** This is Vite's development server port with hot reload.  
**In production**: Frontend is on port 3001.

---

## Project Structure

```text
breco_v2_0_0/
├── frontend/
│   └── breco/              # Vue.js application
│       ├── src/
│       │   ├── domain/     # Entities, Repositories (interfaces)
│       │   ├── data/       # DataSources, Models, Repositories (impls)
│       │   ├── composables/# Reactive logic (useUser, useAuth)
│       │   ├── components/ # Vue components
│       │   └── views/      # Pages
│       └── package.json
│
├── backend/
│   └── breco/              # CakePHP application
│       ├── src/
│       │   ├── Controller/ # API controllers
│       │   ├── Model/      # Entities, Tables
│       │   └── Service/    # Business services
│       ├── config/         # Configuration
│       └── composer.json
│
├── nginx/
│   └── default.conf        # Nginx configuration
│
├── docker-compose.yml      # Docker Windows
├── docker-compose.linux.yml# Docker Linux
├── Jenkinsfile             # CI/CD pipeline
└── docs/                   # Documentation
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
# Password: root
```

```bash
# Migrations
docker-compose exec backend bin/cake migrations migrate
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

# Frontend - Linting
npm run lint

# Backend - Tests (coming soon)
docker-compose exec backend vendor/bin/phpunit
```

---

## First Development

### 1. Create a user account

**Via Postman/Insomnia**:

```http
POST http://localhost:8081/api/auth/register
Content-Type: application/json

{
  "email": "dev@test.com",
  "password": "DevPass123!",
  "first_name": "Dev",
  "last_name": "Test"
}
```

**Via the interface**:

1. Open http://localhost:5173
2. Go to "Sign up"
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

1. **Read the architecture** → [DDD Architecture](architecture.md)
2. **Understand the API** → [API Documentation](api.md)
3. **See the ports** → [Endpoints & Ports](endpoints.md)

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

Check `frontend/breco/src/services/api.ts`:

```typescript
const API_BASE_URL = 'http://localhost:8081/api'
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
docker-compose exec backend bin/cake migrations migrate
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
| ------- | ---------- | ----------- |
| Frontend | 3001 | Vue.js (production) |
| Frontend Dev | 5173 | Vite dev server (hot reload) |
| Backend | 8765 | API PHP-FPM (direct) |
| Nginx | 8081 | Reverse proxy |
| MySQL | 3307 | Database |
| Mailhog SMTP | 1025 | SMTP test server |
| Mailhog UI | 8025 | Email interface |

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

### Git Hooks (optional)

```bash
# Install husky for pre-commit hooks
cd frontend/breco
npm install -D husky
npx husky init
```

---

## Development Workflow

### Development mode (recommended)

```bash
# Terminal 1: Docker services
docker-compose up -d backend mysql nginx mailhog

# Terminal 2: Frontend with hot reload
cd frontend/breco
npm run dev
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

- [Complete documentation](README.md)
- [Architecture](architecture.md)
- [API](api.md)
- [Endpoints](endpoints.md)

---

**Last updated**: January 29, 2026
