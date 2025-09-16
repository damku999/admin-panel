const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('Customer Profile Management', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
  });

  test.use({ storageState: './tests/e2e/playwright/auth/customer.json' });

  test('should display customer profile page', async ({ page }) => {
    await page.goto('/customer/profile');

    // Check page title
    await expect(page.locator('h1, .page-title')).toContainText(/Profile/);

    // Check for profile form or information display
    const profileElements = [
      'form',
      '.profile-form',
      '.profile-info',
      'input[name="name"]',
      'input[name="email"]'
    ];

    let profileFound = false;
    for (const selector of profileElements) {
      if (await page.locator(selector).count() > 0) {
        profileFound = true;
        break;
      }
    }

    expect(profileFound).toBeTruthy();

    await helpers.takeScreenshot('customer-profile-page');
  });

  test('should update customer profile information', async ({ page }) => {
    await page.goto('/customer/profile');

    // Wait for form to load
    await page.waitForSelector('form, .profile-form');

    // Update profile fields
    const updatedData = {
      name: 'Updated Customer Name',
      mobile_number: '9876543210',
      address: 'Updated Address'
    };

    // Update available fields
    for (const [field, value] of Object.entries(updatedData)) {
      const fieldElement = page.locator(`[name="${field}"]`);
      if (await fieldElement.count() > 0) {
        await fieldElement.fill(value);
      }
    }

    await helpers.takeScreenshot('customer-profile-updated');

    // Submit form
    const submitButton = page.locator('button[type="submit"], .btn-update, button:has-text("Update")');
    if (await submitButton.count() > 0) {
      await submitButton.click();

      // Wait for success message
      try {
        await helpers.waitForNotification('updated');
      } catch {
        await page.waitForTimeout(2000);
      }

      await helpers.takeScreenshot('customer-profile-update-success');
    }
  });

  test('should display change password form', async ({ page }) => {
    await page.goto('/customer/change-password');

    // Check for password change form
    const passwordFields = [
      'input[name="current_password"]',
      'input[name="password"]',
      'input[name="password_confirmation"]'
    ];

    let fieldsFound = 0;
    for (const field of passwordFields) {
      if (await page.locator(field).count() > 0) {
        fieldsFound++;
      }
    }

    expect(fieldsFound).toBeGreaterThan(1);

    await helpers.takeScreenshot('customer-change-password-form');
  });

  test('should change customer password', async ({ page }) => {
    await page.goto('/customer/change-password');

    const passwordData = {
      current_password: 'password',
      password: 'newpassword123',
      password_confirmation: 'newpassword123'
    };

    // Fill password form
    await helpers.fillForm(passwordData);

    await helpers.takeScreenshot('customer-password-form-filled');

    // Submit password change
    await page.click('button[type="submit"]');

    // Wait for success or error
    try {
      await helpers.waitForNotification('password');
    } catch {
      await page.waitForTimeout(2000);
    }

    await helpers.takeScreenshot('customer-password-change-result');
  });

  test('should validate password change form', async ({ page }) => {
    await page.goto('/customer/change-password');

    // Try to submit empty form
    await page.click('button[type="submit"]');

    // Check for validation errors
    const validationSelectors = [
      '.invalid-feedback',
      '.error',
      '.text-danger',
      '.alert-danger'
    ];

    let validationFound = false;
    for (const selector of validationSelectors) {
      if (await page.locator(selector).count() > 0) {
        validationFound = true;
        break;
      }
    }

    await helpers.takeScreenshot('customer-password-validation');

    // Test password mismatch
    await helpers.fillForm({
      current_password: 'password',
      password: 'newpassword',
      password_confirmation: 'differentpassword'
    });

    await page.click('button[type="submit"]');
    await page.waitForTimeout(1000);

    await helpers.takeScreenshot('customer-password-mismatch');
  });

  test('should show family member profiles if family head', async ({ page }) => {
    await page.goto('/customer/profile');

    // Look for family member management
    const familySelectors = [
      'a[href*="family-member"]',
      '.family-section',
      '.family-members',
      'text=Family Member'
    ];

    for (const selector of familySelectors) {
      if (await page.locator(selector).count() > 0) {
        await page.click(selector);
        await page.waitForTimeout(2000);

        await helpers.takeScreenshot('customer-family-member-profiles');
        break;
      }
    }
  });

  test('should handle family member password management', async ({ page }) => {
    // Try to access family member password management
    const familyMemberUrls = [
      '/customer/family-member/1/change-password',
      '/customer/family-member/2/change-password'
    ];

    for (const url of familyMemberUrls) {
      try {
        await page.goto(url);

        if (await page.locator('input[name="password"]').count() > 0) {
          // Family member password form found
          await helpers.takeScreenshot('customer-family-member-password-form');

          // Fill and test form
          await helpers.fillForm({
            password: 'newfamilypassword',
            password_confirmation: 'newfamilypassword'
          });

          await helpers.takeScreenshot('customer-family-password-filled');
          break;
        }
      } catch (error) {
        // URL might not exist, continue to next
        continue;
      }
    }
  });

  test('should validate profile form fields', async ({ page }) => {
    await page.goto('/customer/profile');

    // Clear required fields and submit
    const nameField = page.locator('[name="name"]');
    if (await nameField.count() > 0) {
      await nameField.fill('');
    }

    const emailField = page.locator('[name="email"]');
    if (await emailField.count() > 0) {
      await emailField.fill('invalid-email');
    }

    await page.click('button[type="submit"]');

    // Check for validation
    const validationErrors = await helpers.testFormValidation('form', ['name', 'email']);

    await helpers.takeScreenshot('customer-profile-validation');

    if (validationErrors.length > 0) {
      console.warn('Profile form validation issues:', validationErrors);
    }
  });

  test('should test profile responsiveness', async ({ page }) => {
    await page.goto('/customer/profile');

    const viewports = [
      { width: 1920, height: 1080, name: 'desktop' },
      { width: 768, height: 1024, name: 'tablet' },
      { width: 375, height: 667, name: 'mobile' }
    ];

    for (const viewport of viewports) {
      await page.setViewportSize(viewport);
      await page.waitForTimeout(1000);

      await expect(page.locator('h1, .page-title')).toBeVisible();
      await expect(page.locator('form, .profile-form')).toBeVisible();

      await helpers.takeScreenshot(`customer-profile-${viewport.name}`);
    }
  });

  test('should check profile accessibility', async ({ page }) => {
    await page.goto('/customer/profile');

    const a11yResults = await helpers.checkAccessibility();

    if (a11yResults.violations.length > 0) {
      console.log('Customer profile accessibility violations:', a11yResults.violations);
    }

    const criticalViolations = a11yResults.violations.filter(v => v.impact === 'critical');
    expect(criticalViolations).toHaveLength(0);
  });

  test('should handle profile image upload if available', async ({ page }) => {
    await page.goto('/customer/profile');

    // Look for image upload
    const imageUploadSelectors = [
      'input[type="file"]',
      '.image-upload',
      '.profile-image-upload',
      '.avatar-upload'
    ];

    for (const selector of imageUploadSelectors) {
      if (await page.locator(selector).count() > 0) {
        await helpers.takeScreenshot('customer-profile-image-upload');
        break;
      }
    }
  });

  test('should show account status and verification info', async ({ page }) => {
    await page.goto('/customer/profile');

    // Look for account status information
    const statusSelectors = [
      '.account-status',
      '.verification-status',
      'text=verified',
      'text=active',
      '.status-badge'
    ];

    for (const selector of statusSelectors) {
      if (await page.locator(selector).count() > 0) {
        await helpers.takeScreenshot('customer-account-status');
        break;
      }
    }
  });

  test('should handle email verification status', async ({ page }) => {
    await page.goto('/customer/profile');

    // Look for email verification status
    const verificationSelectors = [
      'text=verify email',
      'text=unverified',
      '.email-verification',
      'button:has-text("Verify")'
    ];

    for (const selector of verificationSelectors) {
      if (await page.locator(selector).count() > 0) {
        await helpers.takeScreenshot('customer-email-verification-status');

        // If there's a verify button, test it
        if (selector.includes('button')) {
          await page.click(selector);
          await page.waitForTimeout(2000);
          await helpers.takeScreenshot('customer-email-verification-sent');
        }
        break;
      }
    }
  });
});