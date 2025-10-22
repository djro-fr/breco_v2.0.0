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
  <div class="whiteWindow">
    <h1 class="text-center">Inscription</h1>
    <form @submit.prevent="handleRegister">
      <input v-model="firstName" type="text" placeholder="Prénom" required />
      <input v-model="lastName" type="text" placeholder="Nom" required />
      <input v-model="email" type="email" placeholder="Email" required />
      <input v-model="password" type="password" placeholder="Mot de passe" required />
      <div class="pa0 ma0 text-center">
        <button type="submit" :disabled="isLoading" class="btn-action w100">
          {{ isLoading ? 'Inscription...' : "S'inscrire" }}
        </button>
      </div>
    </form>
    <p v-if="error" style="color: red">{{ error }}</p>
    <div class="pa0 ma0 text-center">
      <button @click="$router.push('/login')" class="btn-secondary">Se connecter</button>
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
  margin-bottom: 16px;
}

button.btn-secondary {
  margin-top: 24px;
}


</style>
