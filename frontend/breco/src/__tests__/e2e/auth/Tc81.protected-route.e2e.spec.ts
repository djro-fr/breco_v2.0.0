// @vitest-environment node

// frontend/breco/src/__tests__/e2e/auth/Tc81.protected-route.e2e.spec.ts

// TC-81 | E2E | Selenium | Direct navigation to protected route without JWT token
// Preconditions: Frontend on http://localhost:3001, no active session

import { describe, it, expect, beforeAll, afterAll } from 'vitest'
import { Builder, Browser, By, until, WebDriver } from 'selenium-webdriver'
import firefox from 'selenium-webdriver/firefox.js'

// ─── Config ────────────────────────────────────────────────────────────────

const BASE_URL = 'http://localhost:3001'
const TIMEOUT  = 10_000

// ─── TC-81 Suite ───────────────────────────────────────────────────────────

describe('TC-81 | E2E | Direct navigation to protected route without JWT token', () => {
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

  it('Navigating to /dashboard without token redirects to /login', async () => {
    // Clear any existing token from localStorage before navigating
    await driver.get(BASE_URL)
    await driver.executeScript('localStorage.removeItem("token")')

    // Attempt direct access to protected route
    await driver.get(`${BASE_URL}/dashboard`)

    // Assert redirection to /login
    await driver.wait(until.urlContains('/login'), TIMEOUT)
    const currentUrl = await driver.getCurrentUrl()
    expect(currentUrl).toContain('/login')

    // Assert login page is displayed
    const h1 = await driver.wait(until.elementLocated(By.css('h1')), TIMEOUT)
    expect(await h1.getText()).toContain('Connexion')
  }, TIMEOUT * 2)
})
