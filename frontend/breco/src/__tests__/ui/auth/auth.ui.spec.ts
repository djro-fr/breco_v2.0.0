import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import LoginPage from '@/presentation/features/auth/pages/LoginPage.vue'
import RegisterPage from '@/presentation/features/auth/pages/RegisterPage.vue'

describe('LoginPage Component', () => {
  it('should render login form', () => {
    const wrapper = mount(LoginPage)
    expect(wrapper.find('form').exists()).toBe(true)
  })

  it('should have email input', () => {
    const wrapper = mount(LoginPage)
    expect(wrapper.find('input[type="email"]').exists()).toBe(true)
  })

  it('should have password input', () => {
    const wrapper = mount(LoginPage)
    expect(wrapper.find('input[type="password"]').exists()).toBe(true)
  })

  it('should have submit button', () => {
    const wrapper = mount(LoginPage)
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true)
  })
})

describe('RegisterPage Component', () => {
  it('should render register page', () => {
    const wrapper = mount(RegisterPage)
    expect(wrapper.find('.register-container').exists()).toBe(true)
  })

  it('should display step 1 by default', () => {
    const wrapper = mount(RegisterPage)
    expect(wrapper.text()).toContain('Pour vous contacter')
  })

  it('should have email input in step 1', () => {
    const wrapper = mount(RegisterPage)
    expect(wrapper.find('input[type="email"]').exists()).toBe(true)
  })

  it('should have password inputs in step 1', () => {
    const wrapper = mount(RegisterPage)
    const passwordInputs = wrapper.findAll('input[type="password"]')
    expect(passwordInputs.length).toBe(2)
  })

  it('should have phone input in step 1', () => {
    const wrapper = mount(RegisterPage)
    expect(wrapper.find('input[type="tel"]').exists()).toBe(true)
  })

  it('should display progress indicator', () => {
    const wrapper = mount(RegisterPage)
    expect(wrapper.find('.progress-indicator').exists()).toBe(true)
  })

  it('should have next button', () => {
    const wrapper = mount(RegisterPage)
    expect(wrapper.find('.btn-action').exists()).toBe(true)
  })
})
