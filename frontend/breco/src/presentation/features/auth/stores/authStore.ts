// frontend/breco/src/presentation/features/auth/stores/authStore.ts

import { defineStore } from 'pinia'
import { ref, computed, type Ref, type ComputedRef } from 'vue'
import type { User } from '@/domain/entities/User'
import { LoginUseCase } from '@/domain/usecases/auth/LoginUseCase'
import { RegisterUseCase } from '@/domain/usecases/auth/RegisterUseCase'
import { VerifyTokenUseCase } from '@/domain/usecases/auth/VerifyTokenUseCase'
import { LogoutUseCase } from '@/domain/usecases/auth/LogoutUseCase'
import { AuthRepositoryImpl } from '@/data/repositories/AuthRepositoryImpl'
import type { AppException } from '@/domain/exceptions/AppException'
import type { AxiosError } from 'axios'

export const useAuthStore = defineStore('auth', () => {
  // Create the use case instances with the repository
  const authRepository = new AuthRepositoryImpl()
  const loginUseCase = new LoginUseCase(authRepository)
  const registerUseCase = new RegisterUseCase(authRepository)
  const verifyTokenUseCase = new VerifyTokenUseCase(authRepository)
  const logoutUseCase = new LogoutUseCase(authRepository)

  // Reactive state
  const user: Ref<User | null> = ref(null)
  const token: Ref<string | null> = ref(localStorage.getItem('token') || null)
  const isLoading: Ref<boolean> = ref(false)
  const error: Ref<string | null> = ref(null)

  // Computed
  const isAuthenticated: ComputedRef<boolean> = computed(() => !!token.value && !!user.value)

  // Action: Login
  const login = async (email: string, password: string): Promise<void> => {
    isLoading.value = true
    error.value = null
    try {
      const result = await loginUseCase.execute({ email, password })
      token.value = result.token
      user.value = result.user
      localStorage.setItem('token', result.token)
    } catch (err) {
      const axiosError = err as AxiosError

      if (axiosError.response?.status === 401) {
        error.value = 'Email ou mot de passe incorrect'
      } else if (axiosError.response?.status === 422) {
        error.value = 'Données invalides'
      } else {
        error.value = (err as AppException).message || 'Erreur de connexion'
      }
      throw err
    } finally {
      isLoading.value = false
    }
  }
  // Action: Register
  const register = async (
    email: string,
    phone: string,
    password: string,
    firstName: string,
    lastName: string,
    driver: boolean = false,
    gender?: 'Homme' | 'Femme' | 'Ne pas dire',
    zipCode?: string,
    town?: string,
    carModel?: string,
    carColor?: string,
    carSeatNb?: number,
  ): Promise<void> => {

    isLoading.value = true
    error.value = null
    try {
      const result = await registerUseCase.execute({
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
      token.value = result.token
      user.value = result.user
      localStorage.setItem('token', result.token)
    } catch (err) {
      const exception = err as AppException
      error.value = exception.message || "Erreur d'inscription"
      throw err
    } finally {
      isLoading.value = false
    }
  }
  // Action: Logout
  const logout = async (): Promise<void> => {
    isLoading.value = true
    try {
      await logoutUseCase.execute()
    } finally {
      clearAuth()
      isLoading.value = false
    }
  }
  // Action: Check the token at startup
  const checkAuth = async (): Promise<void> => {
    const storedToken = localStorage.getItem('token')
    if (storedToken) {
      token.value = storedToken
      try {
        const userData = await verifyTokenUseCase.execute()
        user.value = userData
      } catch {
        clearAuth()
      }
    }
  }
  // Action: Erase the authentication data
  const clearAuth = (): void => {
    user.value = null
    token.value = null
    error.value = null
    localStorage.removeItem('token')
  }

  return {
    // State
    user,
    token,
    isLoading,
    error,
    isAuthenticated,

    // Actions
    login,
    register,
    logout,
    checkAuth,
    clearAuth,
  }
})
