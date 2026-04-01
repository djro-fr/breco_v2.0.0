// frontend/breco/src/__tests__/integration/auth/login.integration.spec.ts

import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'

import { AppException, UnauthorizedException } from '@/domain/exceptions/AppException'

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
describe('S1: Login', () => {

  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
  })

  it('TC-53: email with format xxx@yyy.zzz but not in database', async () => {

    const store = useAuthStore()
    const ds = getDataSourceMock()

    ds.login.mockRejectedValue(new UnauthorizedException('E-mail inconnu, inscrivez-vous'))

    await expect(store.login('toto@tata.com', 'Chabada123')).rejects.toThrow('E-mail inconnu, inscrivez-vous')
    expect(store.isAuthenticated).toBe(false)
    expect(store.error).toBe('E-mail inconnu, inscrivez-vous')
    expect(ds.login).toHaveBeenCalledOnce()
  })


  it('TC-54: email with format xxx@yyy.zzz, present in database', async () => {

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

    await expect(store.login('toto@titi.com', 'Toto1234')).resolves.toBeUndefined()
    expect(store.isAuthenticated).toBe(true)
    expect(store.error).toBeNull()
    expect(store.isLoading).toBe(false)
    expect(ds.login).toHaveBeenCalledOnce()
  })

  it('TC-55: incorrect password', async () => {

    const store = useAuthStore()
    const ds = getDataSourceMock()

    ds.login.mockRejectedValue(new UnauthorizedException('E-mail ou mot de passe incorrect'))

    await expect(store.login('toto@tata.com', 'Chabada123')).rejects.toThrow('E-mail ou mot de passe incorrect')
    expect(store.isAuthenticated).toBe(false)
    expect(store.error).toBe('E-mail ou mot de passe incorrect')
    expect(ds.login).toHaveBeenCalledOnce()
  })

  it('TC-60: login with unverified account', async () => {

    const store = useAuthStore()
    const ds = getDataSourceMock()

    ds.login.mockRejectedValue(new UnauthorizedException('Veuillez vérifier votre adresse e-mail avant de vous connecter'))

    await expect(store.login('toto@titi.com', 'Toto1234')).rejects.toThrow('Veuillez vérifier votre adresse e-mail avant de vous connecter')
    expect(store.isAuthenticated).toBe(false)
    expect(store.error).toBe('Veuillez vérifier votre adresse e-mail avant de vous connecter')
    expect(ds.login).toHaveBeenCalledOnce()
  })

  it('TC-77: backend unavailable during login', async () => {

    const store = useAuthStore()
    const ds = getDataSourceMock()

    ds.login.mockRejectedValue(new AppException('API_ERROR', 'Une erreur est survenue', 503))

    await expect(store.login('toto@tata.com', 'Chabada123')).rejects.toThrow('Une erreur est survenue')
    expect(store.isAuthenticated).toBe(false)
    expect(store.error).toBe('Une erreur est survenue')
    expect(ds.login).toHaveBeenCalledOnce()
  })


})
