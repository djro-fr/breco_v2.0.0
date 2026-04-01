// src/__tests__/unit/town/townSearch.spec.ts

import { describe, it, expect } from 'vitest'
import { TownSearchSchema } from '@/domain/schemas/townSearch.schema.ts'

describe('TownSearchSchema - S2: Pre-registered Locations', () => {

  it("TC-82: enters no string (empty field) for town", () => {
    // Zod schema rejects empty input with a specific message.
    // In UI: autocomplete is not triggered, empty list displayed (no error shown to user).
    const result = TownSearchSchema.safeParse({})
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("La ville est requise")
  })

  it("TC-83: enters a town with a single letter", () => {
    // Zod schema rejects 1-letter input with a specific message.
    // In UI: autocomplete is not triggered, empty list displayed (no error shown to user).
    const result = TownSearchSchema.safeParse({q:"r"})
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Au moins 2 caractères")
  })

  it("TC-84: enters a town with special characters (@#<>?&+!)", () => {
    // Zod schema rejects this input with a specific message.
    // In UI: autocomplete is not triggered, empty list displayed (no error shown to user).
    const result = TownSearchSchema.safeParse({q:"@#<>?&+!"})
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Caractères non autorisés")
  })

})
