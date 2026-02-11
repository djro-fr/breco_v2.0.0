<script setup lang="ts">
import { ref, watch } from 'vue'
import { useTownSearch } from '../composables/useTownSearch'
import type { Town } from '@/domain/entities/Town'

interface Props {
  modelValue: string
  placeholder?: string
  name: string
  id: string
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Rechercher une ville...'
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
  'select': [town: Town]
}>()

// Composable
const { towns, isLoading, searchTowns } = useTownSearch()

const inputValue = ref(props.modelValue)
const showResults = ref(false)
const selectedIndex = ref(-1)

// Watch input changes. The debounce avoid making too many requests while typing
// and only search when there are at least 2 characters, waiting 300ms after the last keystroke
let debounceTimeout: ReturnType<typeof setTimeout>
watch(inputValue, (newValue) => {
  emit('update:modelValue', newValue)
  clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(async () => {
    if (newValue.length >= 2) {
      await searchTowns(newValue)
      showResults.value = true
    } else {
      showResults.value = false
    }
  }, 300)
})

// Select town
const selectTown = (town: Town) => {
  inputValue.value = town.getDisplayName()
  emit('update:modelValue', town.getDisplayName())
  emit('select', town)
  showResults.value = false
}

// Handle blur
const handleBlur = () => {
  // Delay to allow click on results
  setTimeout(() => {
    showResults.value = false
  }, 200)
}

// Keyboard navigation for accessibility
const handleKeydown = (event: KeyboardEvent) => {
  if (!showResults.value || towns.value.length === 0) return

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault()
      selectedIndex.value = Math.min(selectedIndex.value + 1, towns.value.length - 1)
      break
    case 'ArrowUp':
      event.preventDefault()
      selectedIndex.value = Math.max(selectedIndex.value - 1, -1)
      break
    case 'Enter':
      event.preventDefault()
      if (selectedIndex.value >= 0) {
        const selectedTown = towns.value[selectedIndex.value]
        if (selectedTown) {
          selectTown(selectedTown)
        }
      }
      break
    case 'Escape':
      showResults.value = false
      break
  }
}
</script>

<template>
  <div class="relative w-full">
    <input
      :id="id"
      :name="name"
      v-model="inputValue"
      type="text"
      autocomplete="off"
      :placeholder="placeholder"
      class="focus:border-action"
      @blur="handleBlur"
      @focus="showResults = inputValue.length >= 2"
      @keydown="handleKeydown"
    />

    <div v-if="isLoading" class="absolute right-3 top-1/2 -translate-y-1/2">
      <div class="animate-spin h-4 w-4 border-2 border-primary-dark border-t-transparent rounded-full"></div>
    </div>

    <ul
      v-if="showResults && towns.length > 0"
      class="absolute z-10 w-full mt-1 bg-white shadow-window rounded max-h-60 overflow-y-auto"
    >
      <li
        v-for="(town, index) in towns"
        :key="town.id"
        :class="[
          'px-4 py-2 cursor-pointer transition-colors',
          index === selectedIndex ? 'bg-primary-lightest text-primary-dark' : 'hover:bg-primary-lightest20'
        ]"
        @click="selectTown(town)"
      >
        {{ town.getDisplayName() }}
      </li>
    </ul>

    <div
      v-if="showResults && !isLoading && towns.length === 0 && inputValue.length >= 2"
      class="absolute z-10 w-full mt-1 bg-white shadow-window rounded px-4 py-2 text-gray-500"
    >
      Aucune ville trouvée
    </div>
  </div>
</template>
