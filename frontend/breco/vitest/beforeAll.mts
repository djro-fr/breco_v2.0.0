import createFetchMock from 'vitest-fetch-mock'
import { beforeAll, vi } from 'vitest'

beforeAll(() => {
  console.log('📝 vitest beforeAll')

  const fetchMocker = createFetchMock(vi)
  fetchMocker.enableMocks()

  // Mock localStorage
  Object.defineProperty(window, 'localStorage', {
    value: {
      getItem: () => null,
      setItem: () => {},
      removeItem: () => {},
      clear: () => {},
      key: () => null,
      length: 0,
    },
    writable: true,
  })
})
