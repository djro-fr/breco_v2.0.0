// frontend/breco/src/domain/repositories/IAuthRepository.ts
import { z } from 'zod'
import { emailSchema, phoneSchema, nameSchema, passwordSchema } from '@/domain/schemas/validationSchemas'
import type { User } from '../entities/User'

// Validation schemas
export const LoginInputSchema = z.object({
  email: emailSchema,
  password: z.string().min(1, "Mot de passe requis")
})

export const RegisterInputSchema = z.object({
  email: emailSchema,
  phone: phoneSchema,
  password: passwordSchema,
  firstName: nameSchema('prénom'),
  lastName: nameSchema('nom'),
  driver: z.boolean().optional().default(false),
  gender: z.enum(['Homme', 'Femme', 'Ne pas dire']).optional(),
  zipCode: z.string().refine(
    (val) => /^\d{5}$/.test(val),
    { message: "Code postal invalide" }
  ).optional(),
  town: z.string().min(2).max(100).optional(),
  carModel: z.string().max(50).optional(),
  carColor: z.string().max(30).optional(),
  carSeatNb: z.number().int().min(1).max(8).optional()
})

// Driver-specific validation
export const RegisterDriverInputSchema = RegisterInputSchema.refine(
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

// Inferred types
export type LoginInput = z.infer<typeof LoginInputSchema>
export type RegisterInput = z.infer<typeof RegisterInputSchema>

// If requiresVerification: true -> no token nor user
// If normal connection -> token and user present
export interface AuthOutput {
  token?: string
  user?: User
  requiresVerification?: boolean
  message?: string
  success?: boolean
}

export interface IAuthRepository {
  login(input: LoginInput): Promise<AuthOutput>
  register(input: RegisterInput): Promise<AuthOutput>
  logout(): Promise<void>
  verifyToken(): Promise<User>
}
