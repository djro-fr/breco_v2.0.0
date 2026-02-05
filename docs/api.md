# Breco API Documentation

REST API documentation for the Breco v2.0.0 carpooling application.

---

## Interactive Documentation

**The complete, up-to-date API documentation is available via Swagger UI:**

- **Local**: http://localhost:8081/swagger
- **Production**: http://37.59.101.232:8081/swagger

---

## Authentication

The API uses **JWT (JSON Web Tokens)** for authentication.

### Required headers for protected routes

```http
Authorization: Bearer {your_token}
Content-Type: application/json
```

---

## Base URLs

| Environment       | URL                              |
| ----------------- | -------------------------------- |
| Local Development | `http://localhost:8081/api`      |
| VPS Production    | `http://37.59.101.232:8081/api`  |

---

**Last updated**: February 5, 2026
