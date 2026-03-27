// src/__tests__/unit/auth/userSchema.spec.ts

import { describe, it, expect } from 'vitest'
import { CreateUserSchema } from '@/domain/entities/User.ts'

describe('CreateUserSchema - S1 : Inscription', () => {

  const validBase = {
    email: 'toto@titi.com',
    phone: '0123456789',
    firstName: 'Jean',
    lastName: 'Dupont',
    driver: false,
  }

  it('TC-30 - genre invalide', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      gender: 'masculin'
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le genre doit être : Homme, Femme ou Ne pas dire")
  })

  it('TC-31a - genre null', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      gender: null
    })
    expect(result.success).toBe(true)
  })

  it('TC-31b - genre non renseigné', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
    })
    expect(result.success).toBe(true)
  })

  it('TC-31c - genre vide', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      gender: ''
    })
    expect(result.success).toBe(false)
  })


  it('TC-32 - genre parmi la liste (\'Homme\', \'Femme\', \'Ne pas dire\')', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      gender: 'Ne pas dire'
    })
    expect(result.success).toBe(true)
  })

  it('TC-33a - Code Postal invalide (trop long)', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      zipCode: '123456'
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Code postal invalide")
  })

  it('TC-33b - Code Postal invalide (lettres)', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      zipCode: 'T3100'
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Code postal invalide")
  })

  it('TC-33c - Code Postal invalide (espaces)', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      zipCode: '31 000'
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Code postal invalide")
  })

  it('TC-34 - Code Postal null', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      zipCode: null
    })
    expect(result.success).toBe(true)
  })

  it('TC-35 - Code Postal', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      zipCode: '12345'
    })
    expect(result.success).toBe(true)
  })

  it('TC-36 - Conducteur booléen', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      driver: false
    })
    expect(result.success).toBe(true)
  })

  it('TC-37 - Ville non renseignée', () => {
    const result = CreateUserSchema.safeParse({ ...validBase })
    // town undefined
    expect(result.success).toBe(true)
  })

  it('TC-38 - Ville trop courte (<2)', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      town: "Y"
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("La ville doit contenir au moins 2 caractères")
  })

  it('TC-39 - Ville trop longue (>100)', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      town:  "Saint-Jean-de-la-Vallée-des-Charmes-Saint-Laurent-sur-Montagne-et-les-Bois-de-Fleurieu-dans-la-vallée-de-Dana"
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("La ville ne peut pas dépasser 100 caractères")
  })

  it('TC-40 - Ville', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      town:  "Quimper"
    })
    expect(result.success).toBe(true)
  })

  it('TC-41 - Modèle de voiture non renseigné', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      // carModel undefined
    })
    expect(result.success).toBe(true)
  })

  it('TC-42 - Modèle de voiture 1 car.', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carModel:"A"
    })
    expect(result.success).toBe(true)
  })

  it('TC-43 - Modèle de voiture >50 car.', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carModel:"Ferrari Super-Ultra-Sport-Edition-Luxe-Generation-Turbo-Boost-Extreme-Speed"
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le modèle ne peut pas dépasser 50 caractères")
  })

  it('TC-44 - Modèle de voiture', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carModel:"Renault Clio"
    })
    expect(result.success).toBe(true)
  })

  it('TC-45 - Couleur de voiture non renseignée', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      // carColor undefined
    })
    expect(result.success).toBe(true)
  })

  it('TC-46 - Couleur 1 car.', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carColor:"X"
    })
    expect(result.success).toBe(true)
  })

  it('TC-47 - Couleur > 30 car.', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carColor:"Jaune-orangé-soleil-doré-brillant-intense"
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("La couleur ne peut pas dépasser 30 caractères")
  })

  it('TC-48 - Couleur de voiture', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carColor:"Jaune"
    })
    expect(result.success).toBe(true)
  })

  it('TC-49 - Nombre de places non renseigné', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      // carSeatNb undefined
    })
    expect(result.success).toBe(true)
  })

  it('TC-50 - Nombre de places = 0', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carSeatNb:0
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le nombre de places doit être au moins 1")
  })

  it('TC-51 - Nombre de places > 8', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carSeatNb:9
    })
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le nombre de places ne peut pas dépasser 8")
  })

  it('TC-52 - Nombre de places', () => {
    const result = CreateUserSchema.safeParse({ ...validBase,
      carSeatNb:4
    })
    expect(result.success).toBe(true)
  })
})



