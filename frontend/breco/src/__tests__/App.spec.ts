import { describe, it, expect } from 'vitest'

import { mount } from '@vue/test-utils'
import App from '../presentation/app/App.vue'

describe('App', () => {
  it('mounts renders properly', () => {
    const wrapper = mount(App)
    expect(wrapper.text()).toContain('© 2025 Breco')
  })
})
