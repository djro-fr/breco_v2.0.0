// frontend/breco/src/application/usecases/town/SearchTownsUseCase.ts

// orchestrates business logic and validates inputs for searching towns
// (intermediary between the presentation layer and data layer)

import type { ITownRepository, TownSearchResult } from '@/domain/repositories/ITownRepository'
import { AppException} from '@/domain/exceptions/AppException'

export type SearchTownsInput = {
  query: string
  limit?: number
}

export class SearchTownsUseCase {
  // Declare and initialize the town repository dependency
  constructor(private townRepository: ITownRepository) {}

  /**
   * Execute town search with validation
   *
   * @param input - Search query and optional limit
   * @returns TownSearchResult with validated Town entities
   * @throws AppException if API call fails
   */
  async execute(input: SearchTownsInput): Promise<TownSearchResult> {
    try {
      // Call repository (business logic)
      return await this.townRepository.searchTowns(input.query, input.limit ?? 10)
    } catch (error) {
      // Repository errors (AppException) are re-thrown as-is
      if (error instanceof AppException) {
        throw error
      }
      // Other unexpected errors
      throw error
    }
  }
}
