// frontend/breco/src/__tests__/integration/auth/auth.integration.spec.ts
import { describe, it, expect, beforeEach, vi } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '@/presentation/features/auth/stores/authStore'
import { User } from '@/domain/entities/User'

vi.mock('@/data/repositories/AuthRepositoryImpl', () => ({
  AuthRepositoryImpl: vi.fn().mockImplementation(() => ({
    login: vi.fn().mockResolvedValue({
      // Return a proper User instance
      user: new User(
        1,
        'test@test.com',
        '+33612345678',
        'John',
        'Doe',
        false
      ),
      token: 'jwt-token-123',
    }),
    // Dynamic mock: returns User instance based on parameters
    register: vi.fn().mockImplementation((data) => {
      return Promise.resolve({
        // Return a proper User instance with the data passed
        user: new User(
          2,
          data.email,
          data.phone,
          data.firstName,
          data.lastName,
          data.driver,
          undefined, // createdAt
          data.gender,
          data.zipCode,
          data.town,
          data.carModel,
          data.carColor,
          data.carSeatNb
        ),
        token: 'jwt-token-456',
      })
    }),
    logout: vi.fn().mockResolvedValue({}),
    verifyToken: vi.fn().mockResolvedValue(
      new User(
        1,
        'test@test.com',
        '+33612345678',
        'John',
        'Doe',
        false
      )
    ),
  })),
}))

describe('Auth Integration', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
    // Clear localStorage before each test
    localStorage.clear()
  })

  it('should complete login flow', async () => {
    const store = useAuthStore()

    // Initial state: not authenticated
    expect(store.isAuthenticated).toBe(false)

    // Login
    await store.login('test@test.com', 'Password123')

    // Verify authenticated state
    expect(store.isAuthenticated).toBe(true)
    expect(store.user?.email).toBe('test@test.com')
    expect(store.token).toBe('jwt-token-123')

    // Logout
    await store.logout()

    // Verify logged out state
    expect(store.isAuthenticated).toBe(false)
    expect(store.user).toBeNull()
    expect(store.token).toBeNull()
  })

  it('should complete register flow', async () => {
    const store = useAuthStore()

    // Register a driver with car information
    await store.register(
      'newuser@test.com',
      '0712345678',
      'SecurePass123!',
      'Jane',
      'Smith',
      true,           // driver
      undefined,      // gender
      undefined,      // zipCode
      undefined,      // town
      'Toyota Prius', // carModel (required for drivers)
      'Blanche',      // carColor (required for drivers)
      3               // carSeatNb (required for drivers)
    )

    // Verify registration success
    expect(store.isAuthenticated).toBe(true)
    expect(store.user?.email).toBe('newuser@test.com')
    expect(store.user?.driver).toBe(true)
    expect(store.token).toBe('jwt-token-456')
  })

  it('should verify token on app startup', async () => {
    const store = useAuthStore()

    // Login first
    await store.login('test@test.com', 'Password123')

    // Check authentication (simulates app startup)
    await store.checkAuth()

    // Verify user is still authenticated
    expect(store.isAuthenticated).toBe(true)
    expect(store.user?.email).toBe('test@test.com')
  })

  it('should handle multiple sequential operations', async () => {
    const store = useAuthStore()

    // Login
    await store.login('test@test.com', 'Password123')
    expect(store.isAuthenticated).toBe(true)

    // Clear error
    store.error = null
    expect(store.error).toBeNull()
    expect(store.isAuthenticated).toBe(true)

    // Logout
    await store.logout()
    expect(store.isAuthenticated).toBe(false)
  })

  it('should clear error on new login', async () => {
    const store = useAuthStore()

    // Set a previous error
    store.error = 'Previous error'

    // Login should clear the error
    await store.login('test@test.com', 'Password123')

    // Verify error is cleared and user is authenticated
    expect(store.error).toBeNull()
    expect(store.isAuthenticated).toBe(true)
  })

  it('should complete register flow for non-driver', async () => {
    const store = useAuthStore()

    // Register a non-driver (no car information needed)
    await store.register(
      'passenger@test.com',
      '0612345678',
      'SecurePass123!',
      'Marc',
      'Dupont',
      false  // driver: false (no car info required)
    )

    // Verify registration success for non-driver
    expect(store.isAuthenticated).toBe(true)
    expect(store.user?.email).toBe('passenger@test.com')
    expect(store.user?.driver).toBe(false)
  })
})
