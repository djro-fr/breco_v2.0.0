// @vitest-environment node
// frontend/breco/src/__tests__/e2e/auth/Tc70.login.e2e.spec.ts
// TC-70 | E2E | Selenium | Login flow + redirection
// Preconditions: Frontend on http://localhost:3001, user test@test.com / Password123 exists and is verified in DB (id=1)

import { describe, it, expect, beforeAll, afterAll } from 'vitest'
import { Builder, Browser, By, until, WebDriver, WebElement } from 'selenium-webdriver'
import type { Locator } from 'selenium-webdriver'
import firefox from 'selenium-webdriver/firefox.js'

// ─── Config ────────────────────────────────────────────────────────────────

const VPS_IP   = process.env.VPS_IP ?? 'localhost'
const BASE_URL = `http://${VPS_IP}:3001`
const TIMEOUT  = 10_000

const TEST_EMAIL    = 'test@test.com'
const TEST_PASSWORD = 'Password123'

// ─── Helpers ───────────────────────────────────────────────────────────────

/** Waits for an element to be located and visible, then clicks it */
const waitAndClick = async (driver: WebDriver, locator: Locator): Promise<WebElement> => {
  const el = await driver.wait(until.elementLocated(locator), TIMEOUT)
  await driver.wait(until.elementIsVisible(el), TIMEOUT)
  await el.click()
  return el
}

/** Waits for an element to be located and visible, then types into it */
const waitAndType = async (driver: WebDriver, locator: Locator, text: string): Promise<WebElement> => {
  const el = await driver.wait(until.elementLocated(locator), TIMEOUT)
  await driver.wait(until.elementIsVisible(el), TIMEOUT)
  await el.clear()
  await el.sendKeys(text)
  return el
}

/** Returns all localStorage keys and values as a plain object */
const getLocalStorage = async (driver: WebDriver): Promise<Record<string, string>> => {
  return driver.executeScript<Record<string, string>>(`
    const result = {}
    for (let i = 0; i < localStorage.length; i++) {
      const key = localStorage.key(i)
      if (key) result[key] = localStorage.getItem(key) ?? ''
    }
    return result
  `)
}

/** Returns all sessionStorage keys and values as a plain object */
const getSessionStorage = async (driver: WebDriver): Promise<Record<string, string>> => {
  return driver.executeScript<Record<string, string>>(`
    const result = {}
    for (let i = 0; i < sessionStorage.length; i++) {
      const key = sessionStorage.key(i)
      if (key) result[key] = sessionStorage.getItem(key) ?? ''
    }
    return result
  `)
}

/** Returns all cookies as key-value pairs */
const getCookies = async (driver: WebDriver): Promise<Record<string, string>> => {
  const cookies = await driver.manage().getCookies()
  return Object.fromEntries(cookies.map(c => [c.name, c.value]))
}

// ─── TC-70 Suite ───────────────────────────────────────────────────────────

describe('TC-70 | E2E | Login flow + redirection', () => {
  let driver: WebDriver

  beforeAll(async () => {
    const options = new firefox.Options()
    options.addArguments('--headless')

    driver = await new Builder()
      .forBrowser(Browser.FIREFOX)
      .setFirefoxOptions(options)
      .build()
  }, TIMEOUT)

  afterAll(async () => {
    if (driver) await driver.quit()
  })

  // ── Step 1: Fill in login form ───────────────────────────────────────────
  it('Step 1: Navigate to /login and fill in credentials', async () => {
    await driver.get(`${BASE_URL}/login`)

    // Assert page title
    const h1 = await driver.wait(until.elementLocated(By.css('h1')), TIMEOUT)
    expect(await h1.getText()).toContain('Connexion')

    // Email
    await waitAndType(driver, By.css('input[type="email"]'), TEST_EMAIL)

    // Password
    await waitAndType(driver, By.css('input[type="password"]'), TEST_PASSWORD)

    // Submit form
    await waitAndClick(driver, By.css('button[type="submit"]'))

    // Wait for navigation away from /login
    await driver.wait(async () => {
      const url = await driver.getCurrentUrl()
      return !url.endsWith('/login')
    }, TIMEOUT, 'Expected navigation away from /login after submit')

    const currentUrl = await driver.getCurrentUrl()

    // Assert redirection to search page
    expect(currentUrl).toContain('/search')
  }, TIMEOUT * 3)

  // ── Step 2: JWT token is stored ──────────────────────────────────────────
  it('Step 2: JWT token is stored after successful login', async () => {
    const localStorage   = await getLocalStorage(driver)
    const sessionStorage = await getSessionStorage(driver)
    const cookies        = await getCookies(driver)

    // Collect all storage values to find the JWT
    const allValues = [
      ...Object.values(localStorage),
      ...Object.values(sessionStorage),
      ...Object.values(cookies),
    ]

    // A JWT always starts with "eyJ" (base64-encoded header)
    const jwtFound = allValues.some(v => typeof v === 'string' && v.startsWith('eyJ'))

    // Log storage keys to help locate the token if the test fails
    if (!jwtFound) {
      console.error('[DEBUG] localStorage keys:', Object.keys(localStorage))
      console.error('[DEBUG] sessionStorage keys:', Object.keys(sessionStorage))
      console.error('[DEBUG] cookie names:', Object.keys(cookies))
    }

    expect(jwtFound).toBe(true)
  }, TIMEOUT)
})
