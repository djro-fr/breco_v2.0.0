<!-- frontend\breco\src\presentation\features\auth\pages\LoginPage.vue -->
<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import FormInput from '@/presentation/shared/components/FormInput.vue'
import { emailSchema, passwordSchema } from '@/utils/validationSchemas'
import { ZodError } from 'zod'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const passwordFieldType = ref<'password' | 'text'>('password')

// Errors per field
const errors = ref<Record<string, string>>({})

const isLoading = computed(() => authStore.isLoading)
const globalError = computed(() => authStore.error)

// Real-time validation for each field
const validateField = (field: string, value: string) => {
  try {
    switch (field) {
      case 'email':
        emailSchema.parse(value)
        break
      case 'password':
        passwordSchema.parse(value)
        break
    }
    // If no error, remove field error
    delete errors.value[field]
  } catch (error) {
    if (error instanceof ZodError) {
      errors.value[field] = error.issues[0]?.message || 'Champ invalide'
    } else if (error instanceof Error) {
      errors.value[field] = error.message
    }
  }
}

// Validation when losing focus (blur)
const handleBlur = (field: string, value: string) => {
  if (value && value.length > 0) {
    validateField(field, value)
  }
}

const togglePasswordVisibility = () => {
  passwordFieldType.value = passwordFieldType.value === 'password' ? 'text' : 'password'
}


const handleLogin = async () => {
  // Errors reset
  errors.value = {}

  // Validation before submit
  let hasErrors = false

  if (!email.value || email.value.trim().length === 0) {
    errors.value.email = "L'email est requis"
    hasErrors = true
  } else {
    validateField('email', email.value)
    if (errors.value.email) hasErrors = true
  }

  if (!password.value || password.value.trim().length === 0) {
    errors.value.password = 'Le mot de passe est requis'
    hasErrors = true
  }

  if (hasErrors) {
    return
  }

  try {
    await authStore.login(email.value, password.value)
    router.push({ name: 'Search' })
  } catch (err) {
    console.error('Login error:', err)
  }
}
</script>

<template>
    <h1 class="text-white h-20">Covoiturez en Bretagne pour&nbsp;vos trajets quotidiens</h1>
    <div
      class="flex self-center flex-col mx-auto px-4 pt-4 pb-7.5 w-full max-w-md bg-white rounded-md shadow-window"
    >
      <h2 class="pt-2 pb-6">Connexion</h2>

      <form @submit.prevent="handleLogin">
        <div class="max-w-96 block mx-auto">
          <FormInput
            v-model="email"
            type="email"
            placeholder="E-mail"
            label="E-mail"
            aria-label="E-mail"
            required
            :hasError="Boolean(errors.email)"
            @blur="handleBlur('email', email)"
          />
          <p v-if="errors.email" class="error-text mt-0 mb-4">{{ errors.email }}</p>
        </div>

        <div class="mb-2 max-w-96 block mx-auto relative">
          <FormInput
            v-model="password"
            :type="passwordFieldType"
            placeholder="Mot de passe"
            label="Mot de passe"
            aria-label="Mot de passe"
            required
            :hasError="Boolean(errors.password)"
            @blur="handleBlur('password', password)"
            class="h-full"
            />
            <button
              v-if="password.valueOf().length > 0"
              type="button"
              class="absolute right-1 top-10 transform -translate-y-1/2 text-primary-dark focus:outline-none text-xl"
              @click="togglePasswordVisibility"
              aria-label="Afficher/masquer la confirmation du mot de passe"
            >
              <i v-if="passwordFieldType === 'password'" class="mdi mdi-eye text-xl"></i>
              <i v-else class="mdi mdi-eye-off text-xl"></i>
            </button>
          <p v-if="errors.password" class="error-text mt-0 mb-4">{{ errors.password }}</p>
        </div>

        <div class="text-center mb-4">
          <a href="#">Mot de passe oublié ?</a>
        </div>

        <!-- Global error (server) -->
        <p v-if="globalError" class="error-message mt-0 mb-4">{{ globalError }}</p>



        <div class="text-center mt-4 mb-0">
          <p><em>Les champs avec <span class="text-error"> *</span> sont obligatoires</em></p>
        </div>

        <div class="p-0 mx-auto my-6 text-center">
          <button type="submit" :disabled="isLoading" class="btn-action">
            {{ isLoading ? 'Connexion...' : 'Se connecter' }}
          </button>
        </div>
      </form>

      <p class="p-0 mx-auto my-0 text-center text-lg">Pas encore inscrit ?</p>
      <div class="p-0 mx-auto my-0 text-center">
        <button @click="$router.push('/register')" class="btn-secondary">S'inscrire</button>
      </div>
    </div>
</template>
