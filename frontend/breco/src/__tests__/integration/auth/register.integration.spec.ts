// frontend/breco/src/__tests__/integration/auth/register.integration.spec.ts

import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'

/**
 * Tested layer: the full chain runs for real except the HTTP call
 * (mocked at DataSource level)
 *
 * RegisterPage.vue    >> user clicks
 * → store.register()    >> state management
 * → RegisterUseCase.execute()   >> validates business rules
 * → AuthRepositoryImpl.register()    >> translates for data layer
 * → AuthRemoteDataSource.register() (mocked)  >> HTTP call intercepted here
 *   ✗ axios → backend  >> never reached
 *
 */


// ─ Mock AuthRemoteDataSource ─
// Stop the chain just before the real HTTP call.
// UseCase, Repository and Store execute for real.
vi.mock('@/data/datasources/remote/AuthRemoteDataSource', () => ({
  AuthRemoteDataSource: vi.fn().mockImplementation(() => ({
    register:    vi.fn(),
    login:       vi.fn(),
    logout:      vi.fn(),
    verifyToken: vi.fn(),
  })),
}))

// Vitest automatically hoists vi.mock() before all imports at runtime.
// AuthRemoteDataSource imported here is already the mocked version.
import { useAuthStore }         from '@/presentation/features/auth/stores/authStore'
import { AuthRemoteDataSource } from '@/data/datasources/remote/AuthRemoteDataSource'
import type { RegisterInput }   from '@/domain/repositories/IAuthRepository'

// ─ Test data ─
const VALID_INPUT: RegisterInput = {
  email:     'dev@test.com',
  phone:     '0607080910',
  password:  'DevPass123!', // NOSONAR
  firstName: 'Dev',
  lastName:  'Test',
  driver:    false,
}

// ─ Helper ─
// Retrieves the DataSource mock last instance created by AuthRepositoryImpl
const getDataSourceMock = () =>
  vi.mocked(AuthRemoteDataSource)
    .mock.results   // log of all new AuthRemoteDataSource()
    .at(-1)!        // last created instance (! = never undefined)
    .value as {
      register: ReturnType<typeof vi.fn>   // cast to access .mockResolvedValue()
    }

// ─ Test suite ─
describe('S1 - Inscription', () => {

  beforeEach(() => {
    // Fresh Pinia before each test (store.user = null...)
    setActivePinia(createPinia())
    // Reset all vi.fn()
    vi.clearAllMocks()
  })

  it('TC-52b - Inscription avec données valides', async () => {

    // Instantiates the store → AuthRepositoryImpl → AuthRemoteDataSource (mocked)
    const store = useAuthStore()

    // Must be called after useAuthStore() so mock.results is not empty
    const ds = getDataSourceMock()

    // Programs the mock: when register() is called, return this object
    ds.register.mockResolvedValue({
      success: true,
      message: 'Inscription réussie ! Un e-mail de vérification a été envoyé à votre adresse.',
      requiresVerification: true,
    })

    // Triggers the real chain: store → UseCase → Repository → DataSource (mocked)
    const result = await store.register(VALID_INPUT)

    expect(result).toEqual({
      requiresVerification: true,
      message: 'Inscription réussie ! Un e-mail de vérification a été envoyé à votre adresse.',
    })

    expect(store.isAuthenticated).toBe(false)
    expect(store.error).toBeNull()
    expect(store.isLoading).toBe(false)

    expect(ds.register).toHaveBeenCalledOnce()
    expect(ds.register).toHaveBeenCalledWith(VALID_INPUT)
  })


  it('TC-52c - Inscription avec e-mail déjà en base', async () => {

    const store = useAuthStore()
    const ds = getDataSourceMock()

    // Programs the mock: when register() is called, throw this error (simulates a 422 from the backend)
    ds.register.mockRejectedValue(new Error('Cette adresse e-mail est déjà utilisée'))

    await expect(store.register(VALID_INPUT)).rejects.toThrow('Cette adresse e-mail est déjà utilisée')

    expect(store.isAuthenticated).toBe(false)

    expect(store.error).toBe('Cette adresse e-mail est déjà utilisée')
    expect(store.isLoading).toBe(false)
  })
})
