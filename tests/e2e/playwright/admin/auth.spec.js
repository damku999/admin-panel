const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('Admin Authentication', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
  });

  test('should display login page correctly', async ({ page }) => {
    await page.goto('/login');

    // Check page title
    await expect(page).toHaveTitle(/Login/);

    // Check login form elements
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();

    // Take screenshot for visual regression
    await helpers.takeScreenshot('admin-login-page');

    // Check accessibility
    const a11yResults = await helpers.checkAccessibility();
    expect(a11yResults.violations).toHaveLength(0);
  });

  test('should show validation errors for empty login', async ({ page }) => {
    await page.goto('/login');

    // Try to submit empty form
    await page.click('button[type="submit"]');

    // Check for validation messages
    await helpers.waitForNotification('required');

    // Take screenshot
    await helpers.takeScreenshot('admin-login-validation-errors');
  });

  test('should show error for invalid credentials', async ({ page }) => {
    await page.goto('/login');

    // Fill with invalid credentials
    await helpers.fillForm({
      email: 'invalid@example.com',
      password: 'wrongpassword'
    });

    await page.click('button[type="submit"]');

    // Check for error message
    await helpers.waitForNotification('credentials');

    // Take screenshot
    await helpers.takeScreenshot('admin-login-invalid-credentials');
  });

  test('should login successfully with valid credentials', async ({ page }) => {
    await page.goto('/login');

    // Fill with valid admin credentials
    await helpers.fillForm({
      email: 'admin@admin.com',
      password: 'Admin@123#'
    });

    await page.click('button[type="submit"]');

    // Wait for redirect to dashboard
    await page.waitForURL('**/home');

    // Check that we're on the dashboard
    await expect(page.locator('h1, .page-title')).toContainText(/Dashboard|Home/);

    // Take screenshot
    await helpers.takeScreenshot('admin-dashboard-after-login');
  });

  test('should logout successfully', async ({ page }) => {
    // Login first
    await helpers.loginAsAdmin();

    // Find and click logout button/link
    await page.click('.dropdown-toggle, .user-menu'); // Open user menu
    await page.click('a[href*="logout"], button[onclick*="logout"]');

    // Should redirect to login page
    await page.waitForURL('**/login');

    // Take screenshot
    await helpers.takeScreenshot('admin-after-logout');
  });

  test('should handle password reset flow', async ({ page }) => {
    await page.goto('/password/reset');

    // Check password reset form
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();

    // Take screenshot
    await helpers.takeScreenshot('admin-password-reset-form');

    // Fill email and submit
    await helpers.fillForm({ email: 'admin@admin.com' });
    await page.click('button[type="submit"]');

    // Check for success message
    await helpers.waitForNotification('email');

    // Take screenshot
    await helpers.takeScreenshot('admin-password-reset-sent');
  });

  test('should test responsive design on login page', async ({ page }) => {
    await page.goto('/login');

    const screenshots = await helpers.testResponsiveDesign();

    // Verify login form is accessible on all screen sizes
    for (const viewport of ['desktop-large', 'tablet', 'mobile']) {
      await page.setViewportSize(
        viewport === 'mobile' ? { width: 375, height: 667 } :
        viewport === 'tablet' ? { width: 768, height: 1024 } :
        { width: 1920, height: 1080 }
      );

      await expect(page.locator('input[name="email"]')).toBeVisible();
      await expect(page.locator('input[name="password"]')).toBeVisible();
      await expect(page.locator('button[type="submit"]')).toBeVisible();
    }
  });

  test('should check for performance issues on login', async ({ page }) => {
    await page.goto('/login');

    const metrics = await helpers.getPerformanceMetrics();

    // Assertions for performance
    expect(metrics.domContentLoaded).toBeLessThan(3000); // 3 seconds
    expect(metrics.loadComplete).toBeLessThan(5000); // 5 seconds

    console.log('Login page performance metrics:', metrics);
  });
});