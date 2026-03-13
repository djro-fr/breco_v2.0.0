/*
 * Calls API to search for towns by name or postal code
 * (implements ITownRepository)
 *
 * Converts API DTOs to Domain Entities with validation
 */



import type { ITownRepository, TownSearchResult } from '@/domain/repositories/ITownRepository'
import { TownRemoteDataSource } from '@/data/datasources/remote/TownRemoteDataSource'
import { TownModel } from '@/data/models/TownModel'

export class TownRepositoryImpl implements ITownRepository {
  private remoteDataSource: TownRemoteDataSource

  constructor() {
    this.remoteDataSource = new TownRemoteDataSource()
  }

  /**
   * Search towns by name or postal code
   *
   * @param query - Search query (min 2 chars)
   * @param limit - Max results (default 10)
   * @returns TownSearchResult with validated Town entities
   */
  async searchTowns(query: string, limit: number = 10): Promise<TownSearchResult> {
    // Call API via DataSource (returns DTOs)
    const response = await this.remoteDataSource.searchTowns(query, limit)

    // Convert DTOs to Domain Entities with validation
    const towns = TownModel.fromJsonArray(response.data)

    return {
      towns,
      count: response.count,
      query: response.query
    }
  }
}
