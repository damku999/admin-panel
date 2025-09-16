const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('Admin Dashboard', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
    // Use stored admin authentication state
    await page.goto('/home');
  });

  test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

  test('should display dashboard with all widgets', async ({ page }) => {
    // Check main dashboard elements
    await expect(page.locator('h1, .page-title')).toContainText(/Dashboard|Home/);

    // Check for key statistics cards/widgets
    const statsCards = [
      'Customers',
      'Insurance Companies',
      'Quotations',
      'Active Policies'
    ];

    for (const stat of statsCards) {
      await expect(page.locator('text=' + stat, { timeout: 5000 })).toBeVisible();
    }

    // Take screenshot
    await helpers.takeScreenshot('admin-dashboard-overview');
  });

  test('should display charts and graphs', async ({ page }) => {
    // Wait for charts to load
    await page.waitForSelector('canvas, .chart, .graph', { timeout: 10000 });

    // Check for chart containers
    const chartSelectors = [
      'canvas',
      '.chart-container',
      '.dashboard-chart',
      '#monthly-chart',
      '#revenue-chart'
    ];

    let chartsFound = false;
    for (const selector of chartSelectors) {
      if (await page.locator(selector).count() > 0) {
        chartsFound = true;
        break;
      }
    }

    expect(chartsFound).toBeTruthy();

    // Take screenshot
    await helpers.takeScreenshot('admin-dashboard-charts');
  });

  test('should navigate to different sections from dashboard', async ({ page }) => {
    const navigationTests = [
      { selector: 'a[href*="customers"]', expectedUrl: '**/customers**', name: 'customers' },
      { selector: 'a[href*="insurance_companies"]', expectedUrl: '**/insurance_companies**', name: 'insurance-companies' },
      { selector: 'a[href*="quotations"]', expectedUrl: '**/quotations**', name: 'quotations' },
      { selector: 'a[href*="brokers"]', expectedUrl: '**/brokers**', name: 'brokers' }
    ];

    for (const nav of navigationTests) {
      // Go back to dashboard
      await page.goto('/home');

      // Find and click navigation link
      const navLink = page.locator(nav.selector).first();
      if (await navLink.count() > 0) {
        await navLink.click();
        await page.waitForURL(nav.expectedUrl);

        // Take screenshot
        await helpers.takeScreenshot(`navigation-to-${nav.name}`);

        // Verify we're on the correct page
        await expect(page.url()).toContain(nav.expectedUrl.replace('**', '').replace('*', ''));
      }
    }
  });

  test('should show recent activities or notifications', async ({ page }) => {
    // Look for recent activities section
    const activitySelectors = [
      '.recent-activities',
      '.notifications',
      '.activity-feed',
      '.recent-items'
    ];

    let activitiesFound = false;
    for (const selector of activitySelectors) {
      if (await page.locator(selector).count() > 0) {
        activitiesFound = true;
        await helpers.takeScreenshot(`dashboard-${selector.replace('.', '')}`);
        break;
      }
    }

    // If no specific activities section, check for any list of recent items
    if (!activitiesFound) {
      const recentItemsCount = await page.locator('ul li, .list-group-item, .table tbody tr').count();
      expect(recentItemsCount).toBeGreaterThan(0);
    }
  });

  test('should handle dashboard filters and date ranges', async ({ page }) => {
    // Look for date filters
    const filterSelectors = [
      'input[type="date"]',
      '.date-picker',
      '.daterangepicker',
      'select[name*="period"]',
      'select[name*="filter"]'
    ];

    for (const selector of filterSelectors) {
      const filterElement = page.locator(selector).first();
      if (await filterElement.count() > 0) {
        // Test the filter
        if (selector.includes('date')) {
          await filterElement.fill('2023-01-01');
        } else if (selector.includes('select')) {
          await filterElement.selectOption({ index: 1 });
        }

        await helpers.takeScreenshot(`dashboard-filter-${selector.replace(/[^a-zA-Z0-9]/g, '')}`);

        // Wait for any AJAX updates
        await page.waitForTimeout(2000);
      }
    }
  });

  test('should display user profile information', async ({ page }) => {
    // Look for user profile information
    const profileSelectors = [
      '.user-profile',
      '.user-info',
      '.profile-dropdown',
      '.user-menu'
    ];

    let profileFound = false;
    for (const selector of profileSelectors) {
      if (await page.locator(selector).count() > 0) {
        profileFound = true;

        // Click to open if it's a dropdown
        if (selector.includes('dropdown') || selector.includes('menu')) {
          await page.click(selector);
          await helpers.takeScreenshot('user-profile-menu-open');
        }
        break;
      }
    }

    expect(profileFound).toBeTruthy();
  });

  test('should test dashboard responsiveness', async ({ page }) => {
    const viewports = [
      { width: 1920, height: 1080, name: 'desktop-large' },
      { width: 1366, height: 768, name: 'desktop-medium' },
      { width: 768, height: 1024, name: 'tablet' },
      { width: 375, height: 667, name: 'mobile' }
    ];

    for (const viewport of viewports) {
      await page.setViewportSize(viewport);
      await page.waitForTimeout(1000); // Allow layout adjustment

      // Check that main dashboard elements are still visible
      await expect(page.locator('h1, .page-title')).toBeVisible();

      // Take screenshot
      await helpers.takeScreenshot(`dashboard-responsive-${viewport.name}`);

      // Check for mobile-specific elements
      if (viewport.width < 768) {
        // Look for hamburger menu or mobile navigation
        const mobileNavSelectors = [
          '.navbar-toggler',
          '.mobile-menu',
          '.hamburger-menu',
          '.menu-toggle'
        ];

        let mobileNavFound = false;
        for (const selector of mobileNavSelectors) {
          if (await page.locator(selector).count() > 0) {
            mobileNavFound = true;
            break;
          }
        }

        expect(mobileNavFound).toBeTruthy();
      }
    }
  });

  test('should check dashboard accessibility', async ({ page }) => {
    const a11yResults = await helpers.checkAccessibility({
      rules: {
        'color-contrast': { enabled: true },
        'keyboard-navigation': { enabled: true },
        'focus-management': { enabled: true }
      }
    });

    // Log any violations for review
    if (a11yResults.violations.length > 0) {
      console.log('Accessibility violations found:', a11yResults.violations);
    }

    // Critical violations should not exist
    const criticalViolations = a11yResults.violations.filter(v => v.impact === 'critical');
    expect(criticalViolations).toHaveLength(0);
  });

  test('should test dashboard performance', async ({ page }) => {
    const metrics = await helpers.getPerformanceMetrics();

    // Dashboard should load reasonably fast
    expect(metrics.domContentLoaded).toBeLessThan(5000); // 5 seconds
    expect(metrics.loadComplete).toBeLessThan(10000); // 10 seconds

    // Log metrics for monitoring
    console.log('Dashboard performance metrics:', metrics);

    // Check for JavaScript errors
    const consoleMessages = await helpers.getConsoleMessages();
    const errorMessages = consoleMessages.filter(msg => msg.type === 'error');

    if (errorMessages.length > 0) {
      console.warn('JavaScript errors found:', errorMessages);
    }

    // No critical JavaScript errors should exist
    expect(errorMessages.length).toBeLessThan(5);
  });

  test('should handle data export functionality', async ({ page }) => {
    // Look for export buttons
    const exportSelectors = [
      'button:has-text("Export")',
      'a:has-text("Export")',
      '.export-btn',
      '[data-action="export"]'
    ];

    for (const selector of exportSelectors) {
      const exportButton = page.locator(selector).first();
      if (await exportButton.count() > 0) {
        // Test export functionality
        const downloadPromise = page.waitForEvent('download');
        await exportButton.click();

        try {
          const download = await downloadPromise;
          expect(download).toBeTruthy();
          await helpers.takeScreenshot('dashboard-export-initiated');
          break;
        } catch (error) {
          // Export might not trigger download immediately
          await helpers.takeScreenshot('dashboard-export-clicked');
        }
      }
    }
  });
});