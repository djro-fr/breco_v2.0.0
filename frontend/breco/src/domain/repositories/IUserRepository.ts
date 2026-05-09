// frontend/breco/src/domain/repositories/IUserRepository.ts

import type { CreateUserData, UpdateUserData, User } from '../entities/User'

export interface IUserRepository {
  getById(id: number): Promise<User>
  getAll(): Promise<User[]>
  search(query: string): Promise<User[]>
  create(userData: CreateUserData): Promise<User>
  update(id: number, updates: Partial<UpdateUserData>): Promise<User>
  delete(id: number): Promise<void>
  getByEmail(email: string): Promise<User | null>
}
