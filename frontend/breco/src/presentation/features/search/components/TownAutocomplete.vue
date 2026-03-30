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

const DEBOUNCE_DELAY_MS = 200
const BLUR_DELAY_MS = 200

const { towns, isLoading, searchTowns, clearSearch } = useTownSearch()

const inputValue = ref(props.modelValue)
const showResults = ref(false)
const selectedIndex = ref(-1)
let debounceTimeout: ReturnType<typeof setTimeout>

const isCompleteTown = computed(() => /^.+\s\(\d{5}\)$/.test(inputValue.value))
const isSearchable = computed(() => inputValue.value.length >= 2 && !isCompleteTown.value)
const hasVisibleResults = computed(() => showResults.value && towns.value.length > 0)
const shouldShowNoResults = computed(() =>
  showResults.value &&
  !isLoading.value &&
  towns.value.length === 0 &&
  isSearchable.value
)

const hideResults = () => {
  showResults.value = false
  selectedIndex.value = -1
}
const resetSearch = () => {
  hideResults()
  clearSearch()
}

watch(inputValue, (newValue) => {
  emit('update:modelValue', newValue)

  // Don't search if complete town
  if (isCompleteTown.value) {
    resetSearch()
    return
  }

  // Debounced search
  clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(async () => {
    if (isSearchable.value) {
      await searchTowns(newValue)
      showResults.value = true
    } else {
      resetSearch()
    }
  }, DEBOUNCE_DELAY_MS)
})

const selectTown = (town: Town) => {
  clearTimeout(debounceTimeout)

  inputValue.value = town.getDisplayName()
  emit('update:modelValue', town.getDisplayName())
  emit('select', town)

  resetSearch()
}

// Handle blur - user clicks outside
// timeout is needed to allow click events on results
// before hiding the dropdown
const handleBlur = () => {
  setTimeout(hideResults, BLUR_DELAY_MS)
}

// Handle focus - user clicks inside
const handleFocus = () => {
  if (isSearchable.value) {
    showResults.value = true
  }
}

// Keyboard navigation
const handleKeydown = (event: KeyboardEvent) => {
  if (!hasVisibleResults.value) return

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
      { // braces required to scope the const declaration inside the case block
        const selectedTown = towns.value[selectedIndex.value]
        if (selectedTown) {
          selectTown(selectedTown)
        }
      }
      break
    case 'Escape':
      hideResults()
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

    <div v-if="isLoading" class="absolute right-3 top-1/2 -translate-y-1/2">
      <div class="animate-spin h-4 w-4 border-2 border-primary-dark border-t-transparent rounded-full"></div>
    </div>

    <ul
      v-if="hasVisibleResults"
      class="absolute z-10 w-full mt-1 bg-white shadow-window rounded max-h-60 overflow-y-auto"
    >
      <li
        v-for="(town, index) in towns"
        :key="town.id"
        :class="[
          'px-4 py-2 cursor-pointer transition-colors',
          index === selectedIndex ? 'bg-primary-lightest40' : 'hover:bg-primary-lightest20'
        ]"
        @click="selectTown(town)"
      >
        {{ town.getDisplayName() }}
      </li>
    </ul>

    <div
      v-if="shouldShowNoResults"
      class="absolute z-10 w-full mt-1 bg-white shadow-window rounded px-4 py-2 text-gray-500"
    >
      Aucune ville trouvée
    </div>
  </div>
</template>
