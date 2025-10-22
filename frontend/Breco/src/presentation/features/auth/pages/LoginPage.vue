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
    router.push({ name: 'Dashboard' })
  } catch (err) {
    console.error('Login error:', err)
  }
}
</script>

<template>
  <div class="whiteWindow">
    <h1 class="text-center pa0 ma0">Connexion</h1>
    <form @submit.prevent="handleLogin">
      <input v-model="email" type="email" placeholder="Email" required />
      <input v-model="password" type="password" placeholder="Mot de passe" required />

      <div class="text-center forget">
        <a href="#">Mot de passe oublié ?</a>
      </div>
      <div class="pa0 ma0 text-center">
        <button type="submit" :disabled="isLoading" class="btn-action w100">
          {{ isLoading ? 'Connexion...' : 'Se connecter' }}
        </button>
      </div>
    </form>
    <p v-if="error" style="color: red">{{ error }}</p>
    <p class="pa0 ma0 text-center">Pas encore inscrit ?</p>
    <div class="pa0 ma0 text-center">
      <button @click="$router.push('/register')" class="btn-secondary">S'inscrire</button>
    </div>
  </div>
</template>

<style scoped>
h1 {
  margin-top: 0px;
  line-height: 1;
  font-weight: 800;
  font-size: var(--fontL);
  padding-bottom: 16px;
}

div.whiteWindow {
  margin-top: 30px;
  padding: 30px 16px;
}
div.whiteWindow input {
  width: calc(100% - 10px);
  border: none;
  background-color: var(--dark-white);
  border-bottom: 1px solid var(--primary-color);
  margin-bottom: 20px;
  font-size: var(--fontXS);
  padding: 2px 5px;
}

button.btn-action {
  margin-bottom: 32px;
}

button.btn-secondary {
  margin-top: 4px;
}

div.forget {
  margin-top: -8px;
  margin-bottom: 16px;
}
</style>
