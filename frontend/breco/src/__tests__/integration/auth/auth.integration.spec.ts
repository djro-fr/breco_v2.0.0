import { describe, it, expect, beforeEach, vi } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '@/presentation/features/auth/stores/authStore'

vi.mock('@/data/repositories/AuthRepositoryImpl', () => ({
  AuthRepositoryImpl: vi.fn().mockImplementation(() => ({
    login: vi.fn().mockResolvedValue({
      user: { id: 1, email: 'test@test.com', firstName: 'John', lastName: 'Doe', phone: '+33612345678', driver: false },
      token: 'jwt-token-123',
    }),
    register: vi.fn().mockResolvedValue({
      user: { id: 2, email: 'newuser@test.com', firstName: 'Jane', lastName: 'Smith', phone: '+33712345678', driver: true },
      token: 'jwt-token-456',
    }),
    logout: vi.fn().mockResolvedValue({}),
    verifyToken: vi.fn().mockResolvedValue({ id: 1, email: 'test@test.com', firstName: 'John', lastName: 'Doe', phone: '+33612345678', driver: false }),
  })),
}))

describe('Auth Integration', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
  })

  it('should complete login flow', async () => {
    const store = useAuthStore()

    expect(store.isAuthenticated).toBe(false)

    await store.login('test@test.com', 'password123')

    expect(store.isAuthenticated).toBe(true)
    expect(store.user?.email).toBe('test@test.com')
    expect(store.token).toBe('jwt-token-123')

    await store.logout()

    expect(store.isAuthenticated).toBe(false)
    expect(store.user).toBeNull()
    expect(store.token).toBeNull()
  })

  it('should complete register flow', async () => {
    const store = useAuthStore()

    await store.register(
      'newuser@test.com',
      '+33712345678',
      'SecurePass123!',
      'Jane',
      'Smith',
      true
    )

    expect(store.isAuthenticated).toBe(true)
    expect(store.user?.email).toBe('newuser@test.com')
    expect(store.token).toBe('jwt-token-456')
  })

  it('should verify token on app startup', async () => {
    const store = useAuthStore()

    await store.login('test@test.com', 'password123')
    await store.checkAuth()

    expect(store.isAuthenticated).toBe(true)
    expect(store.user?.email).toBe('test@test.com')
  })

  it('should handle multiple sequential operations', async () => {
    const store = useAuthStore()

    await store.login('test@test.com', 'password123')
    expect(store.isAuthenticated).toBe(true)

    store.error = null
    expect(store.error).toBeNull()

    expect(store.isAuthenticated).toBe(true)

    await store.logout()
    expect(store.isAuthenticated).toBe(false)
  })

  it('should clear error on new login', async () => {
    const store = useAuthStore()

    store.error = 'Previous error'

    await store.login('test@test.com', 'password123')

    expect(store.error).toBeNull()
    expect(store.isAuthenticated).toBe(true)
  })
})
