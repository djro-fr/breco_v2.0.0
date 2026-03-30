// frontend/breco/src/__tests__/ui/auth/RegisterPage.spec.ts

import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import RegisterPage from '@/presentation/features/auth/pages/RegisterPage.vue'

// Mock vue-router
vi.mock('vue-router', () => ({
  useRouter: () => ({ push: vi.fn() }),
  useRoute: () => ({ params: {} }),
}))

// Mock authStore
vi.mock('@/presentation/features/auth/stores/authStore', () => ({
  useAuthStore: () => ({
    isLoading: false,
    error: null,
    register: vi.fn(),
  }),
}))

describe('RegisterPage UI', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('TC-64 : Champs véhicule fournis malgré driver=false', async () => {
    const wrapper = mount(RegisterPage, {
      global: { plugins: [] },
    })

    // Step 1
    await wrapper.find('[aria-label="E-mail"]').setValue('toto@titi.com')
    await wrapper.find('[aria-label="Mot de passe"]').setValue('Toto1234')
    await wrapper.find('[aria-label="Confirmez le mot de passe"]').setValue('Toto1234')
    await wrapper.find('[aria-label="Téléphone"]').setValue('0607080910')

    // Step 1 → 2
    await wrapper.find('button.btn-action').trigger('click')

    // Step 2
    await wrapper.findAll('button').find(b => b.text() === 'Homme')?.trigger('click')
    await wrapper.find('[aria-label="Prénom"]').setValue('Toto')
    await wrapper.find('[aria-label="Nom"]').setValue('TITI')

    // Step 2 → 3
    await wrapper.find('button.btn-action').trigger('click')

    expect(wrapper.find('[aria-label="Modèle"]').exists()).toBe(false)
    expect(wrapper.find('[aria-label="Couleur"]').exists()).toBe(false)
    expect(wrapper.find('#carSeatNb').exists()).toBe(false)
  })

  it('TC-65 : E-mail vide soumis via formulaire affiche une erreur Zod', async () => {
    const wrapper = mount(RegisterPage, {
      global: { plugins: [] },
    })

    // Step 1
    await wrapper.find('[aria-label="E-mail"]').setValue('')
    await wrapper.find('[aria-label="E-mail"]').trigger('blur')  // triggers validation
    await wrapper.find('[aria-label="Mot de passe"]').setValue('Toto1234')
    await wrapper.find('[aria-label="Confirmez le mot de passe"]').setValue('Toto1234')
    await wrapper.find('[aria-label="Téléphone"]').setValue('0607080910')

    // Step 1 → 2
    await wrapper.find('button.btn-action').trigger('click')

    expect(wrapper.find('.error-text').exists()).toBe(true)
    expect(wrapper.find('.error-text').text()).toContain("L'email est requis")
  })

  it('TC-66 : Bouton Suivant désactivé si step1 invalide', async () => {
    const wrapper = mount(RegisterPage, {
      global: { plugins: [] },
    })

    // Step 1
    await wrapper.find('[aria-label="E-mail"]').setValue('')
    await wrapper.find('[aria-label="E-mail"]').trigger('blur')  // triggers validation

    expect(wrapper.find('button.btn-action').attributes('disabled')).toBeDefined()
  })

  it('TC-67 : Progression vers étape 2 si step1 valide', async () => {
    const wrapper = mount(RegisterPage, {
      global: { plugins: [] },
    })

    // Step 1
    await wrapper.find('[aria-label="E-mail"]').setValue('toto@titi.com')
    await wrapper.find('[aria-label="Mot de passe"]').setValue('Toto1234')
    await wrapper.find('[aria-label="Confirmez le mot de passe"]').setValue('Toto1234')
    await wrapper.find('[aria-label="Téléphone"]').setValue('0607080910')

    // Step 1 → 2
    await wrapper.find('button.btn-action').trigger('click')

    expect(wrapper.find('[aria-label="Prénom"]').exists()).toBe(true)
  })

  it('TC-68 : Affichage erreur confirmation mot de passe', async () => {
    const wrapper = mount(RegisterPage, {
      global: { plugins: [] },
    })

    // Step 1
    await wrapper.find('[aria-label="E-mail"]').setValue('toto@titi.com')
    await wrapper.find('[aria-label="Mot de passe"]').setValue('Toto1234')
    await wrapper.find('[aria-label="Confirmez le mot de passe"]').setValue('Toto5678')
    await wrapper.find('[aria-label="Téléphone"]').setValue('0607080910')

    // Step 1 → 2
    await wrapper.find('button.btn-action').trigger('click')

    expect(wrapper.find('.error-text').exists()).toBe(true)
    expect(wrapper.find('.error-text').text()).toContain("Les mots de passe ne correspondent pas")
  })
})
