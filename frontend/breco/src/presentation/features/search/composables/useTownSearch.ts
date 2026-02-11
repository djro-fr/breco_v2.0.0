import { ref, computed } from 'vue'
import { SearchTownsUseCase } from '@/application/usecases/town/SearchTownsUseCase'
import { TownRepositoryImpl } from '@/data/repositories/TownRepositoryImpl'
import type { Town } from '@/domain/entities/Town'
import { ValidationException, AppException } from '@/domain/exceptions/AppException'

// Composable for town search business logic (used by TownAutocomplete.vue)
export function useTownSearch() {
  const towns = ref<Town[]>([])
  const isLoading = ref(false)
  const error = ref<string>('')
  const searchQuery = ref('')

  // Dependencies (created once)
  const repository = new TownRepositoryImpl()
  const searchUseCase = new SearchTownsUseCase(repository)

  const hasTowns = computed(() => towns.value.length > 0)
  const hasError = computed(() => error.value !== '')

  // orchesstrates communication with the backend
  const searchTowns = async (query: string, limit: number = 10) => {
    error.value = ''

    if (query.length < 2) {
      towns.value = []
      return
    }

    isLoading.value = true
    searchQuery.value = query

    try {
      const result = await searchUseCase.execute({ query, limit })
      towns.value = result.towns
    } catch (err) {
      if (err instanceof ValidationException) {
        error.value = err.message
      } else if (err instanceof AppException) {
        error.value = 'Une erreur est survenue lors de la recherche'
      } else {
        error.value = 'Erreur inconnue'
      }
      towns.value = []
    } finally {
      isLoading.value = false
    }
  }

  const clearSearch = () => {
    towns.value = []
    searchQuery.value = ''
    error.value = ''
  }

  return {
    towns,
    isLoading,
    error,
    searchQuery,
    hasTowns,
    hasError,
    searchTowns,
    clearSearch
  }
}
