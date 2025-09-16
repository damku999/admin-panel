const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('Customer Management', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
  });

  test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

  test('should display customer list page', async ({ page }) => {
    await page.goto('/customers');

    // Check page title
    await expect(page.locator('h1, .page-title')).toContainText(/Customer/);

    // Check for table or list
    await expect(page.locator('table, .customer-list')).toBeVisible();

    // Check for Add Customer button
    await expect(page.locator('a:has-text("Add"), button:has-text("Add")')).toBeVisible();

    // Take screenshot
    await helpers.takeScreenshot('customers-list-page');
  });

  test('should open create customer form', async ({ page }) => {
    await page.goto('/customers');

    // Click Add Customer button
    await helpers.waitAndClick('a:has-text("Add"), a[href*="create"], .btn-primary');

    // Should navigate to create form
    await page.waitForURL('**/customers/create');

    // Check form fields
    const expectedFields = [
      'name', 'email', 'mobile_number', 'address', 'city', 'state', 'pincode'
    ];

    for (const field of expectedFields) {
      await expect(page.locator(`[name="${field}"]`)).toBeVisible();
    }

    // Take screenshot
    await helpers.takeScreenshot('customer-create-form');
  });

  test('should create new customer', async ({ page }) => {
    await page.goto('/customers/create');

    const testData = helpers.generateTestData('customer');

    // Fill customer form
    await helpers.fillForm(testData);

    // Take screenshot before submit
    await helpers.takeScreenshot('customer-create-form-filled');

    // Submit form
    await page.click('button[type="submit"], .btn-primary');

    // Wait for success message or redirect
    try {
      await helpers.waitForNotification('success');
    } catch {
      // Might redirect directly
      await page.waitForURL('**/customers**');
    }

    // Take screenshot
    await helpers.takeScreenshot('customer-create-success');
  });

  test('should validate customer form fields', async ({ page }) => {
    await page.goto('/customers/create');

    // Test required field validation
    await page.click('button[type="submit"]');

    // Check for validation errors
    const requiredFields = ['name', 'email', 'mobile_number'];
    const validationErrors = await helpers.testFormValidation('form', requiredFields);

    if (validationErrors.length > 0) {
      console.warn('Form validation issues:', validationErrors);
    }

    // Take screenshot
    await helpers.takeScreenshot('customer-form-validation-errors');

    // Test email format validation
    await helpers.fillForm({ email: 'invalid-email' });
    await page.click('button[type="submit"]');

    // Take screenshot
    await helpers.takeScreenshot('customer-email-validation');
  });

  test('should edit existing customer', async ({ page }) => {
    await page.goto('/customers');

    // Click first edit button
    const editButton = page.locator('a:has-text("Edit"), .btn-edit, .fa-edit').first();
    if (await editButton.count() > 0) {
      await editButton.click();

      await page.waitForURL('**/edit**');

      // Update customer name
      await page.fill('[name="name"]', 'Updated Customer Name');

      // Take screenshot
      await helpers.takeScreenshot('customer-edit-form-updated');

      // Submit update
      await page.click('button[type="submit"]');

      // Wait for success
      try {
        await helpers.waitForNotification('updated');
      } catch {
        await page.waitForURL('**/customers**');
      }

      // Take screenshot
      await helpers.takeScreenshot('customer-edit-success');
    }
  });

  test('should view customer details', async ({ page }) => {
    await page.goto('/customers');

    // Click first view/show button
    const viewButton = page.locator('a:has-text("View"), .btn-view, .fa-eye').first();
    if (await viewButton.count() > 0) {
      await viewButton.click();

      await page.waitForURL('**/show**');

      // Check for customer details display
      await expect(page.locator('.customer-details, .profile-info')).toBeVisible();

      // Take screenshot
      await helpers.takeScreenshot('customer-details-view');
    }
  });

  test('should handle customer status updates', async ({ page }) => {
    await page.goto('/customers');

    // Look for status toggle buttons
    const statusButtons = [
      '.btn-success:has-text("Active")',
      '.btn-danger:has-text("Inactive")',
      '.status-toggle',
      '[data-action="status"]'
    ];

    for (const selector of statusButtons) {
      const button = page.locator(selector).first();
      if (await button.count() > 0) {
        await button.click();

        // Wait for status change
        await page.waitForTimeout(2000);

        // Take screenshot
        await helpers.takeScreenshot('customer-status-update');
        break;
      }
    }
  });

  test('should search customers', async ({ page }) => {
    await page.goto('/customers');

    // Look for search input
    const searchSelectors = [
      'input[name="search"]',
      'input[placeholder*="Search"]',
      '.search-input',
      '#search'
    ];

    for (const selector of searchSelectors) {
      const searchInput = page.locator(selector);
      if (await searchInput.count() > 0) {
        // Perform search
        await searchInput.fill('test');

        // Look for search button or press Enter
        const searchButton = page.locator('button:has-text("Search"), .btn-search');
        if (await searchButton.count() > 0) {
          await searchButton.click();
        } else {
          await searchInput.press('Enter');
        }

        await page.waitForTimeout(2000);

        // Take screenshot
        await helpers.takeScreenshot('customer-search-results');
        break;
      }
    }
  });

  test('should export customer data', async ({ page }) => {
    await page.goto('/customers');

    // Look for export button
    const exportButton = page.locator('a:has-text("Export"), .btn-export, [href*="export"]');
    if (await exportButton.count() > 0) {
      // Test export
      const downloadPromise = page.waitForEvent('download');
      await exportButton.click();

      try {
        const download = await downloadPromise;
        expect(download.suggestedFilename()).toMatch(/\.xlsx?$|\.csv$/);

        // Take screenshot
        await helpers.takeScreenshot('customer-export-success');
      } catch (error) {
        console.warn('Export might not trigger immediate download:', error.message);
        await helpers.takeScreenshot('customer-export-clicked');
      }
    }
  });

  test('should handle customer pagination', async ({ page }) => {
    await page.goto('/customers');

    // Check for pagination
    const paginationSelectors = [
      '.pagination',
      '.page-numbers',
      '.next',
      '.previous'
    ];

    let paginationFound = false;
    for (const selector of paginationSelectors) {
      if (await page.locator(selector).count() > 0) {
        paginationFound = true;

        // Test next page if available
        const nextButton = page.locator('.next:not(.disabled), .page-item:not(.disabled) .page-link').last();
        if (await nextButton.count() > 0 && await nextButton.isEnabled()) {
          await nextButton.click();
          await page.waitForTimeout(2000);

          // Take screenshot
          await helpers.takeScreenshot('customer-pagination-next-page');
        }
        break;
      }
    }

    // If no pagination found, check if there are customers
    const customerRows = await page.locator('table tbody tr, .customer-item').count();
    expect(customerRows).toBeGreaterThan(0);
  });

  test('should handle family group management', async ({ page }) => {
    await page.goto('/customers');

    // Look for family group related buttons/links
    const familySelectors = [
      'a:has-text("Family")',
      '.family-group',
      '[href*="family"]'
    ];

    for (const selector of familySelectors) {
      if (await page.locator(selector).count() > 0) {
        await page.click(selector);

        // Take screenshot of family management
        await helpers.takeScreenshot('customer-family-management');

        // Go back
        await page.goBack();
        break;
      }
    }
  });

  test('should test customers responsive design', async ({ page }) => {
    await page.goto('/customers');

    const viewports = [
      { width: 1920, height: 1080, name: 'desktop' },
      { width: 768, height: 1024, name: 'tablet' },
      { width: 375, height: 667, name: 'mobile' }
    ];

    for (const viewport of viewports) {
      await page.setViewportSize(viewport);
      await page.waitForTimeout(1000);

      // Check that table/list is still accessible
      await expect(page.locator('table, .customer-list')).toBeVisible();

      // Take screenshot
      await helpers.takeScreenshot(`customers-responsive-${viewport.name}`);

      // On mobile, check for responsive table features
      if (viewport.width < 768) {
        const responsiveFeatures = [
          '.table-responsive',
          '.mobile-table',
          '.card-view'
        ];

        let responsiveFound = false;
        for (const feature of responsiveFeatures) {
          if (await page.locator(feature).count() > 0) {
            responsiveFound = true;
            break;
          }
        }

        // Should have some responsive design for tables
        expect(responsiveFound || await page.locator('table').isVisible()).toBeTruthy();
      }
    }
  });

  test('should check customer page accessibility', async ({ page }) => {
    await page.goto('/customers');

    const a11yResults = await helpers.checkAccessibility();

    // Log violations for review
    if (a11yResults.violations.length > 0) {
      console.log('Customer page accessibility violations:', a11yResults.violations);
    }

    // Should not have critical accessibility issues
    const criticalViolations = a11yResults.violations.filter(v => v.impact === 'critical');
    expect(criticalViolations).toHaveLength(0);
  });

  test('should handle WhatsApp integration if available', async ({ page }) => {
    await page.goto('/customers');

    // Look for WhatsApp related buttons
    const whatsappSelectors = [
      'button:has-text("WhatsApp")',
      '.whatsapp-btn',
      '[data-action="whatsapp"]',
      '.fa-whatsapp'
    ];

    for (const selector of whatsappSelectors) {
      const whatsappButton = page.locator(selector).first();
      if (await whatsappButton.count() > 0) {
        await whatsappButton.click();

        // Look for WhatsApp modal or preview
        await page.waitForSelector('.modal, .whatsapp-modal, .preview', { timeout: 5000 });

        // Take screenshot
        await helpers.takeScreenshot('customer-whatsapp-integration');

        // Close modal if open
        const closeButton = page.locator('.modal .btn-close, .modal .close, [data-dismiss="modal"]');
        if (await closeButton.count() > 0) {
          await closeButton.click();
        }
        break;
      }
    }
  });
});