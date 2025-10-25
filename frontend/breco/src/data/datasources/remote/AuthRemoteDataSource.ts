import axiosInstance from '@/shared/api/axiosInstance'
import { UserModel, type UserDTO } from '@/data/models/UserModel'
import type { User } from '@/domain/entities/User'

// API response format for authentication
interface AuthApiResponse {
  token: string
  user: UserDTO
}

export class AuthRemoteDataSource {
  private readonly API_PREFIX = '/auth'

  // API call to log in
  async login(email: string, password: string): Promise<AuthApiResponse> {
    const { data } = await axiosInstance.post<AuthApiResponse>(`${this.API_PREFIX}/login`, {
      email,
      password,
    })
    return data
  }

  // API call to register
  async register(
    email: string,
    phone: string,
    password: string,
    firstName: string,
    lastName: string,
    driver?: boolean,
    gender?: string,
    zipCode?: string,
    town?: string,
    carModel?: string,
    carColor?: string,
    carSeatNb?: number
  ): Promise<AuthApiResponse> {
    const { data } = await axiosInstance.post<AuthApiResponse>(`${this.API_PREFIX}/register`, {
      email,
      phone,
      password,
      firstName,
      lastName,
      driver,
      gender,
      zipCode,
      town,
      carModel,
      carColor,
      carSeatNb,
    })
    return data
  }

  // API call to log out
  async logout(): Promise<void> {
    const token = localStorage.getItem('token')
    try {
      await axiosInstance.post(
        `${this.API_PREFIX}/logout`,
        {},
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        },
      )
    } catch (error) {
      console.warn('Erreur lors du logout serveur:', error)
    }
  }

  // API call to check the token
  async verifyToken(): Promise<User> {
    const token = localStorage.getItem('token')
    const { data } = await axiosInstance.get<UserDTO>(`${this.API_PREFIX}/verify`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    // Convert DTO to Entity User
    return UserModel.fromJson(data)
  }
}
