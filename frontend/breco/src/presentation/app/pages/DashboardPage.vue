<script setup lang="ts">
import { formatPhone } from '@/presentation/shared/utils/phoneFormatter';
import { useAuthStore } from '@/presentation/features/auth/stores/authStore'
const authStore = useAuthStore()
</script>

<template>
  <div class="flex self-center flex-col mx-auto px-6 pt-4 pb-7.5 w-full max-w-md bg-white rounded-md shadow-window">
    <h1 class="pb-4">Tableau de bord</h1>
    <p class="text-xl">Bienvenue {{ authStore.user?.firstName }} !</p>
    <ul class="list-disc pl-4 mt-4">
      <li><strong>Nom complet :</strong> <span v-if="authStore.user?.gender==='Homme'">M. </span><span v-else-if="authStore.user?.gender==='Femme'">Mme. </span><span v-else> </span>  {{ authStore.user?.getFullName() }}</li>
      <li><strong>E-mail :</strong> {{ authStore.user?.email }}</li>
      <li><strong>Téléphone : </strong>{{ formatPhone(authStore.user?.phone) }}</li>

      <li v-if="authStore.user?.zipCode || authStore.user?.town" class="summary-item">
        <strong>Localisation :</strong> {{ authStore.user?.zipCode }} {{ authStore.user?.town }}
      </li>
      <li v-if="authStore.user?.driver" class="summary-item">
              <strong>Véhicule :</strong> {{ authStore.user?.carModel }} ({{ authStore.user?.carColor }})
              - {{ authStore.user?.carSeatNb }} place(s)
      </li>

    </ul>
  </div>
</template>
