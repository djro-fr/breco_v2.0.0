// @vitest-environment node
// frontend/breco/src/__tests__/e2e/auth/Tc69.register.e2e.spec.ts
// TC-69 | E2E | Selenium | Full registration flow via form
// Preconditions: Frontend on http://localhost:3001, Backend + MailHog running in Docker

import { describe, it, expect, beforeAll, afterAll } from 'vitest'
import { Builder, Browser, By, until, WebDriver, WebElement } from 'selenium-webdriver'
import type { Locator } from 'selenium-webdriver'
import firefox from 'selenium-webdriver/firefox.js'

// ─── Config ────────────────────────────────────────────────────────────────

const VPS_IP      = process.env.VPS_IP ?? 'localhost'
const BASE_URL    = `http://${VPS_IP}:3001`
const MAILHOG_URL = `http://${VPS_IP}:8025/api/v2/messages`
const TIMEOUT     = 10_000

// Unique email per run: assigned in beforeAll to ensure Date.now() matches the actual test execution time
let uniqueEmail: string

// ─── Types ─────────────────────────────────────────────────────────────────

interface MailHogRecipient {
  Mailbox: string
  Domain:  string
}

interface MailHogMessage {
  To: MailHogRecipient[]
}

interface MailHogResponse {
  items: MailHogMessage[]
}

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

/** Returns true if MailHog received an email addressed to toEmail */
const emailMatchesRecipient = (toEmail: string) =>
  (recipient: MailHogRecipient): boolean =>
    `${recipient.Mailbox}@${recipient.Domain}` === toEmail

/** Returns true if any message in the MailHog response was sent to toEmail */
const parseMailHogBody = (body: string, toEmail: string): boolean => {
  const data: MailHogResponse = JSON.parse(body)
  return Boolean(
    data.items?.some((msg: MailHogMessage) =>
      msg.To?.some(emailMatchesRecipient(toEmail))
    )
  )
}

/** Handles the HTTP response from MailHog and resolves the promise */
const handleMailHogResponse = (
  res: import('node:http').IncomingMessage,
  toEmail: string,
  resolve: (value: boolean) => void
): void => {
  let body = ''
  res.on('data', (chunk: Buffer) => { body += chunk.toString() })
  res.on('end', () => {
    if (res.statusCode !== 200) return resolve(false)
    try {
      resolve(parseMailHogBody(body, toEmail))
    } catch {
      resolve(false)
    }
  })
}

/** Checks MailHog API using Node's native http module to bypass vitest-fetch-mock */
const checkMailHog = (toEmail: string): Promise<boolean> =>
  new Promise((resolve) => {
    import('node:http').then(({ request }) => {
      const req = request(MAILHOG_URL, (res) => handleMailHogResponse(res, toEmail, resolve))
      req.on('error', () => resolve(false))
      req.end()
    })
  })

// ─── TC-69 Suite ───────────────────────────────────────────────────────────

