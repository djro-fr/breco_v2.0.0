import axiosInstance from '@/shared/api/axiosInstance'
import {
  AppException,
  ValidationException,
  NotFoundException
} from '@/domain/exceptions/AppException'
import axios from 'axios'

// DTO type from API (raw data format before mapping to domain entity)
export interface TownDTO {
  id: number
  name: string
  postal_code: string // snake_case to match API response, will be mapped to camelCase in domain entity
  insee_code: string
}

// API response (exactly matches the TownSearchResponse interface, for type safety)
interface TownSearchResponse {
  success: boolean
  data: TownDTO[]
  count: number
  query: string
}

// Remote data source for Town-related API calls
export class TownRemoteDataSource {
  // if the API endpoint changes, we only need to update this prefix (centralization)
  private readonly API_PREFIX = '/towns'

  // Helper method to extract error message from API response
  // (avoids generic error messages and provides more context to the user)
  private extractErrorMessage(errorData: unknown): string {
    if (typeof errorData === 'object' && errorData !== null) {
      const data = errorData as Record<string, unknown>
      const message = (data.error as string) || (data.message as string)
      if (message) return message
    }
    return ''
  }

  // Centralized error handling for Axios errors
  // (transforms API errors into domain-specific exceptions with meaningful messages)
  private handleAxiosError(error: unknown): never {
    if (axios.isAxiosError(error)) {
      const statusCode = error.response?.status || 500
      const serverMessage = this.extractErrorMessage(error.response?.data)
      switch (statusCode) {
        case 422:
          throw new ValidationException(serverMessage || 'Données invalides')
        case 404:
          throw new NotFoundException(serverMessage || 'Aucune ville trouvée')
        default:
          throw new AppException('API_ERROR', serverMessage || 'Une erreur est survenue', statusCode)
      }
    }
    if (error instanceof Error) {
      throw new AppException('NETWORK_ERROR', error.message, 0)
    }
    throw new AppException('UNKNOWN_ERROR', 'Une erreur inconnue est survenue', 500)
  }

  /**
   * Search towns by name or postal code
   */
  async searchTowns(query: string, limit: number = 10): Promise<TownSearchResponse> {
    try {
      const { data } = await axiosInstance.get<TownSearchResponse>(
        `${this.API_PREFIX}/search`,
        {
          params: { q: query, limit }
        }
      )
      return data
    } catch (error) {
      this.handleAxiosError(error)
    }
  }
}
