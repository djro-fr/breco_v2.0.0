import { createTestingPinia } from '@pinia/testing'
import { setActivePinia } from 'pinia'

export function setup() {
  console.log('📝 vitest setup')
  setActivePinia(createTestingPinia())
}

export function teardown() {
  console.log('📝 vitest teardown')
}
