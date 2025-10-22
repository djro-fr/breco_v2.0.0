<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/presentation/features/auth/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const isAuthenticated = computed(() => authStore.isAuthenticated)

onMounted(async () => {
  await authStore.checkAuth()
})

const handleLogout = async (): Promise<void> => {
  await authStore.logout()
  router.push({ name: 'Login' })
}
</script>

<template>
  <div>
    <header>
      <nav>
        <router-link to="/">Breco</router-link>

        <div v-if="!isAuthenticated">
          <router-link to="/login">Connexion</router-link>
          <router-link to="/register">Inscription</router-link>
        </div>

        <div v-if="isAuthenticated">
          <router-link to="/dashboard">Dashboard</router-link>
          <span>{{ authStore.user?.firstName }}</span>
          <button @click="handleLogout">Déconnexion</button>
        </div>
      </nav>
    </header>

    <main>
      <router-view />
    </main>

    <footer>
      <p>&copy; 2025 Breco</p>
    </footer>
  </div>
</template>



<style scoped></style>
