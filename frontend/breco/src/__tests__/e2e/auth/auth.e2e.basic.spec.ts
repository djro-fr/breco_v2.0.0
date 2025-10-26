import { Browser, Builder } from 'selenium-webdriver';
import firefox from 'selenium-webdriver/firefox.js';
import assert from 'assert';

describe('UI - Test Selenium avec Firefox', function() {
  this.timeout(30000); // Timeout global pour tous les tests
  let driver;

  before(async function() {
    let options = new firefox.Options();
    options.addArguments('--headless');
    let serviceBuilder = new firefox.ServiceBuilder('/usr/bin/geckodriver');
    serviceBuilder.enableVerboseLogging();

    driver = await new Builder()
      .forBrowser(Browser.FIREFOX)
      .setFirefoxOptions(options)
      .setFirefoxService(serviceBuilder)
      .build();
  });

  after(async function() {
    await driver.quit();
  });

  it('Devrait ouvrir selenium-dev et vérifier le titre', async function() {
    await driver.get('https://www.selenium.dev');
    const title = await driver.getTitle();
    console.log('Titre de la page :', title);
    assert.ok(title.includes('Selenium'), 'Le titre devrait contenir "Selenium"');
  });
});
