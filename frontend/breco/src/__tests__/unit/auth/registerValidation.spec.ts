// src/__tests__/unit/auth/registerValidation.spec.ts

import { describe, it, expect } from 'vitest'
import { isStep1Valid } from '@/utils/registerValidation.ts'

describe('S1 - Inscription', () => {

  it('TC-06b: Password confirmation does not match', () => {
    expect(isStep1Valid(
      'toto@titi.com', '',
      'Toto1234', '',
      'Toto5678',
      '0123456789', ''
    )).toBe(false)
  })

  it('TC-06c: Password confirmation matches', () => {
    expect(isStep1Valid(
      'toto@titi.com', '',
      'Toto1234', '',
      'Toto1234',
      '0123456789', ''
    )).toBe(true)
  })

})
