// frontend/breco/src/data/models/UserModel.ts

import { User, UserSchema, CreateUserSchema, type CreateUserData } from '@/domain/entities/User'
import { z } from 'zod'

// DTO = Data Transfer Object, data received from API
export interface UserDTO {
  id: number
  email: string
  phone: string
  firstName: string
  lastName: string
  driver: boolean
  createdAt?: string
  gender?: 'Homme'| 'Femme'| 'Ne pas dire',
  zipCode?: string
  town?: string
  carModel?: string
  carColor?: string
  carSeatNb?: number
}

export class UserModel {
  // Converts UserDTO (API) into Entity Domain
  static fromJson(json: UserDTO): User {
    // validates data with Zod before creating User entity
    const validated = UserSchema.parse(json)
    return new User(
      validated.id,
      validated.email,
      validated.phone,
      validated.firstName,
      validated.lastName,
      validated.driver,
      validated.createdAt,
      validated.gender,
      validated.zipCode,
      validated.town,
      validated.carModel,
      validated.carColor,
      validated.carSeatNb,
    )
  }

  // Safe parse JSON into User entity, returning errors if validation fails
  static fromJsonSafe(json: unknown): { success: true; user: User } | { success: false; errors: z.ZodError } {
    const result = UserSchema.safeParse(json)

    if (result.success) {
      const data = result.data
      return {
        success: true,
        user: new User(
          data.id,
          data.email,
          data.phone,
          data.firstName,
          data.lastName,
          data.driver,
          data.createdAt,
          data.gender,
          data.zipCode,
          data.town,
          data.carModel,
          data.carColor,
          data.carSeatNb,
        )
      }
    }
    return { success: false, errors: result.error }
  }


  // Converts Entity Domain into JSON for API
  // Unsafe, assumes User entity is valid
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

  static validateForCreation(data: unknown): { success: true; data: CreateUserData } | { success: false; errors: z.ZodError } {
    const result = CreateUserSchema.safeParse(data)
    if (result.success) {
      return { success: true, data: result.data }
    }
    return { success: false, errors: result.error }
  }

  // Converts array of UserDTO into array of User entities
  static fromJsonArray(jsonArray: UserDTO[]): User[] {
    return jsonArray.map(json => UserModel.fromJson(json))
  }

  // Safe version of fromJsonArray, filtering out invalid entries
  static fromJsonArraySafe(jsonArray: unknown[]): User[] {
    return jsonArray
      .map(json => UserModel.fromJsonSafe(json))
      .filter(result => result.success)
      .map(result => (result as { success: true; user: User }).user)
  }


}
