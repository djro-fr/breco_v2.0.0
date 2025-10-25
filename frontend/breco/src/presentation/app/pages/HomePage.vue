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
    <h1>Breco</h1>
    <h2>Application de covoiturage en Bretagne</h2>

    <div v-if="!isAuthenticated" class="whiteWindow">
      <h3>Connexion</h3>
      <form @submit.prevent="handleLogin">
        <input v-model="email" type="email" placeholder="Email" aria-label="Email" required />
        <input
          v-model="password"
          type="password"
          placeholder="Mot de passe"
          aria-label="Mot de passe"
          required
        />
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

    <div v-else class="whiteWindow text-center">
      <DashboardPage />
    </div>
  </div>
</template>

<style scoped>
h1,
h2,
h3 {
  text-align: center;
  margin: 0;
  padding: 0;
}
h1 {
  color: white;
  margin-top: 0px;
  line-height: 1.5;
  font-weight: 800;
}
h2 {
  color: white;
  line-height: 1.2;
  font-weight: 400;
}
h3 {
  padding-bottom: 16px;
  font-size: var(--fontL);
}

div.whiteWindow {
  margin-top: 40px;
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
  margin-bottom: 24px;
}

button.btn-secondary {
  margin-top: 4px;
}

div.forget {
  margin-top: -8px;
  margin-bottom: 16px;
  font-size: var(--fontXXS);
}
</style>
