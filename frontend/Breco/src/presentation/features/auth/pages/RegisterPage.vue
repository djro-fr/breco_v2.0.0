<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const firstName = ref('')
const lastName = ref('')
const email = ref('')
const password = ref('')
const isLoading = computed(() => authStore.isLoading)
const error = computed(() => authStore.error)

const handleRegister = async () => {
  try {
    await authStore.register(email.value, password.value, firstName.value, lastName.value)
    router.push({ name: 'Dashboard' })
  } catch (err) {
    console.error('Register error:', err)
  }
}
</script>

<template>
  <div>
    <h1>Inscription</h1>
    <form @submit.prevent="handleRegister">
      <input v-model="firstName" type="text" placeholder="Prénom" required />
      <input v-model="lastName" type="text" placeholder="Nom" required />
      <input v-model="email" type="email" placeholder="Email" required />
      <input v-model="password" type="password" placeholder="Mot de passe" required />
      <button type="submit" :disabled="isLoading">
        {{ isLoading ? 'Inscription...' : 'S\'inscrire' }}
      </button>
    </form>
    <p v-if="error" style="color: red">{{ error }}</p>
    <router-link to="/login">Se connecter</router-link>
  </div>
</template>
