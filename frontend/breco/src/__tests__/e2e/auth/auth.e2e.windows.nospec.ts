import { describe, it, expect, beforeEach, afterEach } from 'vitest'
import { Builder, By, until } from 'selenium-webdriver'
import chrome from 'selenium-webdriver/chrome.js'
import path from 'path'

let driver

const BASE_URL = process.env.VITE_API_URL || 'http://localhost:3001'

describe('Auth E2E Tests (Windows Dev)', () => {
  beforeEach(async () => {
    const chromedriverPath = path.join(
      process.cwd(),
      'node_modules',
      'chromedriver',
      'lib',
      'chromedriver',
      'chromedriver.exe'
    )
    const options = new chrome.Options()
    options.addArguments('--window-size=1920,1080')
    const serviceBuilder = new chrome.ServiceBuilder(chromedriverPath)
    driver = await new Builder()
      .forBrowser('chrome')
      .setChromeOptions(options)
      .setChromeService(serviceBuilder)
      .build()
  }, 60000)

  afterEach(async () => {
    if (driver) {
      await driver.quit()
    }
  })

  it('should login successfully', async () => {
    await driver.get(`${BASE_URL}/login`)
    await driver.wait(until.elementLocated(By.css('input[type="email"]')), 10000)

    const emailInput = await driver.findElement(By.css('input[type="email"]'))
    const passwordInput = await driver.findElement(By.css('input[type="password"]'))
    const submitButton = await driver.findElement(By.css('button[type="submit"]'))

    await emailInput.sendKeys('test@test.com')
    await passwordInput.sendKeys('Password123')
    await submitButton.click()

    await driver.wait(until.urlContains('/dashboard'), 5000)
    const currentUrl = await driver.getCurrentUrl()
    expect(currentUrl).toContain('/dashboard')
  }, 30000)

  it('should register and navigate through steps', async () => {
    await driver.get(`${BASE_URL}/register`)
    await driver.wait(until.elementLocated(By.css('input[type="email"]')), 10000)

    const emailInput = await driver.findElement(By.css('input[type="email"]'))
    const passwordInputs = await driver.findElements(By.css('input[type="password"]'))
    const phoneInput = await driver.findElement(By.css('input[type="tel"]'))

    await emailInput.sendKeys('newuser@test.com')
    await passwordInputs[0].sendKeys('SecurePass123!')
    await passwordInputs[1].sendKeys('SecurePass123!')
    await phoneInput.sendKeys('+33612345678')

    const nextButton = await driver.findElement(By.css('.btn-primary'))
    await nextButton.click()

    await driver.wait(
      until.elementLocated(By.xpath("//*[contains(text(), 'Votre identité')]")),
      5000
    )
    const stepText = await driver.findElement(By.xpath("//*[contains(text(), 'Votre identité')]"))
    expect(await stepText.isDisplayed()).toBe(true)
  }, 30000)

  it('should show error on invalid email', async () => {
    await driver.get(`${BASE_URL}/login`)
    await driver.wait(until.elementLocated(By.css('input[type="email"]')), 10000)

    const emailInput = await driver.findElement(By.css('input[type="email"]'))
    const passwordInput = await driver.findElement(By.css('input[type="password"]'))
    const submitButton = await driver.findElement(By.css('button[type="submit"]'))

    await emailInput.sendKeys('invalid-email')
    await passwordInput.sendKeys('Password123')
    await submitButton.click()

    await driver.wait(
      until.elementLocated(By.css('.error-message')),
      5000
    )
    const errorMessage = await driver.findElement(By.css('.error-message'))
    expect(await errorMessage.isDisplayed()).toBe(true)
  }, 30000)

  it('should logout successfully', async () => {
    await driver.get(`${BASE_URL}/login`)
    await driver.wait(until.elementLocated(By.css('input[type="email"]')), 10000)

    const emailInput = await driver.findElement(By.css('input[type="email"]'))
    const passwordInput = await driver.findElement(By.css('input[type="password"]'))
    const submitButton = await driver.findElement(By.css('button[type="submit"]'))

    await emailInput.sendKeys('test@test.com')
    await passwordInput.sendKeys('Password123')
    await submitButton.click()

    await driver.wait(until.urlContains('/dashboard'), 5000)

    const logoutButton = await driver.wait(
      until.elementLocated(By.css('button[aria-label="Logout"]')),
      5000
    )
    await logoutButton.click()

    await driver.wait(until.urlContains('/login'), 5000)
    const currentUrl = await driver.getCurrentUrl()
    expect(currentUrl).toContain('/login')
  }, 30000)
})
