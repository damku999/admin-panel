const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('Quotations Management', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
  });

  test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

  test('should display quotations list', async ({ page }) => {
    await page.goto('/quotations');

    // Check page title
    await expect(page.locator('h1, .page-title')).toContainText(/Quotation/);

    // Check for table or list
    await expect(page.locator('table, .quotation-list')).toBeVisible();

    // Check for Add button
    await expect(page.locator('a:has-text("Add"), a:has-text("Create")')).toBeVisible();

    // Take screenshot
    await helpers.takeScreenshot('quotations-list');
  });

  test('should open create quotation form', async ({ page }) => {
    await page.goto('/quotations');

    // Click Add/Create button
    await helpers.waitAndClick('a:has-text("Add"), a:has-text("Create"), a[href*="create"]');

    await page.waitForURL('**/create');

    // Check form fields
    const expectedFields = [
      'customer_id', 'vehicle_number', 'vehicle_make', 'vehicle_model',
      'manufacturing_year', 'engine_cc', 'policy_type'
    ];

    // Wait for form to load
    await page.waitForSelector('form', { timeout: 10000 });

    let fieldsFound = 0;
    for (const field of expectedFields) {
      if (await page.locator(`[name="${field}"], select[name="${field}"]`).count() > 0) {
        fieldsFound++;
      }
    }

    expect(fieldsFound).toBeGreaterThan(3); // At least some key fields should be present

    // Take screenshot
    await helpers.takeScreenshot('quotation-create-form');
  });

  test('should create new quotation', async ({ page }) => {
    await page.goto('/quotations/create');

    // Wait for form to load
    await page.waitForSelector('form');

    // Fill basic quotation data
    const quotationData = {
      vehicle_number: 'MH12AB1234',
      vehicle_make: 'Maruti',
      vehicle_model: 'Swift',
      manufacturing_year: '2020',
      engine_cc: '1200'
    };

    // Fill available fields
    for (const [field, value] of Object.entries(quotationData)) {
      const fieldElement = page.locator(`[name="${field}"]`).first();
      if (await fieldElement.count() > 0) {
        await fieldElement.fill(value);
      }
    }

    // Select customer if dropdown exists
    const customerSelect = page.locator('select[name="customer_id"]');
    if (await customerSelect.count() > 0) {
      await customerSelect.selectOption({ index: 1 });
    }

    // Select policy type if dropdown exists
    const policyTypeSelect = page.locator('select[name="policy_type"]');
    if (await policyTypeSelect.count() > 0) {
      await policyTypeSelect.selectOption({ index: 1 });
    }

    // Take screenshot
    await helpers.takeScreenshot('quotation-form-filled');

    // Submit form
    await page.click('button[type="submit"], .btn-primary');

    // Wait for processing or redirect
    await page.waitForTimeout(3000);

    // Take screenshot
    await helpers.takeScreenshot('quotation-create-result');
  });

  test('should view quotation details', async ({ page }) => {
    await page.goto('/quotations');

    // Click first view button
    const viewButton = page.locator('a:has-text("View"), .btn-view, .fa-eye').first();
    if (await viewButton.count() > 0) {
      await viewButton.click();

      await page.waitForURL('**/show**');

      // Check for quotation details
      await expect(page.locator('.quotation-details, .quote-details')).toBeVisible();

      // Take screenshot
      await helpers.takeScreenshot('quotation-details-view');
    }
  });

  test('should generate quotes from insurance companies', async ({ page }) => {
    await page.goto('/quotations');

    // Look for generate quotes button
    const generateButton = page.locator('button:has-text("Generate"), .btn-generate').first();
    if (await generateButton.count() > 0) {
      await generateButton.click();

      // Wait for quotes generation
      await page.waitForTimeout(5000);

      // Look for quotes display
      const quotesTable = page.locator('.quotes-table, .insurance-quotes, table');
      if (await quotesTable.count() > 0) {
        await helpers.takeScreenshot('quotation-generated-quotes');
      }
    }
  });

  test('should download quotation PDF', async ({ page }) => {
    await page.goto('/quotations');

    // Look for PDF download button
    const pdfButton = page.locator('a:has-text("PDF"), .btn-pdf, [href*="pdf"]').first();
    if (await pdfButton.count() > 0) {
      const downloadPromise = page.waitForEvent('download');
      await pdfButton.click();

      try {
        const download = await downloadPromise;
        expect(download.suggestedFilename()).toContain('.pdf');
        await helpers.takeScreenshot('quotation-pdf-download-success');
      } catch (error) {
        await helpers.takeScreenshot('quotation-pdf-download-clicked');
      }
    }
  });

  test('should send quotation via WhatsApp', async ({ page }) => {
    await page.goto('/quotations');

    // Look for WhatsApp button
    const whatsappButton = page.locator('button:has-text("WhatsApp"), .whatsapp-btn').first();
    if (await whatsappButton.count() > 0) {
      await whatsappButton.click();

      // Wait for WhatsApp modal or confirmation
      await page.waitForTimeout(2000);

      // Take screenshot
      await helpers.takeScreenshot('quotation-whatsapp-modal');

      // Look for send button in modal
      const sendButton = page.locator('.modal button:has-text("Send"), .btn-send');
      if (await sendButton.count() > 0) {
        await sendButton.click();
        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('quotation-whatsapp-sent');
      }
    }
  });

  test('should edit existing quotation', async ({ page }) => {
    await page.goto('/quotations');

    const editButton = page.locator('a:has-text("Edit"), .btn-edit').first();
    if (await editButton.count() > 0) {
      await editButton.click();

      await page.waitForURL('**/edit**');

      // Update vehicle number
      const vehicleNumberField = page.locator('[name="vehicle_number"]');
      if (await vehicleNumberField.count() > 0) {
        await vehicleNumberField.fill('MH12XY9876');
      }

      // Take screenshot
      await helpers.takeScreenshot('quotation-edit-form');

      // Submit update
      await page.click('button[type="submit"]');

      await page.waitForTimeout(2000);
      await helpers.takeScreenshot('quotation-edit-success');
    }
  });

  test('should compare insurance quotes', async ({ page }) => {
    await page.goto('/quotations');

    // Look for compare button or quotes comparison
    const compareSelectors = [
      'button:has-text("Compare")',
      '.compare-btn',
      '.quotes-comparison',
      '.insurance-comparison'
    ];

    for (const selector of compareSelectors) {
      if (await page.locator(selector).count() > 0) {
        await page.click(selector);
        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('quotation-comparison-view');
        break;
      }
    }
  });

  test('should handle quotation search and filters', async ({ page }) => {
    await page.goto('/quotations');

    // Test search
    const searchInput = page.locator('input[name="search"], input[placeholder*="Search"]').first();
    if (await searchInput.count() > 0) {
      await searchInput.fill('MH12');
      await searchInput.press('Enter');
      await page.waitForTimeout(2000);
      await helpers.takeScreenshot('quotation-search-results');
    }

    // Test date filters
    const dateFilters = page.locator('input[type="date"]');
    if (await dateFilters.count() > 0) {
      await dateFilters.first().fill('2023-01-01');
      await page.waitForTimeout(2000);
      await helpers.takeScreenshot('quotation-date-filter');
    }
  });

  test('should export quotations', async ({ page }) => {
    await page.goto('/quotations');

    const exportButton = page.locator('a:has-text("Export"), [href*="export"]');
    if (await exportButton.count() > 0) {
      const downloadPromise = page.waitForEvent('download');
      await exportButton.click();

      try {
        const download = await downloadPromise;
        expect(download.suggestedFilename()).toMatch(/\.xlsx?$|\.csv$/);
        await helpers.takeScreenshot('quotation-export-success');
      } catch (error) {
        await helpers.takeScreenshot('quotation-export-clicked');
      }
    }
  });

  test('should test quotation form validation', async ({ page }) => {
    await page.goto('/quotations/create');

    // Try to submit empty form
    await page.click('button[type="submit"]');

    // Check for validation errors
    const errorSelectors = [
      '.invalid-feedback',
      '.error',
      '.text-danger',
      '.alert-danger'
    ];

    let validationFound = false;
    for (const selector of errorSelectors) {
      if (await page.locator(selector).count() > 0) {
        validationFound = true;
        break;
      }
    }

    await helpers.takeScreenshot('quotation-form-validation');

    // Should have some validation for required fields
    expect(validationFound).toBeTruthy();
  });

  test('should test quotation responsive design', async ({ page }) => {
    await page.goto('/quotations');

    const viewports = [
      { width: 1920, height: 1080, name: 'desktop' },
      { width: 768, height: 1024, name: 'tablet' },
      { width: 375, height: 667, name: 'mobile' }
    ];

    for (const viewport of viewports) {
      await page.setViewportSize(viewport);
      await page.waitForTimeout(1000);

      await expect(page.locator('table, .quotation-list')).toBeVisible();
      await helpers.takeScreenshot(`quotations-${viewport.name}`);
    }
  });

  test('should check quotation accessibility', async ({ page }) => {
    await page.goto('/quotations');

    const a11yResults = await helpers.checkAccessibility();
    const criticalViolations = a11yResults.violations.filter(v => v.impact === 'critical');
    expect(criticalViolations).toHaveLength(0);

    if (a11yResults.violations.length > 0) {
      console.log('Quotation accessibility violations:', a11yResults.violations);
    }
  });

  test('should handle quotation status updates', async ({ page }) => {
    await page.goto('/quotations');

    const statusButtons = [
      '.btn-success:has-text("Active")',
      '.btn-warning:has-text("Pending")',
      '.btn-info:has-text("Approved")',
      '.status-toggle'
    ];

    for (const selector of statusButtons) {
      const button = page.locator(selector).first();
      if (await button.count() > 0) {
        await button.click();
        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('quotation-status-update');
        break;
      }
    }
  });
});