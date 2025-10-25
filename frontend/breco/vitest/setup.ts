import { config } from '@vue/test-utils'
import { createPinia } from 'pinia'
import createFetchMock from 'vitest-fetch-mock'
import { vi } from 'vitest'
import { createRouter, createMemoryHistory } from 'vue-router'

// Activates fetchMock
const fetchMocker = createFetchMock(vi)
fetchMocker.enableMocks()

// Creates a Pinia instance
const pinia = createPinia()

// Creates a minimal router to avoid injection errors with an empty route
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
