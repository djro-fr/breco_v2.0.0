import { createTestingPinia } from '@pinia/testing'
import { setActivePinia } from 'pinia'

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
})

// Initialise Pinia pour les tests
setActivePinia(createTestingPinia())
