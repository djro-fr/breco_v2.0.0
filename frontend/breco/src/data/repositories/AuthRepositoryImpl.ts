// frontend/breco/src/data/repositories/AuthRepositoryImpl.ts
import type {
  IAuthRepository,
  LoginInput,
  RegisterInput,
  AuthOutput,
} from '@/domain/repositories/IAuthRepository'
import type { User } from '@/domain/entities/User'
import { AuthRemoteDataSource } from '@/data/datasources/remote/AuthRemoteDataSource'
import { UserModel } from '@/data/models/UserModel'

export class AuthRepositoryImpl implements IAuthRepository {
  private remoteDataSource: AuthRemoteDataSource

  constructor() {
    this.remoteDataSource = new AuthRemoteDataSource()
  }

  // Implements the login method of the interface
  async login(input: LoginInput): Promise<AuthOutput> {
    // Calls the data source to make the HTTP call
    const response = await this.remoteDataSource.login(input.email, input.password)

    if (!response.token || !response.user) {
      throw new Error('Réponse invalide du serveur')
    }

    const user = UserModel.fromJsonUnsafe(response.user)

    // Return the result to the Use Case
    return {
      token: response.token,
      user,
    }
  }

  // Implements the register method of the interface
  async register(input: RegisterInput): Promise<AuthOutput> {
    // Calls the data source to make the HTTP call
    const response = await this.remoteDataSource.register(
      input.email,
      input.phone,
      input.password,
      input.firstName,
      input.lastName,
      input.driver,
      input.gender,
      input.zipCode,
      input.town,
      input.carModel,
      input.carColor,
      input.carSeatNb,
    )

    // Check if email verification is required
    if (response.requiresVerification) {
      return {
        requiresVerification: true,
        message: response.message,
        success: response.success
      }
    }

    // Direct login with token and user
    if (!response.token || !response.user) {
      throw new Error('Réponse invalide du serveur')
    }

    const user = UserModel.fromJson(response.user)

    return {
      token: response.token,
      user,
    }
  }

  // Implements the logout method of the interface
  async logout(): Promise<void> {
    await this.remoteDataSource.logout()
  }

  // Implements the verifyToken method of the interface
  async verifyToken(): Promise<User> {
    return await this.remoteDataSource.verifyToken()
  }
}
