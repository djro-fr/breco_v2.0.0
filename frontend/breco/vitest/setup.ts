import createFetchMock from 'vitest-fetch-mock'
import { vi } from 'vitest'

/**
* Vitest setup function
*/
export function setup() {
  console.log('📝 vitest globalSetup')
  const fetchMocker = createFetchMock(vi)

  // sets globalThis.fetch and globalThis.fetchMock to our mocked version
  fetchMocker.enableMocks()
}

/**
* Vitest Teardown function
*/
export function teardown() {
  console.log('📝 vitest globalTeardown')
}
