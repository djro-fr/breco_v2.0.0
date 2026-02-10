<script setup lang="ts">
defineProps<{
  icon: string
  size?: string
}>()
</script>

<template>
  <i :class="['mdi', `mdi-${icon}`, size || 'text-xl']" aria-hidden="true" />
</template>
