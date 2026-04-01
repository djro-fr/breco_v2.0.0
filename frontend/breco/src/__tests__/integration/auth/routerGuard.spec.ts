// frontend/breco/src/__tests__/integration/auth/routerGuard.spec.ts

import { describe, it, expect, beforeEach, vi } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import DashboardPage from '@/presentation/app/pages/DashboardPage.vue'
import LoginPage from '@/presentation/features/auth/pages/LoginPage.vue'

const mockCheckAuth = vi.fn()
const mockIsAuthenticated = { value: false }
const mockToken = { value: null as string | null }

vi.mock('@/presentation/features/auth/stores/authStore', () => ({
  useAuthStore: () => ({
    get isAuthenticated() { return mockIsAuthenticated.value },
    get token() { return mockToken.value },
    error: null,
    checkAuth: mockCheckAuth,
  }),
}))

describe('S1: Login - Router guard', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    localStorage.clear()
    vi.clearAllMocks()
  })

  const createTestRouter = () => {
    const router = createRouter({
      history: createWebHistory(),
      routes: [
        { path: '/dashboard', name: 'Dashboard', component: DashboardPage, meta: { requiresAuth: true } },
        { path: '/login', name: 'Login', component: LoginPage },
      ],
    })
    router.beforeEach(async (to, _from, next) => {
      const authStore = {
        isAuthenticated: mockIsAuthenticated.value,
        token: mockToken.value,
        error: null,
        checkAuth: mockCheckAuth,
      }
      if (!authStore.token && localStorage.getItem('token')) {
        await authStore.checkAuth()
      }
      if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'Login', query: { redirect: to.fullPath } })
      } else {
        next()
      }
    })
    return router
  }

  it('TC-79: Expiration du token JWT en session active', async () => {
    // ARRANGE: token expired, checkAuth clears the session
    localStorage.setItem('token', 'expired-token')  // token exists in localStorage
    mockToken.value = null                          // but not yet in store
    mockIsAuthenticated.value = false               // session not restored
    mockCheckAuth.mockResolvedValue(undefined)      // checkAuth does nothing

    const router = createTestRouter()   // Create an isolated test router with the navigation guard
    await router.push('/dashboard')     // Trigger navigation to the protected route
    await router.isReady()              // Wait for all async operations to complete (guard, checkAuth, redirects)

    expect(router.currentRoute.value.name).toBe('Login')
  })

  it('TC-80: Rafraîchissement de page en session authentifiée', async () => {
    // ARRANGE: token valid, checkAuth restores the session
    localStorage.setItem('token', 'valid-token')    // token exists in localStorage
    mockToken.value = null                          // but not yet in store, the page has just reloaded
    mockIsAuthenticated.value = true                // but checkAuth has restored the session
    mockCheckAuth.mockResolvedValue(undefined)      // checkAuth executes with no error


    const router = createTestRouter()
    await router.push('/dashboard')
    await router.isReady()

    expect(router.currentRoute.value.name).toBe('Dashboard')
  })
})
