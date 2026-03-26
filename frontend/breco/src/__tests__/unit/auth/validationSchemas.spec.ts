// src/__tests__/unit/auth/validationSchemas.spec.ts

import { describe, it, expect } from 'vitest'
import { emailSchema, passwordSchema, phoneSchema, nameSchema } from '@/utils/validationSchemas'

describe('passwordSchema - S1 : Inscription', () => {

  it('TC-01 - mot de passe null', () => {
    const result = passwordSchema.safeParse(null)
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le mot de passe est requis")
  })

  it('TC-02 - mot de passe vide', () => {
    const result = passwordSchema.safeParse('')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le mot de passe doit contenir au moins 8 caractères")
  })

  it('TC-03 - mot de passe court < 8 car.', () => {
    const result = passwordSchema.safeParse('toto')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le mot de passe doit contenir au moins 8 caractères")
  })

  it('TC-04 - Mot de passe valide (8 car., maj + min + chiffre)', () => {
    const result = passwordSchema.safeParse('Toto1234')
    expect(result.success).toBe(true)
  })

  it('TC-05 - Mot de passe sans majuscule', () => {
    const result = passwordSchema.safeParse('toto1234')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre")
  })

  it('TC-06 - Mot de passe sans chiffre', () => {
    const result = passwordSchema.safeParse('Tititoto')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre")
  })
})

describe('emailSchema - S1 : Inscription ', () => {

  it('TC-07 - e-mail vide', () => {
    const result = emailSchema.safeParse('')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("L'email est requis")
  })

  it('TC-08 - e-mail incorrect (format invalide)', () => {
    const result = emailSchema.safeParse('toto@titi')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Format d'e-mail invalide")
  })

  it('TC-09 - e-mail avec format xxx@yyy.zzz', () => {
    const result = emailSchema.safeParse('toto@titi.com')
    expect(result.success).toBe(true)
  })
})

describe('phoneSchema - S1 : Inscription', () => {

  it('TC-10 - téléphone vide', () => {
    const result = phoneSchema.safeParse('')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Format téléphone invalide (10 chiffres commençant par 0)")
  })

  it('TC-11 - téléphone incorrect (trop court)', () => {
    const result = phoneSchema.safeParse('1234')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Format téléphone invalide (10 chiffres commençant par 0)")
  })

  it('TC-12 - téléphone incorrect (trop invalide)', () => {
    const result = phoneSchema.safeParse('1234567890')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Format téléphone invalide (10 chiffres commençant par 0)")
  })

  it('TC-13 - téléphone correct (10 chiffres commençant par 0)', () => {
    const result = phoneSchema.safeParse('0123456789')
    expect(result.success).toBe(true)
  })
})

describe('nameSchema - S1 : Inscription', () => {

  it('TC-14 - prénom vide', () => {
    const result = nameSchema('prénom').safeParse('')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le prénom contient des caractères invalides")
  })

  it('TC-15 - prénom trop long (> 50 car.)', () => {
    const result = nameSchema('prénom').safeParse('Jean-Baptiste-Alexandre-Sébastien-Emmanuel-Christophe-Nicolas')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le prénom ne peut pas dépasser 50 caractères")
  })

  it('TC-16 - prénom court (1 car.)', () => {
    const result = nameSchema('prénom').safeParse('A')
    expect(result.success).toBe(true)
  })

  it('TC-17 - prénom avec chiffres)', () => {
    const result = nameSchema('prénom').safeParse('R2D2')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le prénom contient des caractères invalides")
  })

  it('TC-18 - prénom avec accents (é,è,ë, ï)', () => {
    const result = nameSchema('prénom').safeParse('Noëmie')
    expect(result.success).toBe(true)
  })

  it('TC-19 - prénom avec apostrophe', () => {
    const result = nameSchema('prénom').safeParse("D’Angelo")
    expect(result.success).toBe(true)
  })

  it('TC-20 - prénom avec espaces', () => {
    const result = nameSchema('prénom').safeParse('Marie Line')
    expect(result.success).toBe(true)
  })

  it('TC-21 - prénom sans accents', () => {
    const result = nameSchema('prénom').safeParse('Sarah')
    expect(result.success).toBe(true)
  })


  it('TC-22 - nom de famille vide', () => {
    const result = nameSchema('nom de famille').safeParse('')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le nom de famille contient des caractères invalides")
  })

  it('TC-23 - nom de famille trop long (> 50 car.)', () => {
    const result = nameSchema('nom de famille').safeParse('Jean-Baptiste-Alexandre-Sébastien-Emmanuel-Christophe-Nicolas')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le nom de famille ne peut pas dépasser 50 caractères")
  })

  it('TC-24 - nom de famille court (1 car.)', () => {
    const result = nameSchema('nom de famille').safeParse('A')
    expect(result.success).toBe(true)
  })

  it('TC-25 - nom de famille avec chiffres)', () => {
    const result = nameSchema('nom de famille').safeParse('R2D2')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le nom de famille contient des caractères invalides")
  })

  it('TC-26 - nom de famille avec accents (é,è,ë, ï)', () => {
    const result = nameSchema('nom de famille').safeParse('Noëmie')
    expect(result.success).toBe(true)
  })

  it('TC-27 - nom de famille avec apostrophe', () => {
    const result = nameSchema('nom de famille').safeParse("D’Agobert")
    expect(result.success).toBe(true)
  })

  it('TC-28 - nom de famille avec espaces', () => {
    const result = nameSchema('nom de famille').safeParse('Dupont Durand')
    expect(result.success).toBe(true)
  })

  it('TC-29 - nom de famille sans accents', () => {
    const result = nameSchema('nom de famille').safeParse('Martineau')
    expect(result.success).toBe(true)
  })

})
