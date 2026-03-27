// frontend/breco/src/domain/usecases/auth/RegisterUseCase.ts

import type {
  IAuthRepository,
  RegisterInput,
  AuthOutput,
} from '@/domain/repositories/IAuthRepository'
import { RegisterInputSchema, RegisterDriverInputSchema } from '@/domain/repositories/IAuthRepository'
import { AppException, ValidationException } from '@/domain/exceptions/AppException'
import { ZodError } from 'zod'

export class RegisterUseCase {
  constructor(private readonly authRepository: IAuthRepository) {}

  async execute(input: RegisterInput): Promise<AuthOutput> {
    try {
      // Validation with driver schema if driver is true
      const schema = input.driver ? RegisterDriverInputSchema : RegisterInputSchema
      const validated = schema.parse(input)

      // Call to repository
      return await this.authRepository.register(validated)
    } catch (error) {
      // The errors of the repository (AppException with the server message)
      // are brought up as they are
      if (error instanceof AppException) {
        throw error
      }
      // Zod validation errors (client-side)
      if (error instanceof ZodError) {
        // Transform ZodError into ValidationException
        const firstError = error.issues[0]
        const message = firstError?.message || 'Données invalides'
        throw new ValidationException(message)
      }
      // Other unexpected errors
      throw error
    }
  }
}
