import { describe, it, expect, beforeAll, afterAll } from 'vitest'
import { Browser, Builder } from 'selenium-webdriver'
import chrome from 'selenium-webdriver/chrome.js'
import path from 'path'

let driver

describe('E2E Basic Test', () => {
  beforeAll(async () => {
    const chromedriverPath = path.join(
      process.cwd(),
      'node_modules',
      'chromedriver',
      'lib',
      'chromedriver',
      'chromedriver.exe'
    )

    const options = new chrome.Options()
    const serviceBuilder = new chrome.ServiceBuilder(chromedriverPath)
    serviceBuilder.enableVerboseLogging()

    driver = await new Builder()
      .forBrowser(Browser.CHROME)
      .setChromeOptions(options)
      .setChromeService(serviceBuilder)
      .build()
  }, 10000)

  afterAll(async () => {
    if (driver) {
      await driver.quit()
    }
  })

  const BASE_URL = process.env.VITE_API_URL || 'http://37.59.101.232:3001'

  it('should open the app', async () => {
    await driver.get(BASE_URL)
    const title = await driver.getTitle()
    expect(title).toBeTruthy()
  }, 30000)
})
