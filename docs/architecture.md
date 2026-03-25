# Architecture - Breco v2.0.0

Clean Architecture with separation of concerns

---

## Frontend (Vue 3 + TypeScript)

```text
Component > Composable > UseCase > Repository > DataSource > API
```

### Frontend structure

```text
src/
├── domain/         # Entities (Zod schemas) + Repository interfaces
├── application/    # UseCases (business logic)
├── data/           # DataSources + Models (DTO <> Entity) + Repository impl
├── presentation/   # Composables (state) + Components (UI)
└── shared/         # axiosInstance, utils
```

---

## Backend (CakePHP 5 + PHP 8.3)

```text
HTTP > Controller > Service > Repository > CakePHP ORM > DB
```

### Backend structure

```text
src/
├── Controller/Api/   # HTTP handling
├── Service/          # Business logic
├── Repository/       # SQL queries
├── Dto/              # Input validation
└── Model/            # CakePHP ORM (Entity + Table)
```

---

## Key Layers

| Layer | Frontend | Backend |
| --- | --- | --- |
| **Validation** | Zod (input + response) | DTO (input) |
| **Logic** | UseCase | Service |
| **Data** | DataSource (API) | Repository (SQL) |
| **HTTP** | Composable | Controller |

---

## DevOps

```text
GitHub Push
    ↓
Jenkins Pipeline
    ├── Lint
    ├── SonarQube Analysis    ← code quality
    ├── Tests (parallel)
    │   ├── Unit (Vitest)
    │   ├── Integration (Vitest)
    │   ├── UI (Vitest)
    │   ├── E2E (Selenium)
    │   └── PHPUnit
    ├── Swagger Bake           ← API documentation generation
    ├── Docker Build
    ├── Docker Push (Hub)
    ├── Deploy (VPS via SSH)
    ├── Verify deployment
    └── OWASP ZAP Scan         ← security baseline scan
```

### Infrastructure (VPS OVH)

```text
VPS Ubuntu 37.59.101.232
├── breco_nginx       (port 8081)   ← reverse proxy, ZAP scan target
├── breco_frontend    (port 3001)   ← Vue.js
├── breco_backend     (port 8765)   ← CakePHP PHP-FPM
├── breco_mysql       (port 3307)   ← MySQL 8.0
├── breco_mailhog     (port 8025)   ← email testing (dev only)
├── breco_sonarqube   (port 9000)   ← code quality
└── breco-jenkins     (port 8080)   ← CI/CD (standalone container)
```

---

**Last updated**: March 25, 2026
