// frontend/breco/src/domain/schemas/townSearch.schema.ts
import { z } from 'zod'

export const TownSearchSchema = z.object({
  q: z.string({ message: "La ville est requise" })
    .min(2, 'Au moins 2 caractères')
    .regex(/^[^@#<>?&+!]+$/, 'Caractères non autorisés'),
  limit: z.number().min(1).max(50).default(10)
})

export type TownSearchParams = z.infer<typeof TownSearchSchema>
