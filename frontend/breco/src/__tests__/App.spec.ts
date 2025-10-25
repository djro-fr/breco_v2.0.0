import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import App from '../presentation/app/App.vue'

// Mock vue-router to avoid issues during testing
vi.mock('vue-router', () => ({
  createRouter: () => ({}),
  createMemoryHistory: () => ({}),
  useRouter: () => ({}),
  useRoute: () => ({}),
}))

describe('App', () => {
  it('mounts and show properly the footer', () => {
    const wrapper = mount(App)
    expect(wrapper.text()).toContain('© 2025 Breco')
  })
})
