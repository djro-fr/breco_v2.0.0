/* eslint-disable @typescript-eslint/no-explicit-any */
import { describe, it, expect, vi } from 'vitest'
import { LoginUseCase } from '@/domain/usecases/auth/LoginUseCase'
import { RegisterUseCase } from '@/domain/usecases/auth/RegisterUseCase'
import { LogoutUseCase } from '@/domain/usecases/auth/LogoutUseCase'

const mockRepository = {
  login: vi.fn(),
  register: vi.fn(),
  logout: vi.fn(),
}

describe('Auth Use Cases (Unit)', () => {
  it('LoginUseCase should call repository.login with input object', async () => {
    mockRepository.login.mockResolvedValue({ user: { id: 1 }, token: 'token' })
    const useCase = new LoginUseCase(mockRepository as any)

    const input = { email: 'test@test.com', password: 'Password123' }
    await useCase.execute(input)

    expect(mockRepository.login).toHaveBeenCalledWith(input)
  })

  it('RegisterUseCase should call repository.register', async () => {
    mockRepository.register.mockResolvedValue({ user: { id: 1 }, token: 'token' })
    const useCase = new RegisterUseCase(mockRepository as any)

    await useCase.execute({
      email: 'test@test.com',
      phone: '+33612345678',
      password: 'Password123',
      firstName: 'John',
      lastName: 'Doe',
    })

    expect(mockRepository.register).toHaveBeenCalled()
  })

  it('LogoutUseCase should call repository.logout', async () => {
    mockRepository.logout.mockResolvedValue({})
    const useCase = new LogoutUseCase(mockRepository as any)

    await useCase.execute()

    expect(mockRepository.logout).toHaveBeenCalled()
  })
})
