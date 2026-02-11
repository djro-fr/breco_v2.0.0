// frontend/breco/src/domain/entities/Town.ts

import { z } from 'zod'
// Zod schema for Town validation
export const TownSchema = z.object({
  id: z.number().int().positive(),
  name: z.string().min(1),
  postal_code: z.string().refine(
    (val) => /^[0-9]{5}$/.test(val),
    { message: "Code postal invalide" }
  ),
  insee_code: z.string().refine(
    (val) => /^[0-9]{5}$/.test(val),
    { message: "Code insee invalide" }
  )
})

// TypeScript type inferred from Zod schema
export type TownData = z.infer<typeof TownSchema>

// Domain Entity
export class Town {
  constructor(
    public id: number,
    public name: string,
    public postalCode: string,
    public inseeCode: string
  ) {}

  // Business method
  getDisplayName(): string {
    return `${this.name} (${this.postalCode})`
  }
}
