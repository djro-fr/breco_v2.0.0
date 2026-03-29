// frontend/breco/src/__tests__/integration/auth/logout.integration.spec.ts

import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'

/**
 * Tested layer: the full chain runs for real except the HTTP call
 * (mocked at DataSource level)
 *
 * App.vue    >> user clicks
 * → store.logout()    >> state management
 * → LogoutUseCase.execute()   >> validates business rules
 * → AuthRepositoryImpl.logout()    >> translates for data layer
 * → AuthRemoteDataSource.logout() (mocked)  >> HTTP call intercepted here
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
      logout: ReturnType<typeof vi.fn>
    }


// ─ Test suite ─
describe('S1 - Déconnexion', () => {

  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
  })

  it('TC-74 - Suppression du token et réinitialisation du store', async () => {

    const store = useAuthStore()
    const ds = getDataSourceMock()

    ds.login.mockResolvedValue({
      success: true,
      token: 'ceci-est-un-faux-jeton-jwt',
      user: {
        id: 1,
        email: 'toto@titi.com',
        phone: '0607080910',
        firstName: 'Toto',
        lastName: 'TITI',
        driver: false,
      }
    })

    ds.logout.mockResolvedValue(null)

    // ARRANGE: authenticated user
    await expect(store.login('toto@titi.com', 'Toto1234')).resolves.toBeUndefined()

    // ACT
    await expect(store.logout()).resolves.toBeUndefined()

    // ASSERT
    expect(store.isAuthenticated).toBe(false)
    expect(store.user).toBeNull()
    expect(store.token).toBeNull()
    expect(store.error).toBeNull()
    expect(store.isLoading).toBe(false)
    expect(ds.login).toHaveBeenCalledOnce()
    expect(ds.logout).toHaveBeenCalledOnce()
  })


})
