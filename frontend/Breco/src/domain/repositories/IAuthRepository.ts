import type { User } from '../entities/User'

export interface LoginInput {
  email: string
  password: string
}

export interface RegisterInput {
  email: string
  password: string
  firstName: string
  lastName: string
}

export interface AuthOutput {
  token: string
  user: User
}

export interface IAuthRepository {
  login(input: LoginInput): Promise<AuthOutput>
  register(input: RegisterInput): Promise<AuthOutput>
  logout(): Promise<void>
  verifyToken(): Promise<User>
}
