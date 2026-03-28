# Breco, covoiturage breton

Welcome in the demo version of Breco, the carpooling application for Brittany

---

## Quick Start

**New developer?** Start here: [Getting Started Guide](getting-started.md)  
**Production deployment?** Check the [Production Checklist](todo-prod.md)

---

## Tech Stack

### Frontend

- Vue.js 3 + TypeScript
- Tailwind CSS v4
- Vite + Bun

### Backend

- CakePHP 5.x
- MySQL 8.0
- PHP-FPM

### DevOps

- Docker + Docker Compose
- Jenkins CI/CD
- Nginx (reverse proxy)
- SonarQube (code quality)
- OWASP ZAP (security scan)

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
├── todo-prod.md           # Production checklist
└── tests/
    ├── breco - plan de test.odt
    └── breco - cas de test.xlsx

jenkins/
├── Dockerfile-jenkins     # Custom Jenkins image (VPS)
└── JENKINS.md             # Jenkins update procedure
```

---

## Useful Links

### External Resources

- [CakePHP 5 Documentation](https://book.cakephp.org/5/en/)
- [Vue.js 3 Documentation](https://vuejs.org/)
- [Docker Documentation](https://docs.docker.com/)
- [OWASP ZAP Documentation](https://www.zaproxy.org/docs/)

### Repositories

- GitHub: `breco_v2_0_0` (private)
- Docker Hub: `djrofr/breco-frontend`, `djrofr/breco-backend`

### Environments

| | Frontend | Nginx | Swagger | SonarQube | Jenkins |
| --- | --- | --- | --- | --- | --- |
| **Local** | http://localhost:3001 | http://localhost:8081 | http://localhost:8081/swagger | http://localhost:9000 | - |
| **VPS** | http://37.59.101.232:3001 | http://37.59.101.232:8081 | http://37.59.101.232:8081/swagger | http://37.59.101.232:9000 | http://37.59.101.232:8080 |

---

## Need Help?

### Common Issues

#### **Frontend won't start**

```bash
cd frontend/breco
rm -rf node_modules
bun install
bun run dev
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
feat:     new feature
fix:      bug fix
docs:     documentation
refactor: refactoring
test:     add tests
ci:       CI/CD pipeline changes
chore:    maintenance tasks
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
6. **Push**: Automatic Jenkins build triggered via GitHub webhook
7. **Deploy**: Automatic deployment to VPS if tests pass

---

## Project Metrics

- **Tests**: Unit, Integration, UI, E2E, PHPUnit
- **CI/CD**: Jenkins pipeline (Lint → SonarQube → Tests → Build → Deploy → ZAP)
- **Deployment**: Automatic to VPS on every push
- **Security**: OWASP ZAP baseline scan after each deployment
- **Security hardening**: SSH key auth only, Fail2ban IPS, custom SSH port

---

## Last Update

**Date**: March 28, 2026
