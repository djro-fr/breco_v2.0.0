# Architecture - Breco v2.0.0

Domain-Driven Design (DDD) simplified architecture

## Overview

```text
Component → Composable → Repository → DataSource + Model → Entity
```

---

## Structure

```text
src/
├── domain/
│   ├── entities/      # User.ts (business logic + Zod schemas)
│   └── repositories/  # IUserRepository.ts (interfaces)
├── data/
│   ├── datasources/   # ApiDataSource.ts, LocalStorageDataSource.ts
│   ├── models/        # UserModel.ts (DTO conversions)
│   └── repositories/  # UserRepository.ts (implementations)
├── composables/       # useUser.ts (reactive state)
└── components/        # UserForm.vue (UI)
```

---

## Layers

### Entity (Domain)

Business logic + Zod validation

```typescript
import { z } from 'zod'

// Zod schemas
export const UserSchema = z.object({
  id: z.number().int().positive(),
  email: z.string().email(),
  firstName: z.string().min(2),
  lastName: z.string().min(2),
  driver: z.boolean().default(false)
})

export const CreateUserSchema = UserSchema.omit({ id: true })

export type UserData = z.infer<typeof UserSchema>
export type CreateUserData = z.infer<typeof CreateUserSchema>

// Entity with business methods
export class User {
  constructor(
    public id: number,
    public email: string,
    public firstName: string,
    public lastName: string,
    public driver: boolean = false
  ) {}

  getFullName(): string {
    return `${this.firstName} ${this.lastName}`
  }

  canPublishTrip(): boolean {
    return this.driver && this.hasVehicleInfo()
  }
}
```

### DataSource (Data)

API/localStorage communication

```typescript
export class ApiDataSource {
  async fetchUser(id: number): Promise<UserDTO> {
    const response = await api.get(`/api/users/${id}`)
    return response.data.user
  }

  async createUser(data: Partial<UserDTO>): Promise<UserDTO> {
    const response = await api.post('/api/users', data)
    return response.data.user
  }
}
```

### Model (Data)

DTO ↔️ Entity conversion with Zod validation

```typescript
export class UserModel {
  static fromJson(json: UserDTO): User {
    const validated = UserSchema.parse(json) // Zod validation
    return new User(
      validated.id,
      validated.email,
      validated.firstName,
      validated.lastName,
      validated.driver
    )
  }

  static toJson(user: User): UserDTO {
    return {
      id: user.id,
      email: user.email,
      firstName: user.firstName,
      lastName: user.lastName,
      driver: user.driver
    }
  }
}
```

### Repository (Data)

Orchestrate DataSources + cache

```typescript
export class UserRepository {
  async getById(id: number): Promise<User> {
    const cached = localStorage.get(`user_${id}`)
    if (cached) return UserModel.fromJson(cached)
    
    const dto = await apiDataSource.fetchUser(id)
    localStorage.set(`user_${id}`, dto)
    return UserModel.fromJson(dto)
  }

  async create(userData: CreateUserData): Promise<User> {
    const dto = await apiDataSource.createUser(userData)
    return UserModel.fromJson(dto)
  }
}
```

### Composable (Presentation)

Reactive state + Zod validation

```typescript
import { z } from 'zod'

export function useUser() {
  const user = ref<User | null>(null)
  const loading = ref(false)
  const errors = ref<Record<string, string>>({})

  const createUser = async (userData: CreateUserData) => {
    loading.value = true
    errors.value = {}
    
    try {
      // Zod validation before API call
      const validated = CreateUserSchema.parse(userData)
      user.value = await userRepository.create(validated)
    } catch (error) {
      if (error instanceof z.ZodError) {
        error.errors.forEach(err => {
          errors.value[err.path[0]] = err.message
        })
      }
    } finally {
      loading.value = false
    }
  }

  return { user, loading, errors, createUser }
}
```

### Component (Presentation)

UI with error display

```vue
<script setup lang="ts">
const { user, loading, errors, createUser } = useUser()

const formData = ref({
  email: '',
  firstName: '',
  lastName: '',
  driver: false
})

const handleSubmit = async () => {
  await createUser(formData.value)
}
</script>

<template>
  <form @submit.prevent="handleSubmit">
    <input v-model="formData.email" placeholder="Email" />
    <span v-if="errors.email" class="error">{{ errors.email }}</span>
    
    <input v-model="formData.firstName" placeholder="First Name" />
    <span v-if="errors.firstName" class="error">{{ errors.firstName }}</span>
    
    <button type="submit" :disabled="loading">Create</button>
  </form>
</template>
```

---

## Quick Reference

| What? | Where? |
| ----- | ------ |
| Business logic | Entity |
| Zod schemas | Entity |
| API/localStorage | DataSource |
| DTO conversion + validation | Model |
| Cache + orchestration | Repository |
| Reactive state + validation | Composable |
| UI + error display | Component |

---

## Data Flow with Validation

```text
User submits form
  → Component calls Composable
    → Composable validates with Zod (CreateUserSchema)
      → If valid: Composable calls Repository
        → Repository calls DataSource
          → DataSource fetches data
        → Repository validates response with Zod (UserSchema)
        → Repository converts to Entity
      → Composable updates reactive state
    → If invalid: Composable sets errors
  → Component displays result or errors
```

---

## Key Principle

Domain is independent - doesn't depend on anything.

```text
Data → depends on Domain
Composable → depends on Data
Component → depends on Composable
```

Validation happens at 2 points:

1. Composable: Validate user input before sending
2. Model: Validate API response before using

---

## Benefits

- Type-safe: Zod ensures runtime type checking
- Testable: Test entities without API
- Flexible: Change API without touching business logic
- Clear: One file = one responsibility
- Scalable: Add features without breaking existing code
