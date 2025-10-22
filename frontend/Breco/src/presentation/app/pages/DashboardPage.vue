<script setup lang="ts">
import { useAuthStore } from '@/presentation/features/auth/stores/authStore'
const authStore = useAuthStore()
</script>

<template>
  <div>
    <h1>Tableau de bord</h1>
    <p>Bienvenue {{ authStore.user?.firstName }} !</p>
    <p>Email: {{ authStore.user?.email }}</p>
    <p>Nom complet: {{ authStore.user?.getFullName() }}</p>
  </div>
</template>

