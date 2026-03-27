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
  private readonly remoteDataSource: AuthRemoteDataSource

  constructor() {
    this.remoteDataSource = new AuthRemoteDataSource()
  }

  // Implements the login method of the interface
  async login(input: LoginInput): Promise<AuthOutput> {
    const response = await this.remoteDataSource.login(input.email, input.password)
    if (!response.token || !response.user) {
      throw new Error('Réponse invalide du serveur')
    }
    const user = UserModel.fromJsonUnsafe(response.user)
    return { token: response.token, user }
  }

  // Implements the register method of the interface
  async register(input: RegisterInput): Promise<AuthOutput> {
    const response = await this.remoteDataSource.register(input)

    if (response.requiresVerification) {
      return {
        requiresVerification: true,
        message: response.message,
        success: response.success,
      }
    }

    if (!response.token || !response.user) {
      throw new Error('Réponse invalide du serveur')
    }

    const user = UserModel.fromJson(response.user)
    return { token: response.token, user }
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
