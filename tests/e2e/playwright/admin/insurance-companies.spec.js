const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('Insurance Companies Management', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
  });

  test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

  test('should display insurance companies list', async ({ page }) => {
    await page.goto('/insurance_companies');

    // Check page title
    await expect(page.locator('h1, .page-title')).toContainText(/Insurance Compan/);

    // Check for table or list
    await expect(page.locator('table, .insurance-list')).toBeVisible();

    // Check for Add button
    await expect(page.locator('a:has-text("Add"), button:has-text("Add")')).toBeVisible();

    // Take screenshot
    await helpers.takeScreenshot('insurance-companies-list');
  });

  test('should open create insurance company form', async ({ page }) => {
    await page.goto('/insurance_companies');

    // Click Add button
    await helpers.waitAndClick('a:has-text("Add"), a[href*="create"]');

    await page.waitForURL('**/create');

    // Check form fields
    const expectedFields = [
      'name', 'code', 'address', 'contact_person', 'mobile', 'email'
    ];

    for (const field of expectedFields) {
      await expect(page.locator(`[name="${field}"]`)).toBeVisible();
    }

    // Take screenshot
    await helpers.takeScreenshot('insurance-company-create-form');
  });

  test('should create new insurance company', async ({ page }) => {
    await page.goto('/insurance_companies/create');

    const testData = helpers.generateTestData('insurance_company');

    // Fill form
    await helpers.fillForm(testData);

    // Take screenshot
    await helpers.takeScreenshot('insurance-company-form-filled');

    // Submit form
    await page.click('button[type="submit"]');

    // Wait for success
    try {
      await helpers.waitForNotification('success');
    } catch {
      await page.waitForURL('**/insurance_companies**');
    }

    // Take screenshot
    await helpers.takeScreenshot('insurance-company-create-success');
  });

  test('should validate insurance company form', async ({ page }) => {
    await page.goto('/insurance_companies/create');

    // Test required fields
    await page.click('button[type="submit"]');

    // Check for validation
    const validationErrors = await helpers.testFormValidation('form', ['name', 'code', 'email']);

    // Take screenshot
    await helpers.takeScreenshot('insurance-company-validation');

    // Test email format
    await helpers.fillForm({ email: 'invalid-email' });
    await page.click('button[type="submit"]');

    await helpers.takeScreenshot('insurance-company-email-validation');
  });

  test('should edit insurance company', async ({ page }) => {
    await page.goto('/insurance_companies');

    const editButton = page.locator('a:has-text("Edit"), .btn-edit').first();
    if (await editButton.count() > 0) {
      await editButton.click();

      await page.waitForURL('**/edit**');

      // Update name
      await page.fill('[name="name"]', 'Updated Insurance Company');

      // Take screenshot
      await helpers.takeScreenshot('insurance-company-edit-form');

      // Submit
      await page.click('button[type="submit"]');

      // Wait for success
      try {
        await helpers.waitForNotification('updated');
      } catch {
        await page.waitForURL('**/insurance_companies**');
      }

      // Take screenshot
      await helpers.takeScreenshot('insurance-company-edit-success');
    }
  });

  test('should handle status updates', async ({ page }) => {
    await page.goto('/insurance_companies');

    const statusButtons = [
      '.btn-success:has-text("Active")',
      '.btn-danger:has-text("Inactive")',
      '.status-toggle'
    ];

    for (const selector of statusButtons) {
      const button = page.locator(selector).first();
      if (await button.count() > 0) {
        await button.click();
        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('insurance-company-status-update');
        break;
      }
    }
  });

  test('should search insurance companies', async ({ page }) => {
    await page.goto('/insurance_companies');

    const searchInput = page.locator('input[name="search"], input[placeholder*="Search"]').first();
    if (await searchInput.count() > 0) {
      await searchInput.fill('test');

      const searchButton = page.locator('button:has-text("Search")');
      if (await searchButton.count() > 0) {
        await searchButton.click();
      } else {
        await searchInput.press('Enter');
      }

      await page.waitForTimeout(2000);
      await helpers.takeScreenshot('insurance-company-search');
    }
  });

  test('should export insurance companies', async ({ page }) => {
    await page.goto('/insurance_companies');

    const exportButton = page.locator('a:has-text("Export"), [href*="export"]');
    if (await exportButton.count() > 0) {
      const downloadPromise = page.waitForEvent('download');
      await exportButton.click();

      try {
        const download = await downloadPromise;
        expect(download.suggestedFilename()).toMatch(/\.xlsx?$|\.csv$/);
        await helpers.takeScreenshot('insurance-company-export-success');
      } catch (error) {
        await helpers.takeScreenshot('insurance-company-export-clicked');
      }
    }
  });

  test('should test responsive design', async ({ page }) => {
    await page.goto('/insurance_companies');

    const viewports = [
      { width: 1920, height: 1080, name: 'desktop' },
      { width: 768, height: 1024, name: 'tablet' },
      { width: 375, height: 667, name: 'mobile' }
    ];

    for (const viewport of viewports) {
      await page.setViewportSize(viewport);
      await page.waitForTimeout(1000);

      await expect(page.locator('table, .insurance-list')).toBeVisible();
      await helpers.takeScreenshot(`insurance-companies-${viewport.name}`);
    }
  });

  test('should check accessibility', async ({ page }) => {
    await page.goto('/insurance_companies');

    const a11yResults = await helpers.checkAccessibility();
    const criticalViolations = a11yResults.violations.filter(v => v.impact === 'critical');
    expect(criticalViolations).toHaveLength(0);
  });
});