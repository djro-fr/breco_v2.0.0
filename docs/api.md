# Breco API Documentation

API documentation for the Breco v2.0.0 carpooling application

---

## Base URL

**Local**: `http://localhost:8081/api`  
**VPS**: `http://37.59.101.232:8081/api`

---

## Authentication

The API uses **JWT (JSON Web Tokens)**.

### Required headers for protected routes

```http
Authorization: Bearer {your_token}
Content-Type: application/json
```

---

## Available Routes

### 1. POST /api/auth/register

Create a new account.

Request

```json
{
  "email": "user@example.com",
  "password": "SecurePass123!",
  "first_name": "John",
  "last_name": "Doe"
}
```

Response: `201 Created`

```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "email_verified": false
  }
}
```

---

### 2. GET /api/auth/verify-email/{token}

Verify email with the token received by email.

**Response**: `200 OK`

```json
{
  "message": "Email verified successfully",
  "user": {
    "id": 1,
    "email_verified": true
  }
}
```

---

### 3. POST /api/auth/login

Login and get a JWT token.

Request

```json
{
  "email": "user@example.com",
  "password": "SecurePass123!"
}
```

Response: `200 OK`

```json
{
  "message": "Login successful",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "first_name": "John",
    "last_name": "Doe"
  }
}
```

---

### 4. POST /api/auth/logout

Logout (requires authentication).

Headers

```http
Authorization: Bearer {token}
```

Response: `200 OK`

```json
{
  "message": "Logged out successfully"
}
```

---

### 5. GET /api/auth/verify

Check if token is valid (requires authentication).

Headers

```http
Authorization: Bearer {token}
```

Response: `200 OK`

```json
{
  "valid": true,
  "user": {
    "id": 1,
    "email": "user@example.com"
  }
}
```

---

### 6. GET /api/health

Backend health check (no authentication required).

**Response**: `200 OK`

```json
{
  "status": "ok",
  "service": "breco-backend",
  "timestamp": "2026-01-29 12:30:45",
  "version": "2.0.0"
}
```

---

## Common Error Codes

| Code | Description |
| ---- | ----------- |
| 200 | Success |
| 201 | Created successfully |
| 400 | Invalid request |
| 401 | Not authenticated |
| 403 | Access denied |
| 404 | Resource not found |
| 409 | Conflict (e.g., email already exists) |
| 500 | Server error |

---

## Complete Example (JavaScript)

```javascript
// 1. Registration
const register = await fetch('http://localhost:8081/api/auth/register', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'john@example.com',
    password: 'SecurePass123!',
    first_name: 'John',
    last_name: 'Doe'
  })
});

// 2. Login (after email verification)
const login = await fetch('http://localhost:8081/api/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'john@example.com',
    password: 'SecurePass123!'
  })
});

const { token } = await login.json();
localStorage.setItem('token', token);

// 3. Use the token
const response = await fetch('http://localhost:8081/api/protected-route', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
});
```

---

## Security

- JWT token valid for 24h
- HTTPS mandatory in production (TODO)
- CORS configured
- Store token securely (localStorage)
- NEVER share the token

---

**Last updated**: January 29, 2026
