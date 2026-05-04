# Breco, covoiturage breton

Welcome to the demo version of Breco, the carpooling application for Brittany.

---

## Quick Start

**New developer?** Start here: [Getting Started Guide](getting-started.md)

**Production deployment?** Check the [Production Checklist](todo-prod.md)

---

## Tech Stack

### Frontend

- Vue.js 3 + TypeScript
- Tailwind CSS v4
- Vite + Bun (dev) / Nginx (staging/production)

### Backend

- CakePHP 5.2.x
- MySQL 8.0
- PHP-FPM

### DevOps

- Docker + Docker Compose
- Jenkins CI/CD
- Nginx (reverse proxy)
- SonarQube (code quality)
- OWASP ZAP (security scan)
- Prometheus + Grafana (monitoring)
- cAdvisor (container metrics)

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
    └── breco - Test cases.xlsx
    └── testing.md
jenkins/
├── Dockerfile-jenkins     # Custom Jenkins image (VPS)
└── jenkins.md             # Jenkins update procedure
monitoring/
└── monitoring.md          # Prometheus + Grafana monitoring setup
...
frontend/
└── README.md                  # Frontend setup and development guide
backend/
└── README.md                  # Backend setup and API reference
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

#### Application Services

| | Frontend | Nginx | Swagger |
| --- | --- | --- | --- |
| **Local** | http://localhost:3001 | http://localhost:8081 | http://localhost:8081/swagger |
| **VPS** | http://37.59.101.232:3001 | http://37.59.101.232:8081 | http://37.59.101.232:8081/swagger |

#### DevOps Services

| | SonarQube | Jenkins | Grafana |
| --- | --- | --- | --- |
| **Local** | http://localhost:9000 | - | - |
| **VPS** | http://37.59.101.232:9000 | http://37.59.101.232:8080 | SSH tunnel: see [monitoring.md](../monitoring/monitoring.md) |

---

## Need Help?

### Common Issues

#### Frontend won't start

```bash
cd frontend/breco
rm -rf node_modules
bun install
bun run dev
```

#### Docker "port already in use" error

```bash
docker compose down
docker ps -a  # Check no container is using the port
docker compose up --build -d
```

#### "CORS policy" error

Check nginx configuration in `nginx/default.conf`: allowed origins must include your frontend URL.

#### Empty database after restart

MySQL volume persists. To reset:

```bash
docker compose down -v
docker compose up --build -d
# Then run migrations and seeds
```

#### Lost Jenkins admin access after reinstall

If Jenkins has no users after a reinstall (volume wiped), disable security temporarily:

```bash
ssh -p YOUR_SSH_PORT ubuntu@YOUR_VPS_IP "docker exec -u root breco-jenkins bash -c \"sed -i 's|<useSecurity>true</useSecurity>|<useSecurity>false</useSecurity>|' /var/jenkins_home/config.xml\""

ssh -p YOUR_SSH_PORT ubuntu@YOUR_VPS_IP "docker restart breco-jenkins"
```

Then go to `http://YOUR_VPS_IP:8080/securityRealm/addUser` and create your admin account.

Re-enable security in **Manage Jenkins → Security** and set:

- **Security Realm**: Jenkins' own user database
- **Authorization**: Logged-in users can do anything

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
main         # Stable / staging (production branch to be created at launch)
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
7. **Deploy**: Automatic deployment to VPS OVH (staging) if tests pass

---

## Project Metrics

- **Tests**: Unit, Integration, UI, E2E, PHPUnit
- **CI/CD**: Jenkins pipeline (Checkout → Lint → Tests (parallel: Unit, Integration, UI, PHP Unit)
→ SonarQube → Seed Test Data → E2E → Cleanup Test Data → Copy Test Results → SwaggerBake → Build → Deploy
→ Verify Deployment Version → Verify (health check) → OWASP ZAP → Seed JMeter User → JMeter → Cleanup Docker)
- **Deployment**: Automatic to VPS OVH (staging) on every push
- **Security**: OWASP ZAP baseline scan after each deployment
- **Security hardening**: SSH key auth only, Fail2ban IPS, custom SSH port

---

**Last updated**: May 4, 2026
