// vitest/setup.ts
import { config } from '@vue/test-utils'
import { createPinia } from 'pinia'
import { createRouter, createMemoryHistory } from 'vue-router'
import createFetchMock from 'vitest-fetch-mock'
import { vi } from 'vitest'

// Activates fetchMock
const fetchMocker = createFetchMock(vi)
fetchMocker.enableMocks()

// Creates a Pinia instance
const pinia = createPinia()

// Creates a test router
const router = createRouter({
  history: createMemoryHistory(),
  routes: [
    { path: '/', component: { template: '<div>You did it!</div>' } }
  ],
})

// Global config for Vue Test Utils
config.global.plugins = [pinia, router]
config.global.stubs = {
  'router-link': true,
  'router-view': true,
}

console.log('📝 vitest globalSetup - Pinia et Router configurés')
