# Breco v2.0.0 Documentation

> Complete documentation for the Breco carpooling application for Brittany

---

## Quick Start

**New developer?** Start here: [Getting Started Guide](getting-started.md)

**Production deployment?** Check the [Production Checklist](todo-prod.md)

---

## Documentation by Theme

### For Developers

| Document | Description |
| -------- | ----------- |
| [Getting Started](getting-started.md) | Development environment setup |
| [Architecture](architecture.md) | Detailed DDD architecture |
| [Error Handling](error-handling.md) | Application error management |

### API & Integration

| Document | Description |
| -------- | ----------- |
| [API Documentation](api.md) | API routes, JWT authentication, examples |
| [Endpoints & Ports](endpoints.md) | Docker ports and services configuration |

### DevOps & Production

| Document | Description |
| -------- | ----------- |
| [Production Checklist](todo-prod.md) | Complete checklist before production |

---

## By Use Case

### I want to

#### Start the project locally

→ [Getting Started](getting-started.md)

#### Understand the architecture

→ [DDD Architecture](architecture.md)

#### Develop a new feature

1. [Architecture](architecture.md) - Understand where to place code
2. [API Documentation](api.md) - See existing routes
3. [Error Handling](error-handling.md) - Handle errors

#### Deploy to production

1. [Endpoints & Ports](endpoints.md) - Check configuration
2. [Production Checklist](todo-prod.md) - Follow the checklist

#### Debug an issue

1. [Endpoints & Ports](endpoints.md) - Check services
2. [Error Handling](error-handling.md) - Understand errors
3. Docker logs: `docker logs breco_backend`

---

## Tech Stack

### Frontend

- Vue.js 3 + TypeScript
- Tailwind CSS v4
- Vite + npm

### Backend

- CakePHP 5.x
- MySQL 8.0
- PHP-FPM

### DevOps

- Docker + Docker Compose
- Jenkins CI/CD
- Nginx (reverse proxy)

---

## Documentation Structure

```text
docs/
├── README.md              # This file - Documentation index
├── getting-started.md     # Quick setup for developers
├── architecture.md        # Detailed DDD architecture
├── api.md                 # Complete API documentation
├── endpoints.md           # Ports and services
├── error-handling.md      # Error management
└── todo-prod.md           # Production checklist
```

---

## Useful Links

### External Resources

- [CakePHP 5 Documentation](https://book.cakephp.org/5/en/)
- [Vue.js 3 Documentation](https://vuejs.org/)
- [Docker Documentation](https://docs.docker.com/)

### Repositories

- GitHub: `breco_v2_0_0` (private)
- Docker Hub: `djrofr/breco-frontend`, `djrofr/breco-backend`

### Environments

- **Local**: http://localhost:3001 (frontend), http://localhost:8081 (nginx)
- **VPS**: http://37.59.101.232:3001 (frontend), http://37.59.101.232:8081 (nginx)
- **Jenkins**: http://37.59.101.232:8080

---

## Need Help?

### Common Issues

#### **Frontend won't start**

```bash
cd frontend/breco
rm -rf node_modules
npm install
npm run dev
```

#### **Docker "port already in use" error**

```bash
docker-compose down
docker ps -a  # Check no container is using the port
docker-compose up -d
```

**"CORS policy" error**  
→ Check nginx configuration in `nginx/default.conf`

**Empty database after restart**  
→ MySQL volume persists. To reset: `docker-compose down -v`

---

## Code Conventions

### Commits

```text
feat: new feature
fix: bug fix
docs: documentation
refactor: refactoring
test: add tests
chore: maintenance tasks
```

### Branches

```text
main         # Production
develop      # Development
feature/*    # New features
fix/*        # Bug fixes
```

---

## Development Workflow

1. **Clone**: `git clone [repository]`
2. **Setup**: Follow [Getting Started](getting-started.md)
3. **Develop**: Create branch `feature/my-feature`
4. **Test**: Run tests (unit, integration, E2E)
5. **Commit**: Follow commit conventions
6. **Push**: Automatic Jenkins build
7. **Deploy**: Automatic deployment to VPS if tests pass

---

## Project Metrics

- **Lines of code**: ~15,000
- **Tests**: Unit, Integration, UI
- **Coverage**: In development
- **CI/CD**: Jenkins 8 stages
- **Deployment**: Automatic to VPS

---

## Last Update

**Date**: January 29, 2026  
**Status**: In development
