<script setup lang="ts">
import { ref, watch, computed } from 'vue'
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
const { towns, isLoading, searchTowns, clearSearch } = useTownSearch()

// State
const inputValue = ref(props.modelValue)
const showResults = ref(false)
const selectedIndex = ref(-1)
const isSelecting = ref(false)
let debounceTimeout: ReturnType<typeof setTimeout>

const isCompleteTown = computed(() => /^.+\s\(\d{5}\)$/.test(inputValue.value))
const isSearchable = computed(() => inputValue.value.length >= 2 && !isCompleteTown.value)
const shouldShowNoResults = computed(() =>
  showResults.value &&
  !isLoading.value &&
  towns.value.length === 0 &&
  isSearchable.value
)

// Watch input changes
watch(inputValue, (newValue) => {
  // Skip if selecting
  if (isSelecting.value) {
    isSelecting.value = false
    return
  }

  emit('update:modelValue', newValue)

  // Don't search if complete town
  if (isCompleteTown.value) {
    showResults.value = false
    clearSearch()
    return
  }

  // Debounced search
  clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(async () => {
    if (isSearchable.value) {
      await searchTowns(newValue)
      showResults.value = true
    } else {
      showResults.value = false
      clearSearch()
    }
  }, 300)
})

// Select town
const selectTown = (town: Town) => {
  clearTimeout(debounceTimeout)
  isSelecting.value = true

  inputValue.value = town.getDisplayName()
  emit('update:modelValue', town.getDisplayName())
  emit('select', town)

  showResults.value = false
  clearSearch()
}

// Handle blur
const handleBlur = () => {
  setTimeout(() => {
    showResults.value = false
  }, 200)
}

// Handle focus
const handleFocus = () => {
  if (isSearchable.value) {
    showResults.value = true
  }
}

// Keyboard navigation
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
      const selectedTown = towns.value[selectedIndex.value]
      if (selectedTown) {
        selectTown(selectedTown)
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
      @focus="handleFocus"
      @keydown="handleKeydown"
    />

    <!-- Loading indicator -->
    <div v-if="isLoading" class="absolute right-3 top-1/2 -translate-y-1/2">
      <div class="animate-spin h-4 w-4 border-2 border-primary-dark border-t-transparent rounded-full"></div>
    </div>

    <!-- Results dropdown -->
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

    <!-- No results message -->
    <div
      v-if="shouldShowNoResults"
      class="absolute z-10 w-full mt-1 bg-white shadow-window rounded px-4 py-2 text-gray-500"
    >
      Aucune ville trouvée
    </div>
  </div>
</template>
