// frontend/breco/src/presentation/features/search/composables/useTownSearch.ts

/*
 * Presentation composable for town search (used by TownAutocomplete.vue)
 * handles state and communication with the SearchTownsUseCase
 *
 * Zod validation
 *
 */

import { ref, computed } from 'vue'
import { SearchTownsUseCase } from '@/application/usecases/town/SearchTownsUseCase'
import { TownRepositoryImpl } from '@/data/repositories/TownRepositoryImpl'
import type { Town } from '@/domain/entities/Town'
import { ValidationException, AppException } from '@/domain/exceptions/AppException'
import { TownSearchSchema } from '@/domain/schemas/townSearch.schema'

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

  // orchestrates communication with the backend
  const searchTowns = async (query: string, limit: number = 10) => {
    error.value = ''
    towns.value = []

    // Zod validation in presentation layer before calling use case
    const result = TownSearchSchema.safeParse({ q: query, limit })
    if (!result.success) {
      error.value = result.error.issues[0]?.message ?? 'Données invalides'
      return
    }

    if (query.length < 2) return

    isLoading.value = true
    searchQuery.value = query

    try {
      const data = await searchUseCase.execute({ query: result.data.q, limit: result.data.limit })
      towns.value = data.towns
    } catch (err) {
      if (err instanceof ValidationException) {
        error.value = err.message
      } else if (err instanceof AppException) {
        error.value = 'Une erreur est survenue lors de la recherche'
      } else {
        error.value = 'Erreur inconnue'
      }
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
