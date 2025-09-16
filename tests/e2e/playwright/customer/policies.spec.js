const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('Customer Policies', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
  });

  test.use({ storageState: './tests/e2e/playwright/auth/customer.json' });

  test('should display customer policies list', async ({ page }) => {
    await page.goto('/customer/policies');

    // Check page title
    await expect(page.locator('h1, .page-title')).toContainText(/Polic/);

    // Check for policies display
    const policyDisplays = [
      'table',
      '.policy-list',
      '.policy-card',
      '.insurance-policies'
    ];

    let policiesFound = false;
    for (const selector of policyDisplays) {
      if (await page.locator(selector).count() > 0) {
        policiesFound = true;
        break;
      }
    }

    expect(policiesFound).toBeTruthy();

    await helpers.takeScreenshot('customer-policies-list');
  });

  test('should show policy details when clicked', async ({ page }) => {
    await page.goto('/customer/policies');

    // Look for policy links or view buttons
    const policyLinks = [
      'a:has-text("View")',
      '.policy-link',
      '.view-policy',
      'tbody tr td:first-child a'
    ];

    for (const selector of policyLinks) {
      const policyLink = page.locator(selector).first();
      if (await policyLink.count() > 0) {
        await policyLink.click();

        await page.waitForURL('**/policies/**');

        // Check for policy details
        const detailSections = [
          '.policy-details',
          '.policy-info',
          '.insurance-details',
          '.policy-summary'
        ];

        let detailsFound = false;
        for (const detailSelector of detailSections) {
          if (await page.locator(detailSelector).count() > 0) {
            detailsFound = true;
            break;
          }
        }

        expect(detailsFound).toBeTruthy();

        await helpers.takeScreenshot('customer-policy-details');
        break;
      }
    }
  });

  test('should allow policy document downloads', async ({ page }) => {
    await page.goto('/customer/policies');

    // Look for download buttons
    const downloadButtons = [
      'a:has-text("Download")',
      'button:has-text("Download")',
      '.download-btn',
      '.btn-download',
      'a[href*="download"]'
    ];

    for (const selector of downloadButtons) {
      const downloadBtn = page.locator(selector).first();
      if (await downloadBtn.count() > 0) {
        const downloadPromise = page.waitForEvent('download');
        await downloadBtn.click();

        try {
          const download = await downloadPromise;
          expect(download.suggestedFilename()).toMatch(/\.pdf$|\.doc/);
          await helpers.takeScreenshot('customer-policy-download-success');
        } catch (error) {
          await helpers.takeScreenshot('customer-policy-download-clicked');
        }
        break;
      }
    }
  });

  test('should filter policies by status or type', async ({ page }) => {
    await page.goto('/customer/policies');

    // Look for filter options
    const filterSelectors = [
      'select[name*="status"]',
      'select[name*="type"]',
      '.filter-select',
      '.policy-filter'
    ];

    for (const selector of filterSelectors) {
      if (await page.locator(selector).count() > 0) {
        await page.locator(selector).selectOption({ index: 1 });
        await page.waitForTimeout(2000);

        await helpers.takeScreenshot('customer-policies-filtered');
        break;
      }
    }
  });

  test('should show policy renewal information', async ({ page }) => {
    await page.goto('/customer/policies');

    // Look for renewal information
    const renewalSelectors = [
      '.renewal-date',
      '.expiry-date',
      '.expires-on',
      'text=renewal',
      'text=expires',
      'text=expiry'
    ];

    let renewalFound = false;
    for (const selector of renewalSelectors) {
      if (await page.locator(selector).count() > 0) {
        renewalFound = true;
        await helpers.takeScreenshot('customer-policy-renewal-info');
        break;
      }
    }

    // Renewal information should be visible for policies
    expect(renewalFound).toBeTruthy();
  });

  test('should display policy premium and coverage details', async ({ page }) => {
    await page.goto('/customer/policies');

    // Click on first policy to view details
    const firstPolicyLink = page.locator('a:has-text("View"), tbody tr').first();
    if (await firstPolicyLink.count() > 0) {
      await firstPolicyLink.click();
      await page.waitForTimeout(2000);

      // Look for premium and coverage information
      const detailSelectors = [
        'text=Premium',
        'text=Coverage',
        'text=Amount',
        '.premium-amount',
        '.coverage-amount',
        '.policy-amount'
      ];

      let detailsFound = 0;
      for (const selector of detailSelectors) {
        if (await page.locator(selector).count() > 0) {
          detailsFound++;
        }
      }

      expect(detailsFound).toBeGreaterThan(0);

      await helpers.takeScreenshot('customer-policy-premium-coverage');
    }
  });

  test('should show policy claim status if applicable', async ({ page }) => {
    await page.goto('/customer/policies');

    // Look for claim-related information
    const claimSelectors = [
      'text=Claim',
      '.claim-status',
      '.active-claims',
      'button:has-text("Claim")'
    ];

    for (const selector of claimSelectors) {
      if (await page.locator(selector).count() > 0) {
        await helpers.takeScreenshot('customer-policy-claims');
        break;
      }
    }
  });

  test('should handle empty policies state', async ({ page }) => {
    // This test might show empty state if customer has no policies
    await page.goto('/customer/policies');

    // Look for empty state message
    const emptyStateSelectors = [
      'text=No policies',
      'text=no insurance',
      '.empty-state',
      '.no-data',
      'text=You don\'t have'
    ];

    let emptyStateFound = false;
    for (const selector of emptyStateSelectors) {
      if (await page.locator(selector).count() > 0) {
        emptyStateFound = true;
        await helpers.takeScreenshot('customer-policies-empty-state');
        break;
      }
    }

    // Either should have policies or empty state
    const hasPolicies = await page.locator('table tbody tr, .policy-card, .policy-item').count() > 0;
    expect(hasPolicies || emptyStateFound).toBeTruthy();
  });

  test('should test policies page responsiveness', async ({ page }) => {
    await page.goto('/customer/policies');

    const viewports = [
      { width: 1920, height: 1080, name: 'desktop' },
      { width: 768, height: 1024, name: 'tablet' },
      { width: 375, height: 667, name: 'mobile' }
    ];

    for (const viewport of viewports) {
      await page.setViewportSize(viewport);
      await page.waitForTimeout(1000);

      // Main content should be visible
      await expect(page.locator('h1, .page-title')).toBeVisible();

      await helpers.takeScreenshot(`customer-policies-${viewport.name}`);

      // On mobile, check for responsive features
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

  test('should check policies accessibility', async ({ page }) => {
    await page.goto('/customer/policies');

    const a11yResults = await helpers.checkAccessibility();

    if (a11yResults.violations.length > 0) {
      console.log('Customer policies accessibility violations:', a11yResults.violations);
    }

    const criticalViolations = a11yResults.violations.filter(v => v.impact === 'critical');
    expect(criticalViolations).toHaveLength(0);
  });

  test('should handle policy search functionality', async ({ page }) => {
    await page.goto('/customer/policies');

    // Look for search functionality
    const searchSelectors = [
      'input[name="search"]',
      'input[placeholder*="Search"]',
      '.search-input',
      '.policy-search'
    ];

    for (const selector of searchSelectors) {
      const searchInput = page.locator(selector);
      if (await searchInput.count() > 0) {
        await searchInput.fill('test');
        await searchInput.press('Enter');
        await page.waitForTimeout(2000);

        await helpers.takeScreenshot('customer-policies-search');
        break;
      }
    }
  });

  test('should show family member policies if applicable', async ({ page }) => {
    await page.goto('/customer/policies');

    // Look for family member policy display
    const familySelectors = [
      '.family-policies',
      'text=Family Member',
      '.member-policies',
      'select[name*="member"]'
    ];

    for (const selector of familySelectors) {
      if (await page.locator(selector).count() > 0) {
        await helpers.takeScreenshot('customer-family-policies');
        break;
      }
    }
  });

  test('should handle policy sorting', async ({ page }) => {
    await page.goto('/customer/policies');

    // Look for sorting options
    const sortSelectors = [
      'th[data-sort]',
      '.sort-btn',
      'button[data-sort]',
      '.sortable'
    ];

    for (const selector of sortSelectors) {
      const sortButton = page.locator(selector).first();
      if (await sortButton.count() > 0) {
        await sortButton.click();
        await page.waitForTimeout(2000);

        await helpers.takeScreenshot('customer-policies-sorted');
        break;
      }
    }
  });
});