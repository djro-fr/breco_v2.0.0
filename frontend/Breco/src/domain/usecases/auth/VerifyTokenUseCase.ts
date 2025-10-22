import type { IAuthRepository } from '@/domain/repositories/IAuthRepository'
import type { User } from '@/domain/entities/User'

export class VerifyTokenUseCase {
  constructor(private authRepository: IAuthRepository) {}

  async execute(): Promise<User> {
    return await this.authRepository.verifyToken()
  }
}
