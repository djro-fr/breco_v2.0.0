# Architecture Breco v2.0.0

Simplified Domain-Driven Design (DDD) architecture documentation

## Overview

```markdown
Composant Vue
    ↓ utilise
Composable (useUser)
    ↓ utilise
Repository (UserRepository)
    ↓ utilise
DataSource (ApiDataSource) + Model (UserModel)
    ↓ utilise
Entity (User)
```

---

## Folder structure

```markdown
src/
├── domain/
│   ├── entities/           # User.ts, Trajet.ts (logique métier + Zod)
│   └── repositories/       # IUserRepository.ts (interfaces)
│
├── data/
│   ├── datasources/        # ApiDataSource.ts, LocalStorageDataSource.ts
│   ├── models/             # UserModel.ts (DTO + conversions)
│   └── repositories/       # UserRepository.ts (implémentations)
│
├── composables/            # useUser.ts, useAuth.ts (état reactive)
├── components/             # UserForm.vue (UI)
└── utils/                  # validationSchemas.ts (Zod réutilisables)
```

---

## Layers

### 1. Domain Entity

**File** : `src/domain/entities/User.ts`

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

**Contains** : Properties + Business Methods + Zod Schemas
**Does not contain** : API calls, localStorage, DTO conversions

---

### 2. Domain Repository Interface

**File** : `src/domain/repositories/IUserRepository.ts`

```typescript
import type { User, CreateUserData, UpdateUserData } from '../entities/User'

export interface IUserRepository {
  getById(id: number): Promise<User>
  getAll(): Promise<User[]>
  create(userData: CreateUserData): Promise<User>
  update(id: number, updates: Partial<UpdateUserData>): Promise<User>
  delete(id: number): Promise<void>
}
```

**Contains**: Contract (interface)  
**Does not contain**: Implementation

---

### 3. Data Model (DTO)

**File** : `src/data/models/UserModel.ts`

```typescript
import { User, UserSchema } from '@/domain/entities/User'

export interface UserDTO {
  id: number
  email: string
  firstName: string
  lastName: string
  driver: boolean
}

export class UserModel {
  static fromJson(json: UserDTO): User {
    const validated = UserSchema.parse(json)
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

*Contains**: DTO + DTO Conversions ↔️ Entity  
**Does not contain** : Business logic, API calls

---

### 4. DataSource

**File** : `src/data/datasources/ApiDataSource.ts`

```typescript
import api from '@/services/api'
import type { UserDTO } from '@/data/models/UserModel'

export class ApiDataSource {
  async fetchUser(id: number): Promise<UserDTO> {
    const response = await api.get<{ user: UserDTO }>(`/api/users/${id}`)
    return response.data.user
  }

  async fetchUsers(): Promise<UserDTO[]> {
    const response = await api.get<{ users: UserDTO[] }>('/api/users')
    return response.data.users
  }

  async createUser(data: Partial<UserDTO>): Promise<UserDTO> {
    const response = await api.post<{ user: UserDTO }>('/api/users', data)
    return response.data.user
  }
}

export const apiDataSource = new ApiDataSource()
```

**File** : `src/data/datasources/LocalStorageDataSource.ts`

```typescript
export class LocalStorageDataSource {
  private readonly prefix = 'breco_'

  set<T>(key: string, data: T, ttlMinutes: number = 60): void {
    const cached = {
      data,
      expiresAt: Date.now() + ttlMinutes * 60 * 1000
    }
    localStorage.setItem(`${this.prefix}${key}`, JSON.stringify(cached))
  }

  get<T>(key: string): T | null {
    const item = localStorage.getItem(`${this.prefix}${key}`)
    if (!item) return null

    const cached = JSON.parse(item)
    if (Date.now() > cached.expiresAt) {
      this.remove(key)
      return null
    }
    return cached.data
  }

  remove(key: string): void {
    localStorage.removeItem(`${this.prefix}${key}`)
  }
}

export const localStorageDataSource = new LocalStorageDataSource()
```

**Contains**: Raw communication with ONE source  
**Does not contain**: Conversions, complex cache logic

---

### 5. Data Repository

**File** : `src/data/repositories/UserRepository.ts`

```typescript
import type { IUserRepository } from '@/domain/repositories/IUserRepository'
import { User, type CreateUserData } from '@/domain/entities/User'
import { UserModel, type UserDTO } from '@/data/models/UserModel'
import { apiDataSource } from '@/data/datasources/ApiDataSource'
import { localStorageDataSource } from '@/data/datasources/LocalStorageDataSource'

export class UserRepository implements IUserRepository {
  async getById(id: number): Promise<User> {
    // Cache
    const cached = localStorageDataSource.get<UserDTO>(`user_${id}`)
    if (cached) return UserModel.fromJson(cached)
    
    // API
    const dto = await apiDataSource.fetchUser(id)
    localStorageDataSource.set(`user_${id}`, dto, 15)
    
    return UserModel.fromJson(dto)
  }

