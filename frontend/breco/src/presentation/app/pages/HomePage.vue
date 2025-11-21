<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/presentation/features/auth/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

onMounted(() => {
  if (authStore.isAuthenticated) {
    router.push({ name: 'Search' })
  } else {
    router.push({ name: 'Login' })
  }
})
</script>

<template>
  <div></div>
</template>
