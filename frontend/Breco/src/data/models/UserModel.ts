import { User } from '@/domain/entities/User'

// data received from API
export interface UserDTO {
  id: number
  email: string
  firstName: string
  lastName: string
  createdAt?: string
}

export class UserModel {
  // Converts UserDTO into Entity Domain
  static fromJson(json: UserDTO): User {
    return new User(json.id, json.email, json.firstName, json.lastName, json.createdAt)
  }
  // Converts Entity Domain into JSON for API
  static toJson(user: User): UserDTO {
    return {
      id: user.id,
      email: user.email,
      firstName: user.firstName,
      lastName: user.lastName,
      createdAt: user.createdAt
    }
  }
}
