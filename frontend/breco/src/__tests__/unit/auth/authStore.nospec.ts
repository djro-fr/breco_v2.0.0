import { describe, it, expect, beforeEach, vi } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '@/presentation/features/auth/stores/authStore'

vi.mock('@/data/repositories/AuthRepositoryImpl', () => ({
  AuthRepositoryImpl: vi.fn().mockImplementation(() => ({
    login: vi.fn(),
    register: vi.fn(),
    logout: vi.fn().mockResolvedValue({}),
    verifyToken: vi.fn(),
  })),
}))

const createMockUser = (overrides = {}) => ({
  id: 1,
  email: 'test@test.com',
  firstName: 'John',
  lastName: 'Doe',
  phone: '+33612345678',
  driver: false,
  getFullName: () => 'John Doe',
  isValid: () => true,
  isDriver: () => false,
  hasCompleteProfile: () => true,
  ...overrides
})

describe('Auth Store (Unit)', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    localStorage.clear()
    vi.clearAllMocks()
  })

  it('should have initial state', () => {
    const store = useAuthStore()
    expect(store.user).toBeNull()
    expect(store.token).toBeNull()
    expect(store.isAuthenticated).toBe(false)
    expect(store.isLoading).toBe(false)
    expect(store.error).toBeNull()
  })

  it('should logout and clear auth', async () => {
    const store = useAuthStore()
    store.token = 'token-123'
    store.user = createMockUser()

    await store.logout()

    expect(store.user).toBeNull()
    expect(store.token).toBeNull()
    expect(store.isAuthenticated).toBe(false)
  })

  it('isAuthenticated should be true when user and token exist', () => {
    const store = useAuthStore()
    store.token = 'token'
    store.user = createMockUser()

    expect(store.isAuthenticated).toBe(true)
  })

  it('isAuthenticated should be false when no token', () => {
    const store = useAuthStore()
    store.user = createMockUser()

    expect(store.isAuthenticated).toBe(false)
  })

  it('should clear auth data', () => {
    const store = useAuthStore()
    store.token = 'token-123'
    store.user = createMockUser()
    store.error = 'Some error'

    store.clearAuth()

    expect(store.user).toBeNull()
    expect(store.token).toBeNull()
    expect(store.error).toBeNull()
  })
})
