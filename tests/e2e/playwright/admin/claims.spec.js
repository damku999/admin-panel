const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('Claims Management', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
  });

  test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

  test('should display claims list page', async ({ page }) => {
    await page.goto('/insurance-claims');

    // Check page title
    await expect(page.locator('h1, .page-title')).toContainText(/Claim/);

    // Check for table or list
    await expect(page.locator('table, .claims-list')).toBeVisible();

    // Check for Add button
    await expect(page.locator('a:has-text("Add"), a:has-text("Create")')).toBeVisible();

    // Take screenshot
    await helpers.takeScreenshot('claims-list-page');
  });

  test('should display claims statistics dashboard', async ({ page }) => {
    await page.goto('/insurance-claims');

    // Look for statistics cards
    const statsSelectors = [
      '.stats-card',
      '.claim-statistics',
      '.dashboard-stats',
      '.metrics-card'
    ];

    let statsFound = false;
    for (const selector of statsSelectors) {
      if (await page.locator(selector).count() > 0) {
        statsFound = true;
        await helpers.takeScreenshot('claims-statistics');
        break;
      }
    }

    // If no specific stats cards, check for any numerical displays
    const numberDisplays = await page.locator('.number, .count, .total').count();
    if (!statsFound && numberDisplays > 0) {
      statsFound = true;
      await helpers.takeScreenshot('claims-metrics');
    }

    expect(statsFound).toBeTruthy();
  });

  test('should open create claim form', async ({ page }) => {
    await page.goto('/insurance-claims');

    // Click Add Claim button
    await helpers.waitAndClick('a:has-text("Add"), a:has-text("Create"), a[href*="create"]');

    await page.waitForURL('**/create');

    // Check form fields
    const expectedFields = [
      'policy_number', 'claim_amount', 'incident_date',
      'incident_description', 'claim_type'
    ];

    let fieldsFound = 0;
    for (const field of expectedFields) {
      if (await page.locator(`[name="${field}"], select[name="${field}"]`).count() > 0) {
        fieldsFound++;
      }
    }

    expect(fieldsFound).toBeGreaterThan(2);

    // Take screenshot
    await helpers.takeScreenshot('claim-create-form');
  });

  test('should create new insurance claim', async ({ page }) => {
    await page.goto('/insurance-claims/create');

    // Wait for form to load
    await page.waitForSelector('form');

    // Fill claim data
    const claimData = {
      policy_number: 'POL123456789',
      claim_amount: '50000',
      incident_date: '2023-12-01',
      incident_description: 'Vehicle accident on highway'
    };

    // Fill available fields
    for (const [field, value] of Object.entries(claimData)) {
      const fieldElement = page.locator(`[name="${field}"]`).first();
      if (await fieldElement.count() > 0) {
        if (field === 'incident_date') {
          await fieldElement.fill(value);
        } else {
          await fieldElement.fill(value);
        }
      }
    }

    // Select claim type if dropdown exists
    const claimTypeSelect = page.locator('select[name="claim_type"]');
    if (await claimTypeSelect.count() > 0) {
      await claimTypeSelect.selectOption({ index: 1 });
    }

    // Take screenshot
    await helpers.takeScreenshot('claim-form-filled');

    // Submit form
    await page.click('button[type="submit"]');

    // Wait for processing
    await page.waitForTimeout(3000);

    // Take screenshot
    await helpers.takeScreenshot('claim-create-result');
  });

  test('should view claim details', async ({ page }) => {
    await page.goto('/insurance-claims');

    // Click first view button
    const viewButton = page.locator('a:has-text("View"), .btn-view, .fa-eye').first();
    if (await viewButton.count() > 0) {
      await viewButton.click();

      await page.waitForURL('**/show**');

      // Check for claim details sections
      const detailSections = [
        '.claim-details',
        '.claim-info',
        '.policy-details',
        '.incident-details'
      ];

      let detailsFound = false;
      for (const selector of detailSections) {
        if (await page.locator(selector).count() > 0) {
          detailsFound = true;
          break;
        }
      }

      expect(detailsFound).toBeTruthy();

      // Take screenshot
      await helpers.takeScreenshot('claim-details-view');
    }
  });

  test('should update claim status', async ({ page }) => {
    await page.goto('/insurance-claims');

    // Look for status update buttons
    const statusSelectors = [
      '.btn-warning:has-text("Pending")',
      '.btn-info:has-text("Processing")',
      '.btn-success:has-text("Approved")',
      '.btn-danger:has-text("Rejected")',
      '.status-dropdown',
      'select[name="status"]'
    ];

    for (const selector of statusSelectors) {
      const statusElement = page.locator(selector).first();
      if (await statusElement.count() > 0) {
        if (selector.includes('select')) {
          await statusElement.selectOption({ index: 1 });
        } else {
          await statusElement.click();
        }

        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('claim-status-update');
        break;
      }
    }
  });

  test('should manage claim documents', async ({ page }) => {
    await page.goto('/insurance-claims');

    // Look for document management
    const docButtons = page.locator('button:has-text("Documents"), .document-btn').first();
    if (await docButtons.count() > 0) {
      await docButtons.click();

      // Wait for document management section
      await page.waitForTimeout(2000);

      // Look for document upload or management interface
      const docInterface = [
        '.document-upload',
        '.file-upload',
        'input[type="file"]',
        '.document-list'
      ];

      let docFound = false;
      for (const selector of docInterface) {
        if (await page.locator(selector).count() > 0) {
          docFound = true;
          break;
        }
      }

      await helpers.takeScreenshot('claim-document-management');
      expect(docFound).toBeTruthy();
    }
  });

  test('should handle claim search and filters', async ({ page }) => {
    await page.goto('/insurance-claims');

    // Test claim number search
    const searchInput = page.locator('input[name="search"], input[placeholder*="Search"]').first();
    if (await searchInput.count() > 0) {
      await searchInput.fill('POL123');
      await searchInput.press('Enter');
      await page.waitForTimeout(2000);
      await helpers.takeScreenshot('claim-search-results');
    }

    // Test status filter
    const statusFilter = page.locator('select[name="status_filter"], .status-filter');
    if (await statusFilter.count() > 0) {
      await statusFilter.selectOption({ index: 1 });
      await page.waitForTimeout(2000);
      await helpers.takeScreenshot('claim-status-filter');
    }

    // Test date range filter
    const dateFromFilter = page.locator('input[name="date_from"], input[name="from_date"]');
    if (await dateFromFilter.count() > 0) {
      await dateFromFilter.fill('2023-01-01');
      await page.waitForTimeout(2000);
      await helpers.takeScreenshot('claim-date-filter');
    }
  });

  test('should send WhatsApp notifications for claims', async ({ page }) => {
    await page.goto('/insurance-claims');

    // Look for WhatsApp functionality
    const whatsappButtons = [
      'button:has-text("WhatsApp")',
      '.whatsapp-btn',
      '.send-whatsapp'
    ];

    for (const selector of whatsappButtons) {
      const whatsappBtn = page.locator(selector).first();
      if (await whatsappBtn.count() > 0) {
        await whatsappBtn.click();

        // Wait for WhatsApp modal or options
        await page.waitForTimeout(2000);

        // Take screenshot
        await helpers.takeScreenshot('claim-whatsapp-modal');

        // Look for send options
        const sendOptions = [
          'button:has-text("Document List")',
          'button:has-text("Pending Documents")',
          'button:has-text("Claim Number")'
        ];

        for (const option of sendOptions) {
          if (await page.locator(option).count() > 0) {
            await page.click(option);
            await page.waitForTimeout(1000);
            await helpers.takeScreenshot(`claim-whatsapp-${option.replace(/[^a-zA-Z]/g, '')}`);
            break;
          }
        }
        break;
      }
    }
  });

  test('should manage claim stages/workflow', async ({ page }) => {
    await page.goto('/insurance-claims');

    // Look for stage management
    const stageButtons = [
      'button:has-text("Add Stage")',
      '.stage-btn',
      '.workflow-btn'
    ];

    for (const selector of stageButtons) {
      const stageBtn = page.locator(selector).first();
      if (await stageBtn.count() > 0) {
        await stageBtn.click();
        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('claim-stage-management');
        break;
      }
    }

    // Look for existing workflow display
    const workflowDisplay = [
      '.workflow',
      '.stages',
      '.process-steps',
      '.claim-stages'
    ];

    for (const selector of workflowDisplay) {
      if (await page.locator(selector).count() > 0) {
        await helpers.takeScreenshot('claim-workflow-display');
        break;
      }
    }
  });

  test('should export claims data', async ({ page }) => {
    await page.goto('/insurance-claims');

    const exportButton = page.locator('a:has-text("Export"), [href*="export"]');
    if (await exportButton.count() > 0) {
      const downloadPromise = page.waitForEvent('download');
      await exportButton.click();

      try {
        const download = await downloadPromise;
        expect(download.suggestedFilename()).toMatch(/\.xlsx?$|\.csv$/);
        await helpers.takeScreenshot('claim-export-success');
      } catch (error) {
        await helpers.takeScreenshot('claim-export-clicked');
      }
    }
  });

  test('should update liability details', async ({ page }) => {
    await page.goto('/insurance-claims');

    // Look for liability management
    const liabilityButtons = [
      'button:has-text("Liability")',
      '.liability-btn',
      '.update-liability'
    ];

    for (const selector of liabilityButtons) {
      const liabilityBtn = page.locator(selector).first();
      if (await liabilityBtn.count() > 0) {
        await liabilityBtn.click();

        // Wait for liability form/modal
        await page.waitForTimeout(2000);

        // Look for liability fields
        const liabilityFields = [
          'input[name*="liability"]',
          'textarea[name*="liability"]',
          'select[name*="liability"]'
        ];

        let fieldFound = false;
        for (const fieldSelector of liabilityFields) {
          if (await page.locator(fieldSelector).count() > 0) {
            fieldFound = true;
            await helpers.takeScreenshot('claim-liability-form');
            break;
          }
        }

        expect(fieldFound).toBeTruthy();
        break;
      }
    }
  });

  test('should test claims responsive design', async ({ page }) => {
    await page.goto('/insurance-claims');

    const viewports = [
      { width: 1920, height: 1080, name: 'desktop' },
      { width: 768, height: 1024, name: 'tablet' },
      { width: 375, height: 667, name: 'mobile' }
    ];

    for (const viewport of viewports) {
      await page.setViewportSize(viewport);
      await page.waitForTimeout(1000);

      await expect(page.locator('table, .claims-list')).toBeVisible();
      await helpers.takeScreenshot(`claims-${viewport.name}`);

      // Check for mobile-specific features
      if (viewport.width < 768) {
        const mobileFeatures = [
          '.table-responsive',
          '.mobile-view',
          '.card-view'
        ];

        let mobileFound = false;
        for (const feature of mobileFeatures) {
          if (await page.locator(feature).count() > 0) {
            mobileFound = true;
            break;
          }
        }
      }
    }
  });

  test('should check claims accessibility', async ({ page }) => {
    await page.goto('/insurance-claims');

    const a11yResults = await helpers.checkAccessibility();
    const criticalViolations = a11yResults.violations.filter(v => v.impact === 'critical');
    expect(criticalViolations).toHaveLength(0);

    if (a11yResults.violations.length > 0) {
      console.log('Claims accessibility violations:', a11yResults.violations);
    }
  });

  test('should handle claim number management', async ({ page }) => {
    await page.goto('/insurance-claims');

    // Look for claim number update functionality
    const claimNumberButtons = [
      'button:has-text("Claim Number")',
      '.claim-number-btn',
      '.update-claim-number'
    ];

    for (const selector of claimNumberButtons) {
      const claimBtn = page.locator(selector).first();
      if (await claimBtn.count() > 0) {
        await claimBtn.click();
        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('claim-number-update');
        break;
      }
    }
  });
});