import type { IAuthRepository, LoginInput, AuthOutput } from '@/domain/repositories/IAuthRepository'
import { ValidationException } from '@/domain/exceptions/AppException'

export class LoginUseCase {
  constructor(private authRepository: IAuthRepository) {}

  async execute(input: LoginInput): Promise<AuthOutput> {
    // email not empty
    if (!input.email || input.email.trim().length === 0) {
      throw new ValidationException('L\'email est requis')
    }
    // email must contain an @
    if (!input.email.includes('@')) {
      throw new ValidationException('Email invalide')
    }
    // password not empty
    if (!input.password || input.password.trim().length === 0) {
      throw new ValidationException('Le mot de passe est requis')
    }
    // password at least 6 characters
    if (input.password.length < 6) {
      throw new ValidationException('Le mot de passe doit avoir au moins 6 caractères')
    }
    // If everything is OK, call the repository to connect
    return await this.authRepository.login(input)
  }
}