  async getAll(): Promise<User[]> {
    const dtos = await apiDataSource.fetchUsers()
    return dtos.map(dto => UserModel.fromJson(dto))
  }

  async create(userData: CreateUserData): Promise<User> {
    const dto = await apiDataSource.createUser(userData)
    return UserModel.fromJson(dto)
  }
}

export const userRepository = new UserRepository()
```

**Contains**: DataSources Orchestration + Cache + Conversions  
**Does not contain**: Business logic, reactive state

---

### 6. Composable

**File** : `src/composables/useUser.ts`

```typescript
import { ref } from 'vue'
import { userRepository } from '@/data/repositories/UserRepository'
import { User, CreateUserSchema, type CreateUserData } from '@/domain/entities/User'
import { z } from 'zod'

export function useUser() {
  const currentUser = ref<User | null>(null)
  const loading = ref(false)
  const errors = ref<Record<string, string>>({})

  const fetchUser = async (id: number) => {
    loading.value = true
    try {
      currentUser.value = await userRepository.getById(id)
    } finally {
      loading.value = false
    }
  }

  const createUser = async (userData: CreateUserData) => {
    loading.value = true
    errors.value = {}
    
    try {
      const validated = CreateUserSchema.parse(userData)
      currentUser.value = await userRepository.create(validated)
    } catch (error) {
      if (error instanceof z.ZodError) {
        error.errors.forEach(err => {
          errors.value[err.path[0] as string] = err.message
        })
      }
      throw error
    } finally {
      loading.value = false
    }
  }

  return { currentUser, loading, errors, fetchUser, createUser }
}
```

**Contains**: Reactive state + Validation + Orchestration  
**Does not contain**: API calls, business logic

---

### 7. Vue Component

**File** : `src/components/UserForm.vue`

```vue
<script setup lang="ts">
import { ref } from 'vue'
import { useUser } from '@/composables/useUser'
import type { CreateUserData } from '@/domain/entities/User'

const { createUser, loading, errors, currentUser } = useUser()

const formData = ref<Partial<CreateUserData>>({
  email: '',
  firstName: '',
  lastName: '',
  driver: false
})

const handleSubmit = async () => {
  await createUser(formData.value as CreateUserData)
  console.log('Nom complet:', currentUser.value?.getFullName())
}
</script>

<template>
  <form @submit.prevent="handleSubmit">
    <input v-model="formData.email" placeholder="Email" />
    <span v-if="errors.email">{{ errors.email }}</span>
    
    <button type="submit" :disabled="loading">Créer</button>
  </form>
</template>
```

---

## Simple rule: Where to put my code?

| Question |   | Location |
|----------|---|--------------|
| Business logic? |   | Domain Entity |
| HTTP/localStorage call? |   | DataSource |
| Orchestration sources? |   | Repository |
| DTO Conversion ↔️ Entity? |   | Model |
| Contract/Interface? |   | Domain Interface |
| Reactive state? |   | Composable |
| UI/Template? |   | Component View |

---

## Data flow

```markdown
1. User clicks → Vue Component
2. handleSubmit() → Composable useUser()
3. createUser() → UserRepository
4. create() → ApiDataSource + LocalStorageDataSource
5. POST /api/users → Backend
6. response → UserDTO
7. fromJson() → User Entity
8. return User with Business Methods
```

---

## Key principle

**Domain does not depend on ANYTHING.**

```markdown
Data depends on Domain
Composable depends on Domain + Data
Component depends on Composable

Independent domain
```

---

## Auth Example with several sources

```typescript
// src/data/repositories/AuthRepository.ts
export class AuthRepository implements IAuthRepository {
  async login(input: LoginInput): Promise<AuthOutput> {
    // 1. API
    const dto = await apiDataSource.login(input)
    
    // 2. localStorage
    localStorageDataSource.set('token', dto.token)
    
    // 3. Conversion
    const user = UserModel.fromJson(dto.user)
    
    return { token: dto.token, user }
  }
}
```

---

## Benefits

- ✅ **Testable**: Testing Entity without API
- ✅ **Flexible**: Change API = modify DataSource only
- ✅ **Claire**: A File = a role
- ✅ **Scalable**: Add features without breaking the existing

---

## Checklist

```typescript
// ✅ Domain Entity
getFullName(): string { return `${this.firstName} ${this.lastName}` }

// ✅ DataSource
async fetchUser(id: number): Promise<UserDTO> { ... }

// ✅ Model
static fromJson(dto: UserDTO): User { ... }

// ✅ Repository
async getById(id: number): Promise<User> {
  const cached = cache.get()
  if (cached) return cached
  return api.fetch()
}

// ✅ Composable
const { user, loading } = useUser()

// ✅ Composant
<template>{{ user.getFullName() }}</template>
```