describe('TC-69 | E2E | Full registration flow via form', () => {
  let driver: WebDriver

  beforeAll(async () => {
    // Generate email here so Date.now() matches the moment the driver starts
    uniqueEmail = `tc69.test+${Date.now()}@breco.test`

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

  // ── Step 1: Contact ──────────────────────────────────────────────────────
  it('Step 1: Navigate to /register and fill in contact fields', async () => {
    await driver.get(`${BASE_URL}/register`)

    // Assert page title
    const h1 = await driver.wait(until.elementLocated(By.css('h1')), TIMEOUT)
    expect(await h1.getText()).toContain('Créer un compte Breco')

    // Email: blur triggers Zod validation
    await waitAndType(driver, By.css('input[type="email"]'), uniqueEmail)
    const emailInput = await driver.findElement(By.css('input[type="email"]'))
    await driver.executeScript('arguments[0].blur()', emailInput)

    // Password
    const [passwordInput, passwordConfirmInput]: WebElement[] = await driver.findElements(By.css('input[type="password"]'))
    if (!passwordInput || !passwordConfirmInput) throw new Error('Expected 2 password inputs')
    await passwordInput.sendKeys('Password123')
    await driver.executeScript('arguments[0].blur()', passwordInput)

    // Password confirmation
    await passwordConfirmInput.sendKeys('Password123')

    // Phone: blur triggers Zod validation
    await waitAndType(driver, By.css('input[type="tel"]'), '06 12 34 56 78')
    const phoneInput = await driver.findElement(By.css('input[type="tel"]'))
    await driver.executeScript('arguments[0].blur()', phoneInput)

    // Click "Suivant"
    await waitAndClick(driver, By.xpath("//button[contains(text(), 'Suivant')]"))

    // Assert step 2 is displayed
    const h2 = await driver.wait(
      until.elementLocated(By.xpath("//h2[contains(text(), 'Votre identité')]")),
      TIMEOUT
    )
    expect(await h2.getText()).toContain('Votre identité')
  }, TIMEOUT * 3)

  // ── Step 2: Identity ─────────────────────────────────────────────────────
  it('Step 2: Fill in identity information', async () => {
    // Gender
    await waitAndClick(driver, By.xpath("//button[contains(text(), 'Homme')]"))

    // First name: blur triggers Zod validation
    await waitAndType(driver, By.css('input[placeholder="Prénom"]'), 'Jean')
    const firstNameInput = await driver.findElement(By.css('input[placeholder="Prénom"]'))
    await driver.executeScript('arguments[0].blur()', firstNameInput)

    // Last name: blur triggers Zod validation
    await waitAndType(driver, By.css('input[placeholder="Nom"]'), 'Dupont')
    const lastNameInput = await driver.findElement(By.css('input[placeholder="Nom"]'))
    await driver.executeScript('arguments[0].blur()', lastNameInput)

    // Zip code (optional: filled for broader coverage)
    await waitAndType(driver, By.css('input[placeholder="Code Postal"]'), '29200')

    // Town (optional)
    await waitAndType(driver, By.css('input[placeholder="Ville"]'), 'Brest')

    // Click "Suivant"
    await waitAndClick(driver, By.xpath("//button[contains(text(), 'Suivant')]"))

    // Assert step 3 is displayed
    const h2 = await driver.wait(
      until.elementLocated(By.xpath("//h2[contains(text(), 'Votre véhicule')]")),
      TIMEOUT
    )
    expect(await h2.getText()).toContain('Votre véhicule')
  }, TIMEOUT * 2)

  // ── Step 3: Vehicle ──────────────────────────────────────────────────────
  it('Step 3: Skip vehicle step (no car)', async () => {
    // Select "Non": user is not a driver
    await waitAndClick(driver, By.xpath("//button[contains(text(), 'Non')]"))

    // Click "Suivant"
    await waitAndClick(driver, By.xpath("//button[contains(text(), 'Suivant')]"))

    // Assert step 4 (summary) is displayed
    const h2 = await driver.wait(
      until.elementLocated(By.xpath("//h2[contains(text(), 'Récapitulatif')]")),
      TIMEOUT
    )
    expect(await h2.getText()).toContain('Récapitulatif')
  }, TIMEOUT * 2)

  // ── Step 4: Summary + submit ─────────────────────────────────────────────
  it('Step 4: Verify summary data and submit the form', async () => {
    // Wait until the recap block contains actual user data (avoids matching empty .bg-white-back elements from other steps)
    await driver.wait(
      until.elementLocated(By.xpath("//*[contains(@class,'bg-white-back') and .//*[contains(text(),'Nom complet')]]")),
      TIMEOUT
    )
    const recap: WebElement = await driver.findElement(
      By.xpath("//*[contains(@class,'bg-white-back') and .//*[contains(text(),'Nom complet')]]")
    )
    const recapText = await recap.getText()

    expect(recapText).toContain('Jean')
    expect(recapText).toContain('Dupont')
    expect(recapText).toContain(uniqueEmail)

    // Click "Créer compte"
    await waitAndClick(driver, By.xpath("//button[@type='submit' and contains(text(), 'Créer compte')]"))

    // Assert step 5 (email verification screen) is displayed
    const h2 = await driver.wait(
      until.elementLocated(By.xpath("//h2[contains(text(), 'Vérifiez votre email')]")),
      TIMEOUT
    )
    expect(await h2.getText()).toContain('Vérifiez votre email')

    // Assert the user's email address appears in the confirmation message
    const confirmMsg: WebElement = await driver.findElement(
      By.xpath("//*[contains(text(), 'email de vérification a été envoyé')]")
    )
    expect(await confirmMsg.getText()).toContain(uniqueEmail)
  }, TIMEOUT * 3)

  // ── MailHog check ────────────────────────────────────────────────────────
  it('MailHog: Verification email was received', async () => {
    // Poll MailHog every second for up to 15s: email may arrive a few seconds after form submission
    const deadline = Date.now() + 30_000
    let emailReceived = false

    while (Date.now() < deadline) {
      emailReceived = await checkMailHog(uniqueEmail)
      if (emailReceived) break
      await new Promise<void>(resolve => setTimeout(resolve, 1000))
    }

    expect(emailReceived).toBe(true)
  }, TIMEOUT * 4)
})
