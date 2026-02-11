// orchestrates business logic and validates inputs for searching towns
// (intermediary between the presentation layer and data layer)

import type { ITownRepository, TownSearchResult } from '@/domain/repositories/ITownRepository'
import { AppException, ValidationException } from '@/domain/exceptions/AppException'
import { ZodError, z } from 'zod'

// Input validation schema
const SearchTownsInputSchema = z.object({
  query: z.string().min(2, "La recherche doit contenir au moins 2 caractères"),
  limit: z.number().int().min(1).max(50).optional().default(10)
})
export type SearchTownsInput = z.infer<typeof SearchTownsInputSchema>

export class SearchTownsUseCase {
  // Declare and initialize the town repository dependency
  constructor(private townRepository: ITownRepository) {}

  /**
   * Execute town search with validation
   *
   * @param input - Search query and optional limit
   * @returns TownSearchResult with validated Town entities
   * @throws ValidationException if input is invalid
   * @throws AppException if API call fails
   */
  async execute(input: SearchTownsInput): Promise<TownSearchResult> {
    try {
      // Validate input with Zod
      const validated = SearchTownsInputSchema.parse(input)

      // Call repository (business logic)
      return await this.townRepository.searchTowns(validated.query, validated.limit)

    } catch (error) {
      // Repository errors (AppException) are re-thrown as-is
      if (error instanceof AppException) {
        throw error
      }
      // Zod validation errors (client-side)
      if (error instanceof ZodError) {
        const firstError = error.issues[0]
        const message = firstError?.message || 'Données invalides'
        throw new ValidationException(message)
      }
      // Other unexpected errors
      throw error
    }
  }
}
