// frontend/breco/src/__tests__/e2e/auth/auth.e2e.basic.spec.ts
import { describe, it, beforeAll, afterAll, expect } from 'vitest'
import { Browser, Builder, WebDriver } from 'selenium-webdriver'
import firefox from 'selenium-webdriver/firefox.js'

describe('UI - Selenium Test with Firefox', () => {
  let driver: WebDriver

  beforeAll(async () => {
    const options = new firefox.Options()
    options.addArguments('--headless')

    const serviceBuilder = new firefox.ServiceBuilder('/usr/bin/geckodriver')
    serviceBuilder.enableVerboseLogging()

    driver = await new Builder()
      .forBrowser(Browser.FIREFOX)
      .setFirefoxOptions(options)
      .setFirefoxService(serviceBuilder)
      .build()
  }, 30000) // 30 second timeout for setup

  afterAll(async () => {
    if (driver) {
      await driver.quit()
    }
  })

  it('should open selenium-dev and verify title', async () => {
    await driver.get('https://www.selenium.dev')

    const title = await driver.getTitle()
    console.log('Page title:', title)

    expect(title).toContain('Selenium')
  }, 30000) // 30 second timeout for this test
})
