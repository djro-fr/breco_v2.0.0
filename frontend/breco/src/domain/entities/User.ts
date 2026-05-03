// frontend/breco/src/domain/entities/User.ts

import { z } from 'zod'
import { emailSchema, phoneSchema, nameSchema } from '@/domain/schemas/validationSchemas'

// Zod schema for User validation
export const UserSchema = z.object({
  id: z.number().int().positive(),
  email: emailSchema,
  phone: phoneSchema,
  firstName: nameSchema('prénom'),
  lastName: nameSchema('nom'),
  driver: z.boolean().default(false),
  createdAt: z.string().refine(
    (val) => !Number.isNaN(Date.parse(val)),
    { message: "Date invalide" }
    ).optional().nullable().transform(val => val ?? undefined),
  gender: z.enum(['Homme', 'Femme', 'Ne pas dire'], {
    error: "Le genre doit être : Homme, Femme ou Ne pas dire"
  }).optional().nullable()
    .transform(val => val ?? undefined),
  zipCode: z.string().refine(
    (val) => /^\d{5}$/.test(val),
    { message: "Code postal invalide" }
   ).optional().nullable().transform(val => val ?? undefined),
  town: z.string().min(2, "La ville doit contenir au moins 2 caractères")
    .max(100, "La ville ne peut pas dépasser 100 caractères")
    .optional().nullable().transform(val => val ?? undefined),
  carModel: z.string().max(50, "Le modèle ne peut pas dépasser 50 caractères")
    .optional().nullable().transform(val => val ?? undefined),
  carColor: z.string().max(30, "La couleur ne peut pas dépasser 30 caractères")
    .optional().nullable().transform(val => val ?? undefined),
  carSeatNb: z.number().int().min(1, "Le nombre de places doit être au moins 1")
    .max(8, "Le nombre de places ne peut pas dépasser 8").optional().nullable()
    .transform(val => val ?? undefined)
})

// Derived schemas for creating and updating users
// id and createdAt are set by backend
export const CreateUserSchema = UserSchema.omit({ id: true, createdAt: true })
// only id is required for updates, rest are optional
export const UpdateUserSchema = UserSchema.partial().required({ id: true })

// Schema to validate driver-specific fields
export const DriverUserSchema = UserSchema.refine(
  (data) => {
    if (data.driver) {
      return data.carModel && data.carColor && data.carSeatNb
    }
    return true
  },
  {
    message: "Les informations du véhicule sont obligatoires pour les conducteurs",
    path: ['carModel']
  }
)

// TypeScript types inferred from Zod schemas
export type UserData = z.infer<typeof UserSchema>
export type CreateUserData = z.infer<typeof CreateUserSchema>
export type UpdateUserData = z.infer<typeof UpdateUserSchema>


// Domain Entity
export class User {
  constructor(
    public id: number,
    public email: string,
    public phone: string,
    public firstName: string,
    public lastName: string,
    public driver: boolean = false,
    public createdAt?: string,
    public gender?: 'Homme'| 'Femme'| 'Ne pas dire',
    public zipCode?: string,
    public town?: string,
    public carModel?: string,
    public carColor?: string,
    public carSeatNb?: number,
  ) {}

  // Business Methods
  getFullName(): string {
    return `${this.firstName} ${this.lastName}`
  }
  isValid(): boolean {
    return this.id > 0 && this.email.length > 0 && this.firstName.length > 0
  }
  isDriver(): boolean {
    return this.driver
  }
  hasCompleteProfile(): boolean {
    return Boolean(
      this.email &&
      this.phone &&
      this.firstName &&
      this.lastName &&
      this.town &&
      this.zipCode
    )
  }
}
