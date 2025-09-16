const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('Customer Authentication', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
  });

  test('should display customer login page', async ({ page }) => {
    await page.goto('/customer/login');

    // Check page title
    await expect(page).toHaveTitle(/Login|Customer/);

    // Check login form elements
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();

    // Check for customer-specific elements
    await expect(page.locator('text=Customer')).toBeVisible();

    // Take screenshot
    await helpers.takeScreenshot('customer-login-page');
  });

  test('should show validation errors for empty login', async ({ page }) => {
    await page.goto('/customer/login');

    await page.click('button[type="submit"]');

    // Check for validation messages
    await helpers.waitForNotification('required');

    await helpers.takeScreenshot('customer-login-validation');
  });

  test('should show error for invalid credentials', async ({ page }) => {
    await page.goto('/customer/login');

    await helpers.fillForm({
      email: 'invalid@customer.com',
      password: 'wrongpassword'
    });

    await page.click('button[type="submit"]');

    // Check for error message
    await helpers.waitForNotification('credentials');

    await helpers.takeScreenshot('customer-login-invalid');
  });

  test('should login successfully as customer', async ({ page }) => {
    await page.goto('/customer/login');

    await helpers.fillForm({
      email: 'testcustomer@example.com',
      password: 'password'
    });

    await page.click('button[type="submit"]');

    // Wait for redirect to customer dashboard
    await page.waitForURL('**/customer/dashboard');

    // Check dashboard elements
    await expect(page.locator('h1, .page-title')).toContainText(/Dashboard|Welcome/);

    await helpers.takeScreenshot('customer-dashboard-after-login');
  });

  test('should logout customer successfully', async ({ page }) => {
    // Login first
    await helpers.loginAsCustomer();

    // Find logout button
    await page.click('.dropdown-toggle, .user-menu, .profile-menu');
    await page.click('a[href*="logout"], button[onclick*="logout"], form[action*="logout"] button');

    // Should redirect to login page
    await page.waitForURL('**/customer/login');

    await helpers.takeScreenshot('customer-after-logout');
  });

  test('should handle password reset flow', async ({ page }) => {
    await page.goto('/customer/password/reset');

    // Check password reset form
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();

    await helpers.takeScreenshot('customer-password-reset-form');

    // Fill and submit
    await helpers.fillForm({ email: 'testcustomer@example.com' });
    await page.click('button[type="submit"]');

    // Check for success message
    await helpers.waitForNotification('email');

    await helpers.takeScreenshot('customer-password-reset-sent');
  });

  test('should handle email verification', async ({ page }) => {
    // Go to email verification notice page
    await page.goto('/customer/email/verify-notice');

    // Should show verification notice
    await expect(page.locator('text=verify', 'text=verification')).toBeVisible();

    // Check for resend button
    const resendButton = page.locator('button:has-text("Resend"), form[action*="resend"]');
    if (await resendButton.count() > 0) {
      await resendButton.click();
      await helpers.waitForNotification('sent');
    }

    await helpers.takeScreenshot('customer-email-verification');
  });

  test('should test responsive design on customer login', async ({ page }) => {
    await page.goto('/customer/login');

    const viewports = [
      { width: 1920, height: 1080, name: 'desktop' },
      { width: 768, height: 1024, name: 'tablet' },
      { width: 375, height: 667, name: 'mobile' }
    ];

    for (const viewport of viewports) {
      await page.setViewportSize(viewport);
      await page.waitForTimeout(1000);

      await expect(page.locator('input[name="email"]')).toBeVisible();
      await expect(page.locator('input[name="password"]')).toBeVisible();
      await expect(page.locator('button[type="submit"]')).toBeVisible();

      await helpers.takeScreenshot(`customer-login-${viewport.name}`);
    }
  });

  test('should check customer login accessibility', async ({ page }) => {
    await page.goto('/customer/login');

    const a11yResults = await helpers.checkAccessibility();

    if (a11yResults.violations.length > 0) {
      console.log('Customer login accessibility violations:', a11yResults.violations);
    }

    const criticalViolations = a11yResults.violations.filter(v => v.impact === 'critical');
    expect(criticalViolations).toHaveLength(0);
  });

  test('should handle session timeout', async ({ page }) => {
    // Login as customer
    await helpers.loginAsCustomer();

    // Navigate to a protected page
    await page.goto('/customer/policies');

    // Should be accessible initially
    await expect(page.locator('h1, .page-title')).toBeVisible();

    // Simulate session timeout by clearing cookies
    await page.context().clearCookies();

    // Try to navigate to another protected page
    await page.goto('/customer/profile');

    // Should redirect to login
    await page.waitForURL('**/customer/login**');

    await helpers.takeScreenshot('customer-session-timeout');
  });

  test('should handle family member access', async ({ page }) => {
    // Login as family head
    await helpers.loginAsCustomer();

    // Look for family member management
    const familyLinks = [
      'a[href*="family-member"]',
      'text=Family',
      '.family-section'
    ];

    for (const selector of familyLinks) {
      if (await page.locator(selector).count() > 0) {
        await page.click(selector);
        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('customer-family-access');
        break;
      }
    }
  });

  test('should handle rate limiting', async ({ page }) => {
    await page.goto('/customer/login');

    // Attempt multiple failed logins to trigger rate limiting
    for (let i = 0; i < 5; i++) {
      await helpers.fillForm({
        email: 'invalid@example.com',
        password: 'wrongpassword'
      });

      await page.click('button[type="submit"]');
      await page.waitForTimeout(1000);
    }

    // Should show rate limiting message
    await helpers.waitForNotification('too many');

    await helpers.takeScreenshot('customer-rate-limited');
  });
});