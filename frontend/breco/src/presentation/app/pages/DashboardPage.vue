<script setup lang="ts">
import { useAuthStore } from '@/presentation/features/auth/stores/authStore'
const authStore = useAuthStore()
</script>

<template>
  <div class="bg-white rounded-md shadow-window p-8">
    <h1>Tableau de bord</h1>
    <p>Bienvenue {{ authStore.user?.firstName }} !</p>
    <p><strong>Nom complet :</strong> <span v-if="authStore.user?.gender==='Homme'">M. </span><span v-else-if="authStore.user?.gender==='Femme'">Mme. </span><span v-else> </span>  {{ authStore.user?.getFullName() }}</p>
    <p><strong>E-mail :</strong> {{ authStore.user?.email }}</p>
    <p><strong>Téléphone : </strong>{{ authStore.user?.phone }}</p>

    <p v-if="authStore.user?.zipCode || authStore.user?.town" class="summary-item">
      <strong>Localisation :</strong> {{ authStore.user?.zipCode }} {{ authStore.user?.town }}
    </p>
    <p v-if="authStore.user?.driver" class="summary-item">
            <strong>Véhicule :</strong> {{ authStore.user?.carModel }} ({{ authStore.user?.carColor }})
            - {{ authStore.user?.carSeatNb }} place(s)
    </p>
  </div>
</template>
