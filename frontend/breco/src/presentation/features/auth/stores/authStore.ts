// frontend/breco/src/presentation/features/auth/stores/authStore.ts
import { defineStore } from 'pinia'
import { ref, computed, type Ref, type ComputedRef } from 'vue'
import type { User } from '@/domain/entities/User'
import type { RegisterInput } from '@/domain/repositories/IAuthRepository'
import { LoginUseCase } from '@/application/usecases/auth/LoginUseCase'
import { RegisterUseCase } from '@/application/usecases/auth/RegisterUseCase'
import { VerifyTokenUseCase } from '@/application/usecases/auth/VerifyTokenUseCase'
import { LogoutUseCase } from '@/application/usecases/auth/LogoutUseCase'
import { AuthRepositoryImpl } from '@/data/repositories/AuthRepositoryImpl'
import { AppException } from '@/domain/exceptions/AppException'

export const useAuthStore = defineStore('auth', () => {
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
      if (!result.token || !result.user) {
        throw new Error('Réponse invalide du serveur')
      }
      token.value = result.token
      user.value = result.user
      localStorage.setItem('token', result.token)
    } catch (err) {
      if (err instanceof AppException) {
        error.value = err.message
      } else if (err instanceof Error) {
        error.value = err.message
      } else {
        error.value = 'Erreur de connexion'
      }
      throw err
    } finally {
      isLoading.value = false
    }
  }

  // Action: Register
  // Paramètres regroupés dans RegisterInput (fix SonarLint S107)
  const register = async (
    input: RegisterInput,
  ): Promise<{ requiresVerification?: boolean; message?: string }> => {
    isLoading.value = true
    error.value = null
    try {
      const result = await registerUseCase.execute(input)

      if (result.requiresVerification) {
        return {
          requiresVerification: true,
          message: result.message || 'Veuillez vérifier votre email',
        }
      }

      if (!result.token || !result.user) {
        throw new Error('Réponse invalide du serveur')
      }

      token.value = result.token
      user.value = result.user
      localStorage.setItem('token', result.token)

      return {}
    } catch (err) {
      if (err instanceof AppException) {
        error.value = err.message
      } else if (err instanceof Error) {
        error.value = err.message
      } else {
        error.value = "Erreur d'inscription"
      }
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
    user,
    token,
    isLoading,
    error,
    isAuthenticated,
    login,
    register,
    logout,
    checkAuth,
    clearAuth,
  }
})
