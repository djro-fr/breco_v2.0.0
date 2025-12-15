// frontend/breco/src/__tests__/ui/auth/auth.ui.spec.ts
import { describe, it, expect, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import LoginPage from '@/presentation/features/auth/pages/LoginPage.vue'
import RegisterPage from '@/presentation/features/auth/pages/RegisterPage.vue'

describe('LoginPage Component', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('should render login form', () => {
    const wrapper = mount(LoginPage, {
      global: {
        stubs: {
          RouterLink: true,
          RouterView: true
        }
      }
    })
    expect(wrapper.find('form').exists()).toBe(true)
  })

  it('should have email input', () => {
    const wrapper = mount(LoginPage, {
      global: {
        stubs: {
          RouterLink: true,
          RouterView: true
        }
      }
    })
    expect(wrapper.find('input[type="email"]').exists()).toBe(true)
  })

  it('should have password input', () => {
    const wrapper = mount(LoginPage, {
      global: {
        stubs: {
          RouterLink: true,
          RouterView: true
        }
      }
    })
    expect(wrapper.find('input[type="password"]').exists()).toBe(true)
  })

  it('should have submit button', () => {
    const wrapper = mount(LoginPage, {
      global: {
        stubs: {
          RouterLink: true,
          RouterView: true
        }
      }
    })
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true)
  })
})

describe('RegisterPage Component', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('should render register page', () => {
    const wrapper = mount(RegisterPage, {
      global: {
        stubs: {
          RouterLink: true,
          RouterView: true
        }
      }
    })
    expect(wrapper.exists()).toBe(true)
  })

  it('should display step 1 by default', () => {
    const wrapper = mount(RegisterPage, {
      global: {
        stubs: {
          RouterLink: true,
          RouterView: true
        }
      }
    })
    expect(wrapper.text()).toContain('Pour vous contacter')
  })

  it('should have email input in step 1', () => {
    const wrapper = mount(RegisterPage, {
      global: {
        stubs: {
          RouterLink: true,
          RouterView: true
        }
      }
    })
    expect(wrapper.find('input[type="email"]').exists()).toBe(true)
  })

  it('should have password inputs in step 1', () => {
    const wrapper = mount(RegisterPage, {
      global: {
        stubs: {
          RouterLink: true,
          RouterView: true
        }
      }
    })
    const passwordInputs = wrapper.findAll('input[type="password"]')
    expect(passwordInputs.length).toBe(2)
  })

  it('should have phone input in step 1', () => {
    const wrapper = mount(RegisterPage, {
      global: {
        stubs: {
          RouterLink: true,
          RouterView: true
        }
      }
    })
    expect(wrapper.find('input[type="tel"]').exists()).toBe(true)
  })

  it('should display progress indicator', () => {
    const wrapper = mount(RegisterPage, {
      global: {
        stubs: {
          RouterLink: true,
          RouterView: true
        }
      }
    })
    const stepTexts = wrapper.text()
    expect(stepTexts).toContain('Contact')
    expect(stepTexts).toContain('Identité')
    expect(stepTexts).toContain('Véhicule')
    expect(stepTexts).toContain('Confirmation')
  })

  it('should have next button', () => {
    const wrapper = mount(RegisterPage, {
      global: {
        stubs: {
          RouterLink: true,
          RouterView: true
        }
      }
    })
    expect(wrapper.find('.btn-action').exists()).toBe(true)
  })
})
