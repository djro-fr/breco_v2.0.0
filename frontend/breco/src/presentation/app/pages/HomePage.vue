<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/presentation/features/auth/stores/authStore'
import DashboardPage from './DashboardPage.vue'

const router = useRouter()
const authStore = useAuthStore()
const isAuthenticated = computed(() => authStore.isAuthenticated)

const email = ref('')
const password = ref('')
const isLoading = computed(() => authStore.isLoading)
const error = computed(() => authStore.error)

const handleLogin = async () => {
  try {
    await authStore.login(email.value, password.value)
    router.push({ name: 'Dashboard' })
  } catch (err) {
    console.error('Login error:', err)
  }
}
</script>

<template>
  <div>
    <h1 class="m-0 p-0 text-3xl text-white leading-9 text-center font-extrabold">Breco</h1>
    <h2 class="m-0 p-0 text-lg text-white leading-5 text-center font-normal">Application de covoiturage en&nbsp;Bretagne</h2>

    <div v-if="!isAuthenticated" class="bg-white rounded-md shadow-window mt-10 px-4 pt-4 pb-7.5 ">
      <h3 class="m-0 p-0 pb-4 text-2xl font-medium text-center">Connexion</h3>
      <form @submit.prevent="handleLogin">
        <input v-model="email"  class="mb-5 px-2 py-1 w-full text-lg border-b border-primary-light bg-white-dark" type="email" placeholder="Email" aria-label="Email" required />
        <input
          v-model="password"
          class="mb-5 px-2 py-1 w-full text-lg border-b border-primary-light bg-white-dark"
          type="password"
          placeholder="Mot de passe"
          aria-label="Mot de passe"
          required
        />
        <div class="-mt-2 mb-1 text-center">
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

    <div v-else class="bg-white rounded-md shadow-window mt-10 px-4 py-7.5 text-center">
      <DashboardPage />
    </div>
  </div>
</template>

