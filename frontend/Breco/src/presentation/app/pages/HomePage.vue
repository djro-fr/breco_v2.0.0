<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@/presentation/features/auth/stores/authStore'

const authStore = useAuthStore()
const isAuthenticated = computed(() => authStore.isAuthenticated)
</script>


<template>
  <div>
    <h1>Bienvenue sur Breco</h1>
    <p>Application de covoiturage</p>

    <div v-if="!isAuthenticated">
      <router-link to="/login">Se connecter</router-link>
      <router-link to="/register">S'inscrire</router-link>
    </div>

    <div v-else>
      <p>Bienvenue {{ authStore.user?.firstName }} !</p>
      <router-link to="/dashboard">Aller au dashboard</router-link>
    </div>
  </div>
</template>
