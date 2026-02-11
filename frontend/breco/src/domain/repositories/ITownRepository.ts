import type { Town } from '@/domain/entities/Town'

// standardized search result format for town searches
export interface TownSearchResult {
  towns: Town[]
  count: number
  query: string
}

// contract for town data access, abstracting away implementation details
export interface ITownRepository {
  searchTowns(query: string, limit?: number): Promise<TownSearchResult>
}
