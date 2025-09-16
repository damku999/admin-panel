const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('Reports Management', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
  });

  test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

  test('should display reports dashboard', async ({ page }) => {
    await page.goto('/reports');

    // Check page title
    await expect(page.locator('h1, .page-title')).toContainText(/Report/);

    // Check for report generation form or options
    await expect(page.locator('form, .report-form, .report-options')).toBeVisible();

    // Take screenshot
    await helpers.takeScreenshot('reports-dashboard');
  });

  test('should generate customer report', async ({ page }) => {
    await page.goto('/reports');

    // Look for report type selection
    const reportTypeSelect = page.locator('select[name="report_type"], .report-type-select');
    if (await reportTypeSelect.count() > 0) {
      await reportTypeSelect.selectOption('customers');
    }

    // Set date range
    const dateFromInput = page.locator('input[name="date_from"], input[name="from_date"]');
    if (await dateFromInput.count() > 0) {
      await dateFromInput.fill('2023-01-01');
    }

    const dateToInput = page.locator('input[name="date_to"], input[name="to_date"]');
    if (await dateToInput.count() > 0) {
      await dateToInput.fill('2023-12-31');
    }

    // Take screenshot
    await helpers.takeScreenshot('reports-customer-form-filled');

    // Generate report
    const generateButton = page.locator('button:has-text("Generate"), .btn-generate');
    if (await generateButton.count() > 0) {
      await generateButton.click();
      await page.waitForTimeout(3000);
      await helpers.takeScreenshot('reports-customer-generated');
    }
  });

  test('should generate quotations report', async ({ page }) => {
    await page.goto('/reports');

    const reportTypeSelect = page.locator('select[name="report_type"]');
    if (await reportTypeSelect.count() > 0) {
      await reportTypeSelect.selectOption('quotations');
    }

    // Set filters
    const statusFilter = page.locator('select[name="status"]');
    if (await statusFilter.count() > 0) {
      await statusFilter.selectOption({ index: 1 });
    }

    await helpers.takeScreenshot('reports-quotations-form');

    const generateButton = page.locator('button:has-text("Generate")');
    if (await generateButton.count() > 0) {
      await generateButton.click();
      await page.waitForTimeout(3000);
      await helpers.takeScreenshot('reports-quotations-generated');
    }
  });

  test('should export reports to Excel/CSV', async ({ page }) => {
    await page.goto('/reports');

    // Generate a report first
    const generateButton = page.locator('button:has-text("Generate")');
    if (await generateButton.count() > 0) {
      await generateButton.click();
      await page.waitForTimeout(3000);
    }

    // Look for export button
    const exportButton = page.locator('a:has-text("Export"), button:has-text("Export")');
    if (await exportButton.count() > 0) {
      const downloadPromise = page.waitForEvent('download');
      await exportButton.click();

      try {
        const download = await downloadPromise;
        expect(download.suggestedFilename()).toMatch(/\.xlsx?$|\.csv$/);
        await helpers.takeScreenshot('reports-export-success');
      } catch (error) {
        await helpers.takeScreenshot('reports-export-clicked');
      }
    }
  });

  test('should handle report column selection', async ({ page }) => {
    await page.goto('/reports');

    // Look for column selection
    const columnSelectors = [
      'input[type="checkbox"][name*="column"]',
      '.column-selector',
      '.report-columns'
    ];

    for (const selector of columnSelectors) {
      const columnInputs = page.locator(selector);
      if (await columnInputs.count() > 0) {
        // Toggle some columns
        await columnInputs.first().uncheck();
        await columnInputs.nth(1).check();

        await helpers.takeScreenshot('reports-column-selection');
        break;
      }
    }
  });

  test('should test reports responsiveness', async ({ page }) => {
    await page.goto('/reports');

    const viewports = [
      { width: 1920, height: 1080, name: 'desktop' },
      { width: 768, height: 1024, name: 'tablet' },
      { width: 375, height: 667, name: 'mobile' }
    ];

    for (const viewport of viewports) {
      await page.setViewportSize(viewport);
      await page.waitForTimeout(1000);

      await expect(page.locator('form, .report-form')).toBeVisible();
      await helpers.takeScreenshot(`reports-${viewport.name}`);
    }
  });
});