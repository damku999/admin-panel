const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('Visual Regression Testing', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
  });

  // Admin Visual Tests
  test.describe('Admin Interface Visual Tests', () => {
    test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

    test('should capture admin login page visually', async ({ page }) => {
      await page.goto('/login');
      await page.waitForLoadState('networkidle');

      // Take full page screenshot
      await expect(page).toHaveScreenshot('admin-login-page.png', {
        fullPage: true,
        animations: 'disabled'
      });

      // Take viewport screenshot
      await expect(page).toHaveScreenshot('admin-login-viewport.png', {
        animations: 'disabled'
      });
    });

    test('should capture admin dashboard visually', async ({ page }) => {
      await page.goto('/home');
      await page.waitForLoadState('networkidle');

      // Wait for charts/widgets to load
      await page.waitForTimeout(3000);

      await expect(page).toHaveScreenshot('admin-dashboard.png', {
        fullPage: true,
        animations: 'disabled'
      });
    });

    test('should capture customers list page visually', async ({ page }) => {
      await page.goto('/customers');
      await page.waitForLoadState('networkidle');

      await expect(page).toHaveScreenshot('admin-customers-list.png', {
        fullPage: true,
        animations: 'disabled'
      });
    });

    test('should capture customer create form visually', async ({ page }) => {
      await page.goto('/customers/create');
      await page.waitForLoadState('networkidle');

      await expect(page).toHaveScreenshot('admin-customer-create-form.png', {
        fullPage: true,
        animations: 'disabled'
      });
    });

    test('should capture quotations page visually', async ({ page }) => {
      await page.goto('/quotations');
      await page.waitForLoadState('networkidle');

      await expect(page).toHaveScreenshot('admin-quotations-page.png', {
        fullPage: true,
        animations: 'disabled'
      });
    });

    test('should capture claims management visually', async ({ page }) => {
      await page.goto('/insurance-claims');
      await page.waitForLoadState('networkidle');

      await expect(page).toHaveScreenshot('admin-claims-page.png', {
        fullPage: true,
        animations: 'disabled'
      });
    });

    test('should capture reports page visually', async ({ page }) => {
      await page.goto('/reports');
      await page.waitForLoadState('networkidle');

      await expect(page).toHaveScreenshot('admin-reports-page.png', {
        fullPage: true,
        animations: 'disabled'
      });
    });

    test('should capture user management visually', async ({ page }) => {
      await page.goto('/users');
      await page.waitForLoadState('networkidle');

      await expect(page).toHaveScreenshot('admin-users-page.png', {
        fullPage: true,
        animations: 'disabled'
      });
    });
  });

  // Customer Portal Visual Tests
  test.describe('Customer Portal Visual Tests', () => {
    test.use({ storageState: './tests/e2e/playwright/auth/customer.json' });

    test('should capture customer login page visually', async ({ page }) => {
      await page.goto('/customer/login');
      await page.waitForLoadState('networkidle');

      await expect(page).toHaveScreenshot('customer-login-page.png', {
        fullPage: true,
        animations: 'disabled'
      });
    });

    test('should capture customer dashboard visually', async ({ page }) => {
      await page.goto('/customer/dashboard');
      await page.waitForLoadState('networkidle');

      await expect(page).toHaveScreenshot('customer-dashboard.png', {
        fullPage: true,
        animations: 'disabled'
      });
    });

    test('should capture customer policies page visually', async ({ page }) => {
      await page.goto('/customer/policies');
      await page.waitForLoadState('networkidle');

      await expect(page).toHaveScreenshot('customer-policies-page.png', {
        fullPage: true,
        animations: 'disabled'
      });
    });

    test('should capture customer profile page visually', async ({ page }) => {
      await page.goto('/customer/profile');
      await page.waitForLoadState('networkidle');

      await expect(page).toHaveScreenshot('customer-profile-page.png', {
        fullPage: true,
        animations: 'disabled'
      });
    });
  });

  // Responsive Visual Tests
  test.describe('Responsive Visual Tests', () => {
    const devices = [
      { name: 'desktop', width: 1920, height: 1080 },
      { name: 'tablet', width: 768, height: 1024 },
      { name: 'mobile', width: 375, height: 667 }
    ];

    devices.forEach(device => {
      test(`should capture admin login on ${device.name}`, async ({ page }) => {
        await page.setViewportSize({ width: device.width, height: device.height });
        await page.goto('/login');
        await page.waitForLoadState('networkidle');

        await expect(page).toHaveScreenshot(`admin-login-${device.name}.png`, {
          animations: 'disabled'
        });
      });

      test(`should capture customer login on ${device.name}`, async ({ page }) => {
        await page.setViewportSize({ width: device.width, height: device.height });
        await page.goto('/customer/login');
        await page.waitForLoadState('networkidle');

        await expect(page).toHaveScreenshot(`customer-login-${device.name}.png`, {
          animations: 'disabled'
        });
      });
    });
  });

  // Component-Level Visual Tests
  test.describe('Component Visual Tests', () => {
    test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

    test('should capture navigation sidebar visually', async ({ page }) => {
      await page.goto('/home');
      await page.waitForLoadState('networkidle');

      // Capture sidebar component
      const sidebar = page.locator('.sidebar, .nav-sidebar, .main-sidebar');
      if (await sidebar.count() > 0) {
        await expect(sidebar).toHaveScreenshot('admin-sidebar.png');
      }
    });

    test('should capture header navigation visually', async ({ page }) => {
      await page.goto('/home');
      await page.waitForLoadState('networkidle');

      // Capture header component
      const header = page.locator('header, .main-header, .navbar');
      if (await header.count() > 0) {
        await expect(header).toHaveScreenshot('admin-header.png');
      }
    });

    test('should capture data tables visually', async ({ page }) => {
      await page.goto('/customers');
      await page.waitForLoadState('networkidle');

      // Capture table component
      const table = page.locator('table').first();
      if (await table.count() > 0) {
        await expect(table).toHaveScreenshot('admin-data-table.png');
      }
    });

    test('should capture form components visually', async ({ page }) => {
      await page.goto('/customers/create');
      await page.waitForLoadState('networkidle');

      // Capture form
      const form = page.locator('form').first();
      if (await form.count() > 0) {
        await expect(form).toHaveScreenshot('admin-form-component.png');
      }
    });
  });

  // Modal and Dynamic Content Visual Tests
  test.describe('Modal and Dynamic Content Visual Tests', () => {
    test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

    test('should capture modals visually', async ({ page }) => {
      await page.goto('/customers');
      await page.waitForLoadState('networkidle');

      // Look for modal triggers
      const modalTriggers = [
        'button[data-toggle="modal"]',
        'button[data-bs-toggle="modal"]',
        '.modal-trigger',
        'button:has-text("Delete")',
        'button:has-text("Confirm")'
      ];

      for (const selector of modalTriggers) {
        const trigger = page.locator(selector).first();
        if (await trigger.count() > 0) {
          await trigger.click();

          // Wait for modal to appear
          const modal = page.locator('.modal.show, .modal-dialog');
          if (await modal.count() > 0) {
            await modal.waitFor({ state: 'visible' });
            await expect(modal).toHaveScreenshot('admin-modal.png');

            // Close modal
            const closeBtn = page.locator('.modal .btn-close, .modal [data-dismiss="modal"]');
            if (await closeBtn.count() > 0) {
              await closeBtn.click();
            }
            break;
          }
        }
      }
    });

    test('should capture dropdown menus visually', async ({ page }) => {
      await page.goto('/home');
      await page.waitForLoadState('networkidle');

      // Look for dropdown triggers
      const dropdownTriggers = [
        '.dropdown-toggle',
        '[data-toggle="dropdown"]',
        '[data-bs-toggle="dropdown"]',
        '.user-menu'
      ];

      for (const selector of dropdownTriggers) {
        const trigger = page.locator(selector).first();
        if (await trigger.count() > 0) {
          await trigger.click();

          // Wait for dropdown to appear
          await page.waitForTimeout(500);

          const dropdown = page.locator('.dropdown-menu.show');
          if (await dropdown.count() > 0) {
            await expect(dropdown).toHaveScreenshot('admin-dropdown.png');

            // Click elsewhere to close
            await page.click('body');
            break;
          }
        }
      }
    });
  });

  // State-Specific Visual Tests
  test.describe('State-Specific Visual Tests', () => {
    test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

    test('should capture form validation states visually', async ({ page }) => {
      await page.goto('/customers/create');
      await page.waitForLoadState('networkidle');

      // Trigger validation by submitting empty form
      await page.click('button[type="submit"]');
      await page.waitForTimeout(1000);

      await expect(page).toHaveScreenshot('admin-form-validation-errors.png', {
        fullPage: true,
        animations: 'disabled'
      });
    });

    test('should capture loading states visually', async ({ page }) => {
      // Navigate to a page that might show loading
      await page.goto('/reports');
      await page.waitForSelector('form', { timeout: 10000 });

      // Trigger report generation which might show loading
      const generateBtn = page.locator('button:has-text("Generate")');
      if (await generateBtn.count() > 0) {
        await generateBtn.click();

        // Capture loading state if it appears
        const loadingIndicators = [
          '.loading',
          '.spinner',
          '.loading-spinner',
          '.btn:disabled'
        ];

        for (const selector of loadingIndicators) {
          if (await page.locator(selector).count() > 0) {
            await expect(page).toHaveScreenshot('admin-loading-state.png');
            break;
          }
        }
      }
    });

    test('should capture empty states visually', async ({ page }) => {
      // Navigate to a page that might show empty state
      await page.goto('/customers');
      await page.waitForLoadState('networkidle');

      // Look for empty state indicators
      const emptyStateSelectors = [
        '.empty-state',
        '.no-data',
        'text=No records found',
        'text=No customers found'
      ];

      for (const selector of emptyStateSelectors) {
        if (await page.locator(selector).count() > 0) {
          await expect(page).toHaveScreenshot('admin-empty-state.png', {
            fullPage: true
          });
          break;
        }
      }
    });
  });

  // Print Styles Visual Tests
  test.describe('Print Styles Visual Tests', () => {
    test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

    test('should capture print styles for reports', async ({ page }) => {
      await page.goto('/reports');
      await page.waitForLoadState('networkidle');

      // Emulate print media
      await page.emulateMedia({ media: 'print' });

      await expect(page).toHaveScreenshot('admin-reports-print.png', {
        fullPage: true
      });
    });

    test('should capture print styles for customer data', async ({ page }) => {
      await page.goto('/customers');
      await page.waitForLoadState('networkidle');

      await page.emulateMedia({ media: 'print' });

      await expect(page).toHaveScreenshot('admin-customers-print.png', {
        fullPage: true
      });
    });
  });
});