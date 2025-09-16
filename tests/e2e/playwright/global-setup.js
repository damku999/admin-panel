const { chromium } = require('@playwright/test');
const { execSync } = require('child_process');

async function globalSetup() {
  console.log('🔧 Starting global setup...');

  // Setup test database
  console.log('📊 Setting up test database...');
  try {
    // Run migrations and seeders for test database
    execSync('php artisan migrate:fresh --seed --env=testing', { stdio: 'inherit' });
    console.log('✅ Test database setup completed');
  } catch (error) {
    console.error('❌ Database setup failed:', error.message);
    process.exit(1);
  }

  // Create admin authentication state
  console.log('🔐 Creating admin authentication state...');
  const browser = await chromium.launch();
  const context = await browser.newContext();
  const page = await context.newPage();

  try {
    // Navigate to login page
    await page.goto(process.env.APP_URL || 'http://localhost:8000');

    // Login as admin
    await page.fill('input[name="email"]', 'admin@admin.com');
    await page.fill('input[name="password"]', 'Admin@123#');
    await page.click('button[type="submit"]');

    // Wait for successful login redirect
    await page.waitForURL('**/home');

    // Save authentication state
    await context.storageState({ path: './tests/e2e/playwright/auth/admin.json' });
    console.log('✅ Admin authentication state saved');

  } catch (error) {
    console.error('❌ Admin authentication setup failed:', error.message);
  }

  await browser.close();

  // Setup customer authentication state
  console.log('👥 Setting up customer authentication...');
  await setupCustomerAuth();

  console.log('✅ Global setup completed');
}

async function setupCustomerAuth() {
  // Create a test customer if needed
  try {
    execSync(`php artisan tinker --execute="
      use App\\Models\\Customer;
      use App\\Models\\FamilyGroup;
      use App\\Models\\FamilyMember;
      use Illuminate\\Support\\Facades\\Hash;

      // Create family group
      \\$familyGroup = FamilyGroup::firstOrCreate([
        'family_head_name' => 'Test Family Head',
        'family_head_mobile' => '9999999999',
        'family_head_email' => 'testfamily@example.com'
      ]);

      // Create test customer
      \\$customer = Customer::firstOrCreate([
        'email' => 'testcustomer@example.com'
      ], [
        'name' => 'Test Customer',
        'mobile_number' => '9999999999',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
        'status' => 'active'
      ]);

      // Create family member relationship
      FamilyMember::firstOrCreate([
        'family_group_id' => \\$familyGroup->id,
        'customer_id' => \\$customer->id,
        'is_family_head' => true,
        'relation' => 'self'
      ]);

      echo 'Test customer created successfully';
    "`, { stdio: 'inherit' });

    // Create customer authentication state
    const browser = await chromium.launch();
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
      // Navigate to customer login page
      await page.goto((process.env.APP_URL || 'http://localhost:8000') + '/customer/login');

      // Login as test customer
      await page.fill('input[name="email"]', 'testcustomer@example.com');
      await page.fill('input[name="password"]', 'password');
      await page.click('button[type="submit"]');

      // Wait for successful login redirect
      await page.waitForURL('**/customer/dashboard');

      // Save authentication state
      await context.storageState({ path: './tests/e2e/playwright/auth/customer.json' });
      console.log('✅ Customer authentication state saved');

    } catch (error) {
      console.error('❌ Customer authentication setup failed:', error.message);
    }

    await browser.close();

  } catch (error) {
    console.error('❌ Customer setup failed:', error.message);
  }
}

module.exports = globalSetup;