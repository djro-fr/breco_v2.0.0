# Breco Frontend

Vue.js 3 frontend application for the Breco carpooling platform.

## Tech Stack

- **Framework**: Vue.js 3 with TypeScript
- **Build Tool**: Vite
- **Styling**: Tailwind CSS v4
- **State Management**: Pinia (optional)
- **Architecture**: Domain-Driven Design (DDD)

## Quick Start

### Development Server

```bash
npm install
npm run dev
```

Frontend will be available at: http://localhost:5173

### Build for Staging/Production

```bash
npm run build
```

### Run Tests

```bash
npm run test:unit
```

### Lint

```bash
npm run lint
```

## Project Structure

```text
src/
├── domain/           # Business entities and interfaces
│   ├── entities/     # User.ts, Trajet.ts
│   └── repositories/ # IUserRepository.ts
├── data/             # Data layer
│   ├── datasources/  # API, localStorage
│   ├── models/       # DTOs
│   └── repositories/ # Repository implementations
├── composables/      # Vue composables (useUser, useAuth)
├── components/       # Vue components
├── views/            # Page components
└── services/         # API client
```

## Documentation

For complete documentation, see the main [docs folder](../../docs/):

- [Getting Started](../../docs/getting-started.md)
- [Architecture](../../docs/architecture.md)
- [API Documentation](../../docs/api.md)

## Environment Variables

Create a `.env` file (see `.env.example`):

```bash
VITE_API_BASE_URL=http://localhost:8081/api
```

## VSCode Extensions

Recommended:

- Volar (Vue Language Features)
- ESLint
- Prettier
- Tailwind CSS IntelliSense

## Available Scripts

| Command | Description |
| ------- | ----------- |
| `npm run dev` | Start dev server (hot reload) |
| `npm run build` | Build for Staging/Production |
| `npm run preview` | Preview Staging/Production build |
| `npm run test:unit` | Run unit tests |
| `npm run lint` | Lint code |
| `npm run format` | Format code with Prettier |

---

**Part of [Breco v2.0.0](../../README.md)**

---

**Last updated**: April 25, 2026
