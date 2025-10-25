import { config } from '@vue/test-utils'
import { createPinia } from 'pinia'
import createFetchMock from 'vitest-fetch-mock'
import { vi } from 'vitest'
import { createRouter, createMemoryHistory } from 'vue-router'

// Mock sessionStorage
const sessionStorageMock = (() => {
  let store: Record<string, string> = {}
  return {
    getItem: (key: string) => store[key] || null,
    setItem: (key: string, value: string) => {
      store[key] = String(value)
    },
    removeItem: (key: string) => {
      delete store[key]
    },
    clear: () => {
      store = {}
    },
  }
})
// @ts-ignore
globalThis.sessionStorage = sessionStorageMock

// Mock localStorage for jsdom
const localStorageMock = (() => {
  let store: Record<string, string> = {}
  return {
    getItem: (key: string) => store[key] || null,
    setItem: (key: string, value: string) => {
      store[key] = value.toString()
    },
    removeItem: (key: string) => {
      delete store[key]
    },
    clear: () => {
      store = {}
    },
  }
})()
// @ts-ignore
globalThis.localStorage = localStorageMock

// Activate fetchMock
const fetchMocker = createFetchMock(vi)
fetchMocker.enableMocks()

// Mock API calls
const apiUrl = process.env.VITE_API_URL || 'http://localhost:8081/api'
fetchMocker.mockResponse(
  (req) => {
    if (req.url.includes(apiUrl)) {
      return {
        status: 200,
        body: JSON.stringify({ success: true, data: 'mocked response' }),
        headers: { 'Content-Type': 'application/json' },
      }
    }
    return { status: 404, body: 'Not Found' }
  }
)

// Create a Pinia instance
const pinia = createPinia()

// Create a minimal router to avoid injection errors with an empty route
const router = createRouter({
  history: createMemoryHistory(),
  routes: [{ path: '/', component: { template: '<div></div>' } } ],
})

// Disable navigation warnings
router.push = vi.fn() // Mocks push method
router.replace = vi.fn() // Mocks replace method

// Global config for Vue Test Utils
config.global.plugins = [pinia, router]
config.global.stubs = {
  'router-link': true,
  'router-view': true,
}

console.log('📝 vitest globalSetup - Pinia et router silencieux')
