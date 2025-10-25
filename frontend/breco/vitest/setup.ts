import { createTestingPinia } from '@pinia/testing'
import { setActivePinia } from 'pinia'

// Mock localStorage
if (typeof window !== 'undefined') {
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
}

// Initialise Pinia pour les tests
setActivePinia(createTestingPinia())
