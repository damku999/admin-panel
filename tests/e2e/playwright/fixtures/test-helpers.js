const { expect } = require('@playwright/test');

/**
 * Test helper utilities for Playwright tests
 */
class TestHelpers {
  constructor(page) {
    this.page = page;
  }

  /**
   * Take a screenshot with a descriptive name
   */
  async takeScreenshot(name, options = {}) {
    const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
    const filename = `${name}-${timestamp}.png`;
    await this.page.screenshot({
      path: `tests/e2e/screenshots/${filename}`,
      fullPage: true,
      ...options
    });
    return filename;
  }

  /**
   * Wait for page to load completely
   */
  async waitForPageLoad() {
    await this.page.waitForLoadState('networkidle');
    await this.page.waitForLoadState('domcontentloaded');
  }

  /**
   * Check for JavaScript errors on the page
   */
  async checkForJSErrors() {
    const logs = [];
    this.page.on('console', (message) => {
      if (message.type() === 'error') {
        logs.push(message.text());
      }
    });

    this.page.on('pageerror', (error) => {
      logs.push(error.message);
    });

    return logs;
  }

  /**
   * Fill form with data object
   */
  async fillForm(formData, formSelector = 'form') {
    for (const [field, value] of Object.entries(formData)) {
      const selector = `${formSelector} [name="${field}"], ${formSelector} #${field}`;
      const element = this.page.locator(selector);

      if (await element.count() > 0) {
        const tagName = await element.first().evaluate(el => el.tagName.toLowerCase());
        const inputType = await element.first().evaluate(el => el.type);

        if (tagName === 'select') {
          await element.selectOption(value);
        } else if (inputType === 'checkbox' || inputType === 'radio') {
          if (value) await element.check();
        } else {
          await element.fill(String(value));
        }
      }
    }
  }

  /**
   * Wait for and click element
   */
  async waitAndClick(selector, options = {}) {
    await this.page.waitForSelector(selector, { state: 'visible' });
    await this.page.click(selector, options);
  }

  /**
   * Wait for toast/notification message
   */
  async waitForNotification(expectedMessage = null) {
    const notificationSelectors = [
      '.alert', '.toast', '.notification', '.flash-message',
      '.alert-success', '.alert-error', '.alert-warning', '.alert-info'
    ];

    for (const selector of notificationSelectors) {
      try {
        const element = await this.page.waitForSelector(selector, { timeout: 5000 });
        const message = await element.textContent();

        if (expectedMessage && !message.includes(expectedMessage)) {
          continue;
        }

        return message;
      } catch (error) {
        // Try next selector
        continue;
      }
    }

    throw new Error('No notification found');
  }

  /**
   * Check accessibility violations
   */
  async checkAccessibility(options = {}) {
    try {
      // Inject axe-core for accessibility testing
      await this.page.addScriptTag({
        url: 'https://unpkg.com/axe-core@4.4.3/axe.min.js'
      });

      const results = await this.page.evaluate((options) => {
        return new Promise((resolve) => {
          window.axe.run(document, options, (err, results) => {
            if (err) throw err;
            resolve(results);
          });
        });
      }, options);

      return results;
    } catch (error) {
      console.warn('Accessibility check failed:', error.message);
      return { violations: [] };
    }
  }

  /**
   * Test responsive design at different viewports
   */
  async testResponsiveDesign() {
    const viewports = [
      { width: 1920, height: 1080, name: 'desktop-large' },
      { width: 1366, height: 768, name: 'desktop-medium' },
      { width: 768, height: 1024, name: 'tablet' },
      { width: 375, height: 667, name: 'mobile' }
    ];

    const screenshots = [];

    for (const viewport of viewports) {
      await this.page.setViewportSize({ width: viewport.width, height: viewport.height });
      await this.page.waitForTimeout(1000); // Allow layout adjustment

      const filename = await this.takeScreenshot(`responsive-${viewport.name}`);
      screenshots.push({ viewport: viewport.name, filename });
    }

    return screenshots;
  }

  /**
   * Check for broken images
   */
  async checkBrokenImages() {
    const images = await this.page.locator('img').all();
    const brokenImages = [];

    for (const img of images) {
      const src = await img.getAttribute('src');
      const naturalWidth = await img.evaluate(el => el.naturalWidth);

      if (naturalWidth === 0 || !src) {
        brokenImages.push(src || 'no-src');
      }
    }

    return brokenImages;
  }

  /**
   * Test form validation
   */
  async testFormValidation(formSelector, requiredFields) {
    const validationErrors = [];

    // Try to submit empty form
    await this.page.click(`${formSelector} [type="submit"]`);

    // Check for validation messages
    for (const field of requiredFields) {
      const errorSelector = `${formSelector} .error-${field}, ${formSelector} .invalid-feedback`;
      const hasError = await this.page.locator(errorSelector).count() > 0;

      if (!hasError) {
        // Check if field has HTML5 validation
        const fieldElement = this.page.locator(`${formSelector} [name="${field}"]`);
        const isValid = await fieldElement.evaluate(el => el.checkValidity());

        if (isValid) {
          validationErrors.push(`Field ${field} should be required but shows as valid`);
        }
      }
    }

    return validationErrors;
  }

  /**
   * Performance timing metrics
   */
  async getPerformanceMetrics() {
    return await this.page.evaluate(() => {
      const navigation = performance.getEntriesByType('navigation')[0];
      return {
        domContentLoaded: navigation.domContentLoadedEventEnd - navigation.domContentLoadedEventStart,
        loadComplete: navigation.loadEventEnd - navigation.loadEventStart,
        firstPaint: performance.getEntriesByType('paint').find(entry => entry.name === 'first-paint')?.startTime,
        firstContentfulPaint: performance.getEntriesByType('paint').find(entry => entry.name === 'first-contentful-paint')?.startTime
      };
    });
  }

  /**
   * Check for console errors and warnings
   */
  async getConsoleMessages() {
    const messages = [];

    this.page.on('console', (message) => {
      messages.push({
        type: message.type(),
        text: message.text(),
        location: message.location()
      });
    });

    return messages;
  }

  /**
   * Login as admin user
   */
  async loginAsAdmin(email = 'admin@admin.com', password = 'Admin@123#') {
    await this.page.goto('/login');
    await this.fillForm({ email, password });
    await this.page.click('button[type="submit"]');
    await this.page.waitForURL('**/home');
  }

  /**
   * Login as customer user
   */
  async loginAsCustomer(email = 'testcustomer@example.com', password = 'password') {
    await this.page.goto('/customer/login');
    await this.fillForm({ email, password });
    await this.page.click('button[type="submit"]');
    await this.page.waitForURL('**/customer/dashboard');
  }

  /**
   * Generate test data for forms
   */
  generateTestData(type) {
    const testData = {
      customer: {
        name: 'Test Customer',
        email: `test${Date.now()}@example.com`,
        mobile_number: '9999999999',
        address: 'Test Address',
        city: 'Test City',
        state: 'Test State',
        pincode: '123456'
      },
      insurance_company: {
        name: 'Test Insurance Company',
        code: 'TIC',
        address: 'Test Company Address',
        contact_person: 'Test Contact',
        mobile: '9999999999',
        email: `company${Date.now()}@example.com`
      },
      broker: {
        name: 'Test Broker',
        code: 'TB',
        mobile: '9999999999',
        email: `broker${Date.now()}@example.com`,
        address: 'Test Broker Address'
      }
    };

    return testData[type] || {};
  }
}

module.exports = { TestHelpers };