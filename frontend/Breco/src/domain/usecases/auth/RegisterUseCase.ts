import type { IAuthRepository, RegisterInput, AuthOutput } from '@/domain/repositories/IAuthRepository'
import { ValidationException } from '@/domain/exceptions/AppException'

export class RegisterUseCase {
  constructor(private authRepository: IAuthRepository) {}

  async execute(input: RegisterInput): Promise<AuthOutput> {
    if (!input.firstName || input.firstName.trim().length === 0) {
      throw new ValidationException('Le prénom est requis')
    }
    if (!input.lastName || input.lastName.trim().length === 0) {
      throw new ValidationException('Le nom est requis')
    }
    if (!input.email || input.email.trim().length === 0) {
      throw new ValidationException('L\'email est requis')
    }
    if (!input.email.includes('@')) {
      throw new ValidationException('Email invalide')
    }
    if (!input.password || input.password.trim().length === 0) {
      throw new ValidationException('Le mot de passe est requis')
    }
    if (input.password.length < 6) {
      throw new ValidationException('Le mot de passe doit avoir au moins 6 caractères')
    }
    return await this.authRepository.register(input)
  }
}
