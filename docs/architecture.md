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
| ------- | ---------- | --------- |
| **Validation** | Zod (input + response) | DTO (input) |
| **Logic** | UseCase | Service |
| **Data** | DataSource (API) | Repository (SQL) |
| **HTTP** | Composable | Controller |
