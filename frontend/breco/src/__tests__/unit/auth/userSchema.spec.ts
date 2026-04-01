// src/__tests__/unit/auth/userSchema.spec.ts

import { describe, it, expect } from 'vitest'
import { CreateUserSchema } from '@/domain/entities/User.ts'

describe('CreateUserSchema - S1: Register', () => {

  const validBase = {
    email: 'toto@titi.com',
    phone: '0123456789',
    firstName: 'Jean',
    lastName: 'Dupont',
    driver: false,
  }

  it('TC-30: invalid gender', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      gender: 'masculin'
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le genre doit être : Homme, Femme ou Ne pas dire")
  })

  it('TC-31a: null gender', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      gender: null
    })
    expect(result.success).toBe(true)
  })

  it('TC-31b: unspecified gender', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
    })
    expect(result.success).toBe(true)
  })

  it('TC-31c: empty gender', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      gender: ''
    })
    expect(result.success).toBe(false)
  })

  it('TC-32: gender from allowed list (\'Homme\', \'Femme\', \'Ne pas dire\')', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      gender: 'Ne pas dire'
    })
    expect(result.success).toBe(true)
  })

  it('TC-33a: invalid zip code (too long)', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      zipCode: '123456'
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Code postal invalide")
  })

  it('TC-33b: invalid zip code (letters)', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      zipCode: 'T3100'
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Code postal invalide")
  })

  it('TC-33c: invalid zip code (spaces)', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      zipCode: '31 000'
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Code postal invalide")
  })

  it('TC-34: null zip code', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      zipCode: null
    })
    expect(result.success).toBe(true)
  })

  it('TC-35: valid zip code', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      zipCode: '12345'
    })
    expect(result.success).toBe(true)
  })

  it('TC-36: driver boolean', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      driver: false
    })
    expect(result.success).toBe(true)
  })

  it('TC-37: unspecified town', () => {
    const result = CreateUserSchema.safeParse({ ...validBase })
    // town undefined
    expect(result.success).toBe(true)
  })

  it('TC-38: town too short (<2)', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      town: "Y"
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("La ville doit contenir au moins 2 caractères")
  })

  it('TC-39: town too long (>100)', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      town:  "Saint-Jean-de-la-Vallée-des-Charmes-Saint-Laurent-sur-Montagne-et-les-Bois-de-Fleurieu-dans-la-vallée-de-Dana"
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("La ville ne peut pas dépasser 100 caractères")
  })

  it('TC-40: valid town', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      town:  "Quimper"
    })
    expect(result.success).toBe(true)
  })

  it('TC-41: unspecified car model', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      // carModel undefined
    })
    expect(result.success).toBe(true)
  })

  it('TC-42: car model 1 char', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carModel:"A"
    })
    expect(result.success).toBe(true)
  })

  it('TC-43: car model > 50 chars', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carModel:"Ferrari Super-Ultra-Sport-Edition-Luxe-Generation-Turbo-Boost-Extreme-Speed"
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le modèle ne peut pas dépasser 50 caractères")
  })

  it('TC-44: valid car model', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carModel:"Renault Clio"
    })
    expect(result.success).toBe(true)
  })

  it('TC-45: unspecified car color', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      // carColor undefined
    })
    expect(result.success).toBe(true)
  })

  it('TC-46: car color 1 char', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carColor:"X"
    })
    expect(result.success).toBe(true)
  })

  it('TC-47: car color > 30 chars', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carColor:"Jaune-orangé-soleil-doré-brillant-intense"
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("La couleur ne peut pas dépasser 30 caractères")
  })

  it('TC-48: valid car color', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carColor:"Jaune"
    })
    expect(result.success).toBe(true)
  })

  it('TC-49: unspecified seat count', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      // carSeatNb undefined
    })
    expect(result.success).toBe(true)
  })

  it('TC-50: seat count = 0', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carSeatNb:0
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le nombre de places doit être au moins 1")
  })

  it('TC-51: seat count > 8', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carSeatNb:9
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le nombre de places ne peut pas dépasser 8")
  })

  it('TC-52: valid seat count', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carSeatNb:4
    })
    expect(result.success).toBe(true)
  })
})
