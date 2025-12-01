// frontend/breco/src/domain/usecases/auth/LoginUseCase.ts
import type { IAuthRepository, LoginInput, AuthOutput } from '@/domain/repositories/IAuthRepository'
import { LoginInputSchema } from '@/domain/repositories/IAuthRepository'
import { ValidationException } from '@/domain/exceptions/AppException'
import { ZodError } from 'zod'

export class LoginUseCase {
  constructor(private authRepository: IAuthRepository) {}

  async execute(input: LoginInput): Promise<AuthOutput> {
    try {
      // Validation with Zod
      const validated = LoginInputSchema.parse(input)

      // Call to repository
      return await this.authRepository.login(validated)
    } catch (error) {
      if (error instanceof ZodError) {
        // Transform ZodError into ValidationException
        const firstError = error.issues[0]
        const message = firstError?.message || 'Données invalides'
        throw new ValidationException(message)
      }
      throw error
    }
  }
}
