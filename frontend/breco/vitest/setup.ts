import { config } from '@vue/test-utils'
import { createPinia } from 'pinia'
import createFetchMock from 'vitest-fetch-mock'
import { vi } from 'vitest'
import { createRouter, createMemoryHistory } from 'vue-router'

// Activate fetchMock
const fetchMocker = createFetchMock(vi)
fetchMocker.enableMocks()

// Mock API calls
const apiUrl = process.env.VITE_API_URL || 'http://localhost:8081/api'
fetchMocker.mockResponse((req) => {
  if (req.url.includes(apiUrl)) {
    return {
      status: 200,
      body: JSON.stringify({ success: true, data: 'mocked response' }),
      headers: { 'Content-Type': 'application/json' },
    }
  }
  return { status: 404, body: 'Not Found' }
})

// Create Pinia store
const pinia = createPinia()

// Create minimal router
const router = createRouter({
  history: createMemoryHistory(),
  routes: [{ path: '/', component: { template: '<div></div>' } }],
})
router.push = vi.fn()
router.replace = vi.fn()

// Configure Vue Test Utils
config.global.plugins = [pinia, router]
config.global.stubs = {
  'router-link': true,
  'router-view': true,
}

console.log('✅ Setup Vitest terminé')
