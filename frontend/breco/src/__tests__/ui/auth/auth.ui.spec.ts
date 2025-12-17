// frontend/breco/src/__tests__/ui/auth/auth.ui.spec.ts
import { describe, it, expect, beforeEach } from 'vitest'
import { mount, VueWrapper } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import LoginPage from '@/presentation/features/auth/pages/LoginPage.vue'
import RegisterPage from '@/presentation/features/auth/pages/RegisterPage.vue'

// Common configuration for mounting components
const globalConfig = {
  global: {
    stubs: {
      RouterLink: true,
      RouterView: true
    }
  }
}
// Factory function to create a wrapper for the component
function createWrapper(component: any): VueWrapper {
  return mount(component, globalConfig)
}

describe('LoginPage Component', () => {
  let wrapper: VueWrapper

  beforeEach(() => {
    setActivePinia(createPinia())
    wrapper = createWrapper(LoginPage)
  })

  it('should render login form', () => {
    expect(wrapper.find('form').exists()).toBe(true)
  })

  it('should have email input', () => {
    expect(wrapper.find('input[type="email"]').exists()).toBe(true)
  })

  it('should have password input', () => {
    expect(wrapper.find('input[type="password"]').exists()).toBe(true)
  })

  it('should have submit button', () => {
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true)
  })
})

describe('RegisterPage Component', () => {
  let wrapper: VueWrapper

  beforeEach(() => {
    setActivePinia(createPinia())
    wrapper = createWrapper(RegisterPage)
  })

  it('should render register page', () => {
    expect(wrapper.exists()).toBe(true)
  })

  it('should display step 1 by default', () => {
    expect(wrapper.text()).toContain('Pour vous contacter')
    expect(wrapper.text()).toContain('Étape 1/4')
  })

  it('should have email input in step 1', () => {
    expect(wrapper.find('input[type="email"]').exists()).toBe(true)
  })

  it('should have password inputs in step 1', () => {
    const passwordInputs = wrapper.findAll('input[type="password"]')
    expect(passwordInputs.length).toBe(2)
  })

  it('should have phone input in step 1', () => {
    expect(wrapper.find('input[type="tel"]').exists()).toBe(true)
  })

  it('should display progress indicator with 4 steps', () => {
    const stepIndicators = wrapper.findAll('.w-8.h-8.rounded-full')
    expect(stepIndicators.length).toBe(4)
    expect(wrapper.text()).toContain('Étape 1/4')
    expect(wrapper.text()).toContain('Contact')
  })

  it('should highlight current step (step 1) in progress bar', () => {
    const stepIndicators = wrapper.findAll('.w-8.h-8.rounded-full')

    expect(stepIndicators[0].classes()).toContain('bg-primary-light')
    expect(stepIndicators[1].classes()).toContain('bg-white')
    expect(stepIndicators[2].classes()).toContain('bg-white')
    expect(stepIndicators[3].classes()).toContain('bg-white')
  })

  it('should have next button', () => {
    const nextButton = wrapper.find('button.btn-action')
    expect(nextButton.exists()).toBe(true)
    expect(nextButton.text()).toContain('Suivant')
  })

  it('should disable next button when step 1 is invalid', () => {
    const nextButton = wrapper.find('button.btn-action')
    expect(nextButton.attributes('disabled')).toBeDefined()
  })
})
