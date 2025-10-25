
import { vi } from 'vitest'

const localStorageMock: Storage = {
  getItem: vi.fn((key: string) => null),
  setItem: vi.fn((key: string, value: string) => {}),
  removeItem: vi.fn((key: string) => {}),
  clear: vi.fn(),
  key: vi.fn((index: number) => null),
  get length() { return 0 },
}

const sessionStorageMock: Storage = {
  getItem: vi.fn((key: string) => null),
  setItem: vi.fn((key: string, value: string) => {}),
  removeItem: vi.fn((key: string) => {}),
  clear: vi.fn(),
  key: vi.fn((index: number) => null),
  get length() { return 0 },
}

Object.defineProperty(globalThis, 'localStorage', {
  value: localStorageMock,
  writable: true,
})

Object.defineProperty(globalThis, 'sessionStorage', {
  value: sessionStorageMock,
  writable: true,
})

vi.mock('@vue/devtools-api', () => ({
  setupDevToolsApp: vi.fn(),
  setupDevToolsPlugin: vi.fn(),
}))

//console.log('localStorage est-il une fonction ?', typeof globalThis.localStorage.getItem === 'function')
