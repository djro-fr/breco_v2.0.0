// frontend/breco/src/__tests__/integration/auth/login.integration.spec.ts

import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'

/**
 * Tested layer: the full chain runs for real except the HTTP call
 * (mocked at DataSource level)
 *
 * LoginPage.vue    >> user clicks
 * → store.login()    >> state management
 * → LoginUseCase.execute()   >> validates business rules
 * → AuthRepositoryImpl.login()    >> translates for data layer
 * → AuthRemoteDataSource.login() (mocked)  >> HTTP call intercepted here
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

// ─ Helper ─
// Retrieves the DataSource mock last instance created by AuthRepositoryImpl
const getDataSourceMock = () =>
  vi.mocked(AuthRemoteDataSource)
    .mock.results
    .at(-1)!
    .value as {
      login: ReturnType<typeof vi.fn>
    }


// ─ Test suite ─
describe('S1 - Connexion', () => {

  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
  })

  it('TC-53 - E-mail avec format xxx@yyy.zzz mais pas en base', async () => {

    const store = useAuthStore()
    const ds = getDataSourceMock()

    ds.login.mockRejectedValue(new Error('E-mail inconnu, inscrivez-vous'))

    await expect(store.login('toto@tata.com', 'DevPass123!')).rejects.toThrow('E-mail inconnu, inscrivez-vous')
    expect(store.isAuthenticated).toBe(false)
    expect(store.error).toBe('E-mail inconnu, inscrivez-vous')
    expect(ds.login).toHaveBeenCalledOnce()
  })


  it('TC-54 - E-mail avec format xxx@yyy.zzz, bien en base', async () => {

    const store = useAuthStore()
    const ds = getDataSourceMock()

    ds.login.mockResolvedValue({
      success: true,
      token: 'ceci-est-un-faux-jeton-jwt',
      user: {
        id: 1,
        email: 'toto@titi.com',
        phone: '0607080910',
        firstName: 'Dev',
        lastName: 'Test',
        driver: false,
      }
    })

    await expect(store.login('toto@titi.com', 'DevPass123!')).resolves.toBeUndefined()
    expect(store.isAuthenticated).toBe(true)
    expect(store.error).toBeNull()
    expect(store.isLoading).toBe(false)
    expect(ds.login).toHaveBeenCalledOnce()
  })
})
