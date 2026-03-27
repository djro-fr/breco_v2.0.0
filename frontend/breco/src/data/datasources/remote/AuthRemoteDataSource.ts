// frontend/breco/src/data/datasources/remote/AuthRemoteDataSource.ts
import axiosInstance from '@/shared/api/axiosInstance'
import { UserModel, type UserDTO } from '@/data/models/UserModel'
import type { User } from '@/domain/entities/User'
import type { RegisterInput } from '@/domain/repositories/IAuthRepository'
import {
  AppException,
  ValidationException,
  UnauthorizedException,
  NotFoundException,
} from '@/domain/exceptions/AppException'
import axios from 'axios'

// API response format for authentication
interface AuthApiResponse {
  token?: string
  user?: UserDTO
  requiresVerification?: boolean
  message?: string
  success?: boolean
}

export class AuthRemoteDataSource {
  private readonly API_PREFIX = '/auth'

  private extractErrorMessage(errorData: unknown): string {
    if (typeof errorData === 'object' && errorData !== null) {
      const data = errorData as Record<string, unknown>
      const message = (data.error as string) || (data.message as string)
      if (message) return message
    }
    return ''
  }

  private handleAxiosError(error: unknown): never {
    if (axios.isAxiosError(error)) {
      const statusCode = error.response?.status || 500
      const serverMessage = this.extractErrorMessage(error.response?.data)
      switch (statusCode) {
        case 401:
          throw new UnauthorizedException(serverMessage || 'Non authentifié')
        case 404:
          throw new NotFoundException(serverMessage || 'Ressource non trouvée')
        case 422:
          throw new ValidationException(serverMessage || 'Données invalides')
        default:
          throw new AppException('API_ERROR', serverMessage || 'Une erreur est survenue', statusCode)
      }
    }
    if (error instanceof Error) {
      throw new AppException('NETWORK_ERROR', error.message, 0)
    }
    throw new AppException('UNKNOWN_ERROR', 'Une erreur inconnue est survenue', 500)
  }

  // API call to log in
  async login(email: string, password: string): Promise<AuthApiResponse> {
    try {
      const { data } = await axiosInstance.post<AuthApiResponse>(
        `${this.API_PREFIX}/login`,
        { email, password },
      )
      return data
    } catch (error) {
      this.handleAxiosError(error)
    }
  }

  // API call to register
  async register(input: RegisterInput): Promise<AuthApiResponse> {
    try {
      const { data } = await axiosInstance.post<AuthApiResponse>(
        `${this.API_PREFIX}/register`,
        input,
      )
      return data
    } catch (error) {
      this.handleAxiosError(error)
    }
  }

  // API call to log out
  async logout(): Promise<void> {
    const token = localStorage.getItem('token')
    try {
      await axiosInstance.post(
        `${this.API_PREFIX}/logout`,
        {},
        { headers: { Authorization: `Bearer ${token}` } },
      )
    } catch (error) {
      console.warn('Erreur lors du logout serveur:', error)
    }
  }

  // API call to check the token
  async verifyToken(): Promise<User> {
    const token = localStorage.getItem('token')
    const { data } = await axiosInstance.get<UserDTO>(
      `${this.API_PREFIX}/verify`,
      { headers: { Authorization: `Bearer ${token}` } },
    )
    return UserModel.fromJson(data)
  }
}
