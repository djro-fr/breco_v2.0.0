// frontend/breco/src/domain/schemas/validationSchemas.ts

import { z } from 'zod'

export const emailSchema = z
  .string({ message: "L'email est requis" })
  .min(1, "L'email est requis")
  .check(z.email({ message: "Format d'e-mail invalide" }))
  .toLowerCase()
  .trim()

export const phoneSchema = z
  .string({ message: 'Le téléphone est requis' })
  .trim()
  .refine(
    (val) => {
      const cleaned = val.replace(/\s/g, '')
      return /^0[1-9]\d{8}$/.test(cleaned)
    },
    {
      message: 'Format téléphone invalide (10 chiffres commençant par 0)',
    },
  )

export const passwordSchema = z
  .string({ message: 'Le mot de passe est requis' })
  .min(8, 'Le mot de passe doit contenir au moins 8 caractères')
  .refine((val) => /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(val), {
    message: 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre',
  })

export const nameSchema = (fieldName: string) =>
  z
    .string({ message: `Le ${fieldName} est requis` })
    .trim()
    .max(50, `Le ${fieldName} ne peut pas dépasser 50 caractères`)
    .refine((val) => /^[a-zA-ZÀ-ÿ\s'\u2019-]+$/.test(val), {
      message: `Le ${fieldName} contient des caractères invalides`,
    })

export const hourSchema = z
  .string()
  .regex(/^([01]\d|2[0-3]):([0-5]\d)$/, {
    message: "Format d'heure invalide (HH:MM)",
  })
  .optional()
