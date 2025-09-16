const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('User Management', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
  });

  test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

  test('should display users list', async ({ page }) => {
    await page.goto('/users');

    await expect(page.locator('h1, .page-title')).toContainText(/User/);
    await expect(page.locator('table, .users-list')).toBeVisible();
    await expect(page.locator('a:has-text("Add"), button:has-text("Add")')).toBeVisible();

    await helpers.takeScreenshot('users-list');
  });

  test('should create new user', async ({ page }) => {
    await page.goto('/users');

    await helpers.waitAndClick('a:has-text("Add"), a[href*="create"]');
    await page.waitForURL('**/create');

    const userData = {
      name: 'Test User',
      email: `testuser${Date.now()}@example.com`,
      password: 'TestPassword123',
      password_confirmation: 'TestPassword123'
    };

    await helpers.fillForm(userData);

    // Select role if available
    const roleSelect = page.locator('select[name="roles"], select[name="role"]');
    if (await roleSelect.count() > 0) {
      await roleSelect.selectOption({ index: 1 });
    }

    await helpers.takeScreenshot('user-create-form-filled');

    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await helpers.takeScreenshot('user-create-success');
  });

  test('should manage user roles and permissions', async ({ page }) => {
    await page.goto('/users');

    const editButton = page.locator('a:has-text("Edit"), .btn-edit').first();
    if (await editButton.count() > 0) {
      await editButton.click();
      await page.waitForURL('**/edit**');

      // Look for role/permission management
      const roleSelectors = [
        'select[name="roles"]',
        'input[type="checkbox"][name*="permission"]',
        '.roles-section',
        '.permissions-section'
      ];

      let roleManagementFound = false;
      for (const selector of roleSelectors) {
        if (await page.locator(selector).count() > 0) {
          roleManagementFound = true;

          if (selector.includes('checkbox')) {
            await page.locator(selector).first().check();
          } else if (selector.includes('select')) {
            await page.locator(selector).selectOption({ index: 1 });
          }

          await helpers.takeScreenshot('user-role-permissions');
          break;
        }
      }

      expect(roleManagementFound).toBeTruthy();
    }
  });

  test('should display roles management', async ({ page }) => {
    await page.goto('/roles');

    await expect(page.locator('h1, .page-title')).toContainText(/Role/);
    await expect(page.locator('table, .roles-list')).toBeVisible();

    await helpers.takeScreenshot('roles-list');
  });

  test('should display permissions management', async ({ page }) => {
    await page.goto('/permissions');

    await expect(page.locator('h1, .page-title')).toContainText(/Permission/);
    await expect(page.locator('table, .permissions-list')).toBeVisible();

    await helpers.takeScreenshot('permissions-list');
  });

  test('should handle user status updates', async ({ page }) => {
    await page.goto('/users');

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
        await helpers.takeScreenshot('user-status-update');
        break;
      }
    }
  });
});