// frontend/breco/src/utils/validationSchemas.ts

import { z } from 'zod'

export const emailSchema = z
  .email({ message: "Format d'e-mail invalide" })
  .min(1, "L'email est requis")
  .toLowerCase()
  .trim()

  export const phoneSchema = z
  .string({ message: "Le téléphone est requis" })
  .trim()
  .refine(
    (val) => {
      const cleaned = val.replace(/\s/g, '')
      return /^0[1-9][0-9]{8}$/.test(cleaned)
    },
    {
      message: "Format téléphone invalide (10 chiffres commençant par 0)"
    }
  )

  export const passwordSchema = z
  .string({ message: "Le mot de passe est requis" })
  .min(8, "Le mot de passe doit contenir au moins 8 caractères")
  .refine((val) => /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(val), {
    message: "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre"
  })

  // nameSchema factory to customize field name in error messages
  export const nameSchema = (fieldName: string) =>
    z
      .string({ message: `Le ${fieldName} est requis` })
      .trim()
      .max(50, `Le ${fieldName} ne peut pas dépasser 50 caractères`)
      .refine((val) => /^[a-zA-ZÀ-ÿ\s'-]+$/.test(val), {
        message: `Le ${fieldName} contient des caractères invalides`
      })
