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
  <div class="flex self-center flex-col mx-auto px-4 pt-4 pb-7.5 w-full max-w-md bg-white rounded-md shadow-window ">
    <h1 class="pt-2 pb-6">Connexion</h1>
    <form @submit.prevent="handleLogin">
      <input v-model="email" class="mb-7 max-w-96 block mx-auto" type="email" placeholder="E-mail" aria-label="Email" required />
      <input
        v-model="password"
        class="mb-2 max-w-96 block mx-auto"
        type="password"
        placeholder="Mot de passe"
        aria-label="Mot de passe"
        required
      />

      <div class="text-center">
        <a href="#">Mot de passe oublié ?</a>
      </div>
      <div class="p-0 mx-auto my-6 text-center">
        <button type="submit" :disabled="isLoading" class="btn-action">
          {{ isLoading ? 'Connexion...' : 'Se connecter' }}
        </button>
      </div>
    </form>
    <p v-if="error" style="color: red">{{ error }}</p>
    <p class="p-0 mx-auto my-0 text-center text-lg">Pas encore inscrit ?</p>
    <div class="p-0 mx-auto my-0 text-center">
      <button @click="$router.push('/register')" class="btn-secondary">S'inscrire</button>
    </div>
  </div>
</template>


