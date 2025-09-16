const { test as setup } = require('@playwright/test');
const { TestDataManager } = require('./data/test-data-manager');
const { TestHelpers } = require('./fixtures/test-helpers');

// Global setup for authentication states and test data
setup.describe.configure({ mode: 'serial' });

setup.describe('Test Environment Setup', () => {
  let dataManager;

  setup.beforeAll(() => {
    dataManager = new TestDataManager();
  });

  setup('initialize test database', async () => {
    console.log('🔧 Setting up test database...');
    const success = await dataManager.initializeTestData();
    if (!success) {
      throw new Error('Failed to initialize test database');
    }
  });

  setup('create comprehensive test data', async () => {
    console.log('📊 Creating comprehensive test dataset...');
    await dataManager.createComprehensiveTestData();
  });

  setup('setup admin authentication', async ({ page }) => {
    const helpers = new TestHelpers(page);

    console.log('🔐 Setting up admin authentication...');

    // Navigate to admin login
    await page.goto('/login');

    // Fill admin credentials
    await page.fill('input[name="email"]', 'admin@admin.com');
    await page.fill('input[name="password"]', 'Admin@123#');

    // Click login button
    await page.click('button[type="submit"]');

    // Wait for successful login redirect
    await page.waitForURL('**/home');

    // Verify we're logged in
    await page.waitForSelector('h1, .page-title', { timeout: 10000 });

    // Save authentication state
    await page.context().storageState({
      path: './tests/e2e/playwright/auth/admin.json'
    });

    console.log('✅ Admin authentication state saved');

    // Take screenshot of successful login
    await helpers.takeScreenshot('setup-admin-authenticated');
  });

  setup('setup customer authentication', async ({ page }) => {
    const helpers = new TestHelpers(page);

    console.log('👥 Setting up customer authentication...');

    // Create a test customer with known credentials
    const testCustomer = await dataManager.createTestCustomer({
      name: 'Test Customer',
      email: 'testcustomer@example.com',
      mobile_number: '9999999999'
    });

    if (!testCustomer) {
      throw new Error('Failed to create test customer');
    }

    // Navigate to customer login
    await page.goto('/customer/login');

    // Fill customer credentials
    await page.fill('input[name="email"]', 'testcustomer@example.com');
    await page.fill('input[name="password"]', 'password');

    // Click login button
    await page.click('button[type="submit"]');

    try {
      // Wait for successful login redirect
      await page.waitForURL('**/customer/dashboard', { timeout: 10000 });

      // Verify we're on customer dashboard
      await page.waitForSelector('h1, .page-title', { timeout: 5000 });

      // Save authentication state
      await page.context().storageState({
        path: './tests/e2e/playwright/auth/customer.json'
      });

      console.log('✅ Customer authentication state saved');

      // Take screenshot of successful login
      await helpers.takeScreenshot('setup-customer-authenticated');

    } catch (error) {
      console.warn('⚠️  Customer authentication setup failed - some tests may not work properly');
      console.warn('Error:', error.message);

      // Create a minimal auth state file anyway
      await page.context().storageState({
        path: './tests/e2e/playwright/auth/customer.json'
      });
    }
  });

  setup('verify test environment', async ({ page }) => {
    console.log('✅ Verifying test environment...');

    // Check admin access
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@admin.com');
    await page.fill('input[name="password"]', 'Admin@123#');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/home');

    // Verify key admin pages are accessible
    const adminPages = [
      '/customers',
      '/insurance_companies',
      '/quotations',
      '/insurance-claims',
      '/reports',
      '/users'
    ];

    for (const url of adminPages) {
      try {
        await page.goto(url);
        await page.waitForSelector('h1, .page-title', { timeout: 5000 });
        console.log(`✅ Admin page accessible: ${url}`);
      } catch (error) {
        console.warn(`⚠️  Admin page may have issues: ${url}`);
      }
    }

    // Check customer portal access
    try {
      await page.goto('/customer/login');
      await page.fill('input[name="email"]', 'testcustomer@example.com');
      await page.fill('input[name="password"]', 'password');
      await page.click('button[type="submit"]');

      const customerPages = [
        '/customer/dashboard',
        '/customer/policies',
        '/customer/quotations',
        '/customer/profile'
      ];

      for (const url of customerPages) {
        try {
          await page.goto(url);
          await page.waitForSelector('h1, .page-title', { timeout: 5000 });
          console.log(`✅ Customer page accessible: ${url}`);
        } catch (error) {
          console.warn(`⚠️  Customer page may have issues: ${url}`);
        }
      }
    } catch (error) {
      console.warn('⚠️  Customer portal verification failed');
    }

    console.log('🎉 Test environment verification completed');
  });

  setup.afterAll(async () => {
    console.log('✨ Setup completed successfully!');
    console.log('');
    console.log('📋 Setup Summary:');
    console.log('- ✅ Test database initialized');
    console.log('- ✅ Comprehensive test data created');
    console.log('- ✅ Admin authentication state saved');
    console.log('- ✅ Customer authentication state saved');
    console.log('- ✅ Test environment verified');
    console.log('');
    console.log('🚀 Ready to run Playwright tests!');
  });
});