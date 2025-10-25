
import createFetchMock from 'vitest-fetch-mock'
import { beforeAll, vi } from 'vitest'

beforeAll(() => {
  console.log('📝 vitest beforeAll')
  const fetchMocker = createFetchMock(vi)

  // sets globalThis.fetch and globalThis.fetchMock to our mocked version
  fetchMocker.enableMocks()
})
