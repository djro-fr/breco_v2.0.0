// src/__tests__/unit/auth/validationSchemas.spec.ts

import { describe, it, expect } from 'vitest'
import { emailSchema, passwordSchema, phoneSchema, nameSchema } from '@/domain/schemas/validationSchemas'

describe('passwordSchema - S1: Register', () => {

  it('TC-01: null password', () => {
    const result = passwordSchema.safeParse(null)
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le mot de passe est requis")
  })

  it('TC-02: empty password', () => {
    const result = passwordSchema.safeParse('')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le mot de passe doit contenir au moins 8 caractères")
  })

  it('TC-03: password too short (< 8 chars)', () => {
    const result = passwordSchema.safeParse('toto')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le mot de passe doit contenir au moins 8 caractères")
  })

  it('TC-04: valid password (8 chars, uppercase + lowercase + digit)', () => {
    const result = passwordSchema.safeParse('Toto1234')
    expect(result.success).toBe(true)
  })

  it('TC-05: password without uppercase', () => {
    const result = passwordSchema.safeParse('toto1234')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre")
  })

  it('TC-06a: password without digit', () => {
    const result = passwordSchema.safeParse('Tititoto')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre")
  })

})

describe('emailSchema - S1: Register ', () => {

  it('TC-07: empty email', () => {
    const result = emailSchema.safeParse('')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("L'email est requis")
  })

  it('TC-08: incorrect email (invalid format)', () => {
    const result = emailSchema.safeParse('toto@titi')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Format d'e-mail invalide")
  })

  it('TC-09: email with format xxx@yyy.zzz', () => {
    const result = emailSchema.safeParse('toto@titi.com')
    expect(result.success).toBe(true)
  })
})

describe('phoneSchema - S1: Register', () => {

  it('TC-10: empty phone number', () => {
    const result = phoneSchema.safeParse('')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Format téléphone invalide (10 chiffres commençant par 0)")
  })

  it('TC-11: incorrect phone (too short)', () => {
    const result = phoneSchema.safeParse('1234')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Format téléphone invalide (10 chiffres commençant par 0)")
  })

  it('TC-12: incorrect phone (invalid format)', () => {
    const result = phoneSchema.safeParse('+33612345678')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Format téléphone invalide (10 chiffres commençant par 0)")
  })

  it('TC-13: valid phone (10 digits starting with 0)', () => {
    const result = phoneSchema.safeParse('0123456789')
    expect(result.success).toBe(true)
  })
})

describe('nameSchema -  S1: Register', () => {

  it('TC-14: empty first name', () => {
    const result = nameSchema('prénom').safeParse('')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le prénom contient des caractères invalides")
  })

  it('TC-15: first name too long (> 50 chars)', () => {
    const result = nameSchema('prénom').safeParse('Jean-Baptiste-Alexandre-Sébastien-Emmanuel-Christophe-Nicolas')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le prénom ne peut pas dépasser 50 caractères")
  })

  it('TC-16: short first name (1 char)', () => {
    const result = nameSchema('prénom').safeParse('A')
    expect(result.success).toBe(true)
  })

  it('TC-17: first name with digits', () => {
    const result = nameSchema('prénom').safeParse('R2D2')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le prénom contient des caractères invalides")
  })

  it('TC-18: first name with accented chars (é,è,ë,ï)', () => {
    const result = nameSchema('prénom').safeParse('Noëmie')
    expect(result.success).toBe(true)
  })

  it('TC-19: first name with apostrophe', () => {
    const result = nameSchema('prénom').safeParse("D'Angelo")
    expect(result.success).toBe(true)
  })

  it('TC-20: first name with spaces', () => {
    const result = nameSchema('prénom').safeParse('Marie Line')
    expect(result.success).toBe(true)
  })

  it('TC-21: first name without accents', () => {
    const result = nameSchema('prénom').safeParse('Sarah')
    expect(result.success).toBe(true)
  })


  it('TC-22: empty last name', () => {
    const result = nameSchema('nom de famille').safeParse('')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le nom de famille contient des caractères invalides")
  })

  it('TC-23: last name too long (> 50 chars)', () => {
    const result = nameSchema('nom de famille').safeParse('Jean-Baptiste-Alexandre-Sébastien-Emmanuel-Christophe-Nicolas')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le nom de famille ne peut pas dépasser 50 caractères")
  })

  it('TC-24: short last name (1 char)', () => {
    const result = nameSchema('nom de famille').safeParse('A')
    expect(result.success).toBe(true)
  })

  it('TC-25: last name with digits', () => {
    const result = nameSchema('nom de famille').safeParse('R2D2')
    expect(result.success).toBe(false)
    expect(result.error?.issues[0]?.message).toBe("Le nom de famille contient des caractères invalides")
  })

  it('TC-26: last name with accented chars (é,è,ë,ï)', () => {
    const result = nameSchema('nom de famille').safeParse('Noëmie')
    expect(result.success).toBe(true)
  })

  it('TC-27: last name with apostrophe', () => {
    const result = nameSchema('nom de famille').safeParse("D'Agobert")
    expect(result.success).toBe(true)
  })

  it('TC-28: last name with spaces', () => {
    const result = nameSchema('nom de famille').safeParse('Dupont Durand')
    expect(result.success).toBe(true)
  })

  it('TC-29: last name without accents', () => {
    const result = nameSchema('nom de famille').safeParse('Martineau')
    expect(result.success).toBe(true)
  })

})
