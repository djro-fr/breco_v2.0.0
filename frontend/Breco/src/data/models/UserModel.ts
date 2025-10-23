import { User } from '@/domain/entities/User'

// data received from API
export interface UserDTO {
  id: number
  email: string
  phone: string
  firstName: string
  lastName: string
  driver: boolean
  createdAt?: string
  gender?: string
  zipCode?: string
  town?: string
  carModel?: string
  carColor?: string
  carSeatNb?: number
}

export class UserModel {
  // Converts UserDTO into Entity Domain
  static fromJson(json: UserDTO): User {
    return new User(
      json.id,
      json.email,
      json.phone,
      json.firstName,
      json.lastName,
      json.driver,
      json.createdAt,
      json.gender,
      json.zipCode,
      json.town,
      json.carModel,
      json.carColor,
      json.carSeatNb,
    )
  }
  // Converts Entity Domain into JSON for API
  static toJson(user: User): UserDTO {
    return {
      id: user.id,
      email: user.email,
      phone: user.phone,
      firstName: user.firstName,
      lastName: user.lastName,
      driver: user.driver,
      createdAt: user.createdAt,
      gender: user.gender,
      zipCode: user.zipCode,
      town: user.town,
      carModel: user.carModel,
      carColor: user.carColor,
      carSeatNb: user.carSeatNb,
    }
  }
}
