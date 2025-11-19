<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const isLoading = computed(() => authStore.isLoading)
const error = computed(() => authStore.error)

const handleLogin = async () => {
  try {
    await authStore.login(email.value, password.value)
    router.push({ name: 'Search' })
  } catch (err) {
    console.error('Login error:', err)
  }
}
</script>

<template>
  <div class="bg-white rounded-md shadow-window px-4 pt-4 pb-7.5">
    <h1 class="p-0 mx-auto my-0 pb-4 text-center text-3xl font-medium leading-none">Connexion</h1>
    <form @submit.prevent="handleLogin">
      <input v-model="email" class="mb-5 px-2 py-1 w-full text-lg border-b border-primary-light bg-white-dark" type="email" placeholder="Email" aria-label="Email" required />
      <input
        v-model="password"
        class="mb-5 px-2 py-1 w-full text-lg border-b border-primary-light bg-white-dark"
        type="password"
        placeholder="Mot de passe"
        aria-label="Mot de passe"
        required
      />

      <div class="text-center forget">
        <a href="#">Mot de passe oublié ?</a>
      </div>
      <div class="p-0 mx-auto my-0 text-center">
        <button type="submit" :disabled="isLoading" class="my-6 py-2 w-full text-xl font-medium rounded-md bg-action hover:bg-action-on shadow-md shadow-black/20 hover:shadow-lg">
          {{ isLoading ? 'Connexion...' : 'Se connecter' }}
        </button>
      </div>
    </form>
    <p v-if="error" style="color: red">{{ error }}</p>
    <p class="p-0 mx-auto my-0 text-center">Pas encore inscrit ?</p>
    <div class="p-0 mx-auto my-0 text-center">
      <button @click="$router.push('/register')" class="mt-1 px-9 py-1 bg-primary-dark hover:bg-primary-dark-on  text-md text-white rounded-md shadow-md shadow-black/20 hover:shadow-lg">S'inscrire</button>
    </div>
  </div>
</template>


