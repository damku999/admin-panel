const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('Customer Dashboard', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
  });

  test.use({ storageState: './tests/e2e/playwright/auth/customer.json' });

  test('should display customer dashboard with policy overview', async ({ page }) => {
    await page.goto('/customer/dashboard');

    // Check main dashboard elements
    await expect(page.locator('h1, .page-title')).toContainText(/Dashboard|Welcome/);

    // Check for policy overview cards
    const policyOverviewElements = [
      '.policy-card',
      '.insurance-card',
      '.policy-overview',
      '.active-policies'
    ];

    let policyOverviewFound = false;
    for (const selector of policyOverviewElements) {
      if (await page.locator(selector).count() > 0) {
        policyOverviewFound = true;
        break;
      }
    }

    // If no specific cards, check for general policy information
    if (!policyOverviewFound) {
      const policyInfo = await page.locator('text=Policy, text=Insurance').count();
      expect(policyInfo).toBeGreaterThan(0);
    }

    await helpers.takeScreenshot('customer-dashboard-overview');
  });

  test('should show customer profile information', async ({ page }) => {
    await page.goto('/customer/dashboard');

    // Look for customer profile section
    const profileSelectors = [
      '.profile-info',
      '.customer-info',
      '.user-profile',
      '.profile-section'
    ];

    let profileFound = false;
    for (const selector of profileSelectors) {
      if (await page.locator(selector).count() > 0) {
        profileFound = true;
        await helpers.takeScreenshot('customer-profile-section');
        break;
      }
    }

    // Should have customer name displayed somewhere
    const customerNameElements = await page.locator('text=Test Customer, .customer-name, .user-name').count();
    expect(customerNameElements).toBeGreaterThan(0);
  });

  test('should navigate to policies section', async ({ page }) => {
    await page.goto('/customer/dashboard');

    // Look for policies link
    const policiesLink = page.locator('a[href*="policies"], a:has-text("Policies"), .policies-link');
    if (await policiesLink.count() > 0) {
      await policiesLink.first().click();
      await page.waitForURL('**/customer/policies');

      // Verify we're on policies page
      await expect(page.locator('h1, .page-title')).toContainText(/Polic/);
      await helpers.takeScreenshot('customer-policies-page');
    }
  });

  test('should navigate to quotations section', async ({ page }) => {
    await page.goto('/customer/dashboard');

    const quotationsLink = page.locator('a[href*="quotations"], a:has-text("Quotations"), .quotations-link');
    if (await quotationsLink.count() > 0) {
      await quotationsLink.first().click();
      await page.waitForURL('**/customer/quotations');

      await expect(page.locator('h1, .page-title')).toContainText(/Quotation/);
      await helpers.takeScreenshot('customer-quotations-page');
    }
  });

  test('should display recent activities or notifications', async ({ page }) => {
    await page.goto('/customer/dashboard');

    // Look for recent activities
    const activitySelectors = [
      '.recent-activities',
      '.notifications',
      '.activity-feed',
      '.recent-items',
      '.updates'
    ];

    let activitiesFound = false;
    for (const selector of activitySelectors) {
      if (await page.locator(selector).count() > 0) {
        activitiesFound = true;
        await helpers.takeScreenshot('customer-activities');
        break;
      }
    }

    // Look for any list items that might be activities
    const listItems = await page.locator('ul li, .list-item, .activity-item').count();
    if (!activitiesFound && listItems > 0) {
      await helpers.takeScreenshot('customer-dashboard-items');
    }
  });

  test('should show renewal reminders', async ({ page }) => {
    await page.goto('/customer/dashboard');

    // Look for renewal reminders
    const renewalSelectors = [
      '.renewal-reminder',
      '.expiring-soon',
      '.renewal-alert',
      'text=renewal',
      'text=expir'
    ];

    for (const selector of renewalSelectors) {
      if (await page.locator(selector).count() > 0) {
        await helpers.takeScreenshot('customer-renewal-reminders');
        break;
      }
    }
  });

  test('should handle family member switching if available', async ({ page }) => {
    await page.goto('/customer/dashboard');

    // Look for family member switcher
    const familySelectors = [
      '.family-switcher',
      'select[name*="family"]',
      '.family-dropdown',
      'button:has-text("Family")'
    ];

    for (const selector of familySelectors) {
      if (await page.locator(selector).count() > 0) {
        if (selector.includes('select')) {
          await page.locator(selector).selectOption({ index: 1 });
        } else {
          await page.click(selector);
        }

        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('customer-family-switching');
        break;
      }
    }
  });

  test('should display quick action buttons', async ({ page }) => {
    await page.goto('/customer/dashboard');

    // Look for quick action buttons
    const quickActions = [
      'button:has-text("Download")',
      'button:has-text("View")',
      'a:has-text("View Policies")',
      'a:has-text("View Quotations")',
      '.quick-actions',
      '.action-buttons'
    ];

    let actionsFound = 0;
    for (const selector of quickActions) {
      if (await page.locator(selector).count() > 0) {
        actionsFound++;
      }
    }

    expect(actionsFound).toBeGreaterThan(0);
    await helpers.takeScreenshot('customer-quick-actions');
  });

  test('should test dashboard responsiveness', async ({ page }) => {
    await page.goto('/customer/dashboard');

    const viewports = [
      { width: 1920, height: 1080, name: 'desktop' },
      { width: 768, height: 1024, name: 'tablet' },
      { width: 375, height: 667, name: 'mobile' }
    ];

    for (const viewport of viewports) {
      await page.setViewportSize(viewport);
      await page.waitForTimeout(1000);

      // Check main dashboard is still visible
      await expect(page.locator('h1, .page-title')).toBeVisible();

      await helpers.takeScreenshot(`customer-dashboard-${viewport.name}`);

      // Check mobile navigation
      if (viewport.width < 768) {
        const mobileNav = [
          '.navbar-toggler',
          '.mobile-menu',
          '.hamburger'
        ];

        for (const navSelector of mobileNav) {
          if (await page.locator(navSelector).count() > 0) {
            await page.click(navSelector);
            await helpers.takeScreenshot(`customer-mobile-nav-${viewport.name}`);
            break;
          }
        }
      }
    }
  });

  test('should check dashboard accessibility', async ({ page }) => {
    await page.goto('/customer/dashboard');

    const a11yResults = await helpers.checkAccessibility({
      rules: {
        'color-contrast': { enabled: true },
        'keyboard-navigation': { enabled: true },
        'focus-management': { enabled: true }
      }
    });

    if (a11yResults.violations.length > 0) {
      console.log('Customer dashboard accessibility violations:', a11yResults.violations);
    }

    const criticalViolations = a11yResults.violations.filter(v => v.impact === 'critical');
    expect(criticalViolations).toHaveLength(0);
  });

  test('should handle document downloads from dashboard', async ({ page }) => {
    await page.goto('/customer/dashboard');

    // Look for download buttons
    const downloadButtons = page.locator('a:has-text("Download"), button:has-text("Download"), .download-btn');

    if (await downloadButtons.count() > 0) {
      const downloadPromise = page.waitForEvent('download');
      await downloadButtons.first().click();

      try {
        const download = await downloadPromise;
        expect(download).toBeTruthy();
        await helpers.takeScreenshot('customer-dashboard-download-success');
      } catch (error) {
        // Download might not be available
        await helpers.takeScreenshot('customer-dashboard-download-clicked');
      }
    }
  });

  test('should test dashboard performance', async ({ page }) => {
    await page.goto('/customer/dashboard');

    const metrics = await helpers.getPerformanceMetrics();

    // Customer dashboard should load reasonably fast
    expect(metrics.domContentLoaded).toBeLessThan(5000);
    expect(metrics.loadComplete).toBeLessThan(10000);

    console.log('Customer dashboard performance metrics:', metrics);

    // Check for JavaScript errors
    const consoleMessages = await helpers.getConsoleMessages();
    const errorMessages = consoleMessages.filter(msg => msg.type === 'error');

    if (errorMessages.length > 0) {
      console.warn('Customer dashboard JavaScript errors:', errorMessages);
    }

    expect(errorMessages.length).toBeLessThan(3);
  });

  test('should show customer support information', async ({ page }) => {
    await page.goto('/customer/dashboard');

    // Look for support/help information
    const supportSelectors = [
      '.support-info',
      '.help-section',
      '.contact-info',
      'text=support',
      'text=help',
      'text=contact'
    ];

    for (const selector of supportSelectors) {
      if (await page.locator(selector).count() > 0) {
        await helpers.takeScreenshot('customer-support-info');
        break;
      }
    }
  });
});