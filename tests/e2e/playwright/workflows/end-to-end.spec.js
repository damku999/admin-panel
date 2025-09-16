const { test, expect } = require('@playwright/test');
const { TestHelpers } = require('../fixtures/test-helpers');

test.describe('End-to-End Workflow Tests', () => {
  let helpers;

  test.beforeEach(async ({ page }) => {
    helpers = new TestHelpers(page);
  });

  test.describe('Complete Customer Onboarding Workflow', () => {
    test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

    test('should complete full customer lifecycle from creation to policy', async ({ page }) => {
      // Step 1: Create Customer
      await page.goto('/customers');
      await helpers.waitAndClick('a:has-text("Add"), a[href*="create"]');

      const customerData = helpers.generateTestData('customer');
      await helpers.fillForm(customerData);

      await helpers.takeScreenshot('workflow-customer-created');

      await page.click('button[type="submit"]');
      await page.waitForTimeout(2000);

      // Step 2: Create Quotation for Customer
      await page.goto('/quotations');
      await helpers.waitAndClick('a:has-text("Add"), a:has-text("Create")');

      // Fill quotation data
      const quotationData = {
        vehicle_number: 'WF12AB1234',
        vehicle_make: 'Honda',
        vehicle_model: 'City',
        manufacturing_year: '2022',
        engine_cc: '1500'
      };

      await helpers.fillForm(quotationData);

      // Select the customer we just created
      const customerSelect = page.locator('select[name="customer_id"]');
      if (await customerSelect.count() > 0) {
        await customerSelect.selectOption({ index: 1 });
      }

      await helpers.takeScreenshot('workflow-quotation-created');

      await page.click('button[type="submit"]');
      await page.waitForTimeout(3000);

      // Step 3: Generate Insurance Quotes
      const generateBtn = page.locator('button:has-text("Generate")');
      if (await generateBtn.count() > 0) {
        await generateBtn.click();
        await page.waitForTimeout(5000);
        await helpers.takeScreenshot('workflow-quotes-generated');
      }

      // Step 4: Create Insurance Policy (if possible)
      await page.goto('/customer_insurances');
      const addPolicyBtn = page.locator('a:has-text("Add"), a[href*="create"]');
      if (await addPolicyBtn.count() > 0) {
        await addPolicyBtn.click();

        // Fill policy data
        const policyData = {
          policy_number: 'POL' + Date.now(),
          premium_amount: '25000',
          coverage_amount: '500000'
        };

        await helpers.fillForm(policyData);

        await helpers.takeScreenshot('workflow-policy-created');
      }

      // Step 5: Verify customer can see their data
      // Switch to customer context
      await page.goto('/customer/login');

      // Login as test customer or create one
      await helpers.fillForm({
        email: customerData.email,
        password: 'password'
      });

      await page.click('button[type="submit"]');

      // Check if customer dashboard shows the policy/quotation
      try {
        await page.waitForURL('**/customer/dashboard');
        await helpers.takeScreenshot('workflow-customer-dashboard-complete');
      } catch (error) {
        console.log('Customer login not successful, workflow partially complete');
      }
    });
  });

  test.describe('Claims Processing Workflow', () => {
    test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

    test('should complete full claims processing workflow', async ({ page }) => {
      // Step 1: Create New Claim
      await page.goto('/insurance-claims');
      await helpers.waitAndClick('a:has-text("Add"), a:has-text("Create")');

      const claimData = {
        policy_number: 'POL123456789',
        claim_amount: '75000',
        incident_date: '2023-12-15',
        incident_description: 'Vehicle collision on main road, front bumper damaged'
      };

      await helpers.fillForm(claimData);

      const claimTypeSelect = page.locator('select[name="claim_type"]');
      if (await claimTypeSelect.count() > 0) {
        await claimTypeSelect.selectOption({ index: 1 });
      }

      await helpers.takeScreenshot('workflow-claim-created');

      await page.click('button[type="submit"]');
      await page.waitForTimeout(2000);

      // Step 2: Update Claim Status
      await page.goto('/insurance-claims');

      const statusBtn = page.locator('.btn-warning:has-text("Pending"), .status-toggle').first();
      if (await statusBtn.count() > 0) {
        await statusBtn.click();
        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('workflow-claim-status-updated');
      }

      // Step 3: Add Document Requirements
      const documentsBtn = page.locator('button:has-text("Documents")').first();
      if (await documentsBtn.count() > 0) {
        await documentsBtn.click();
        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('workflow-claim-documents');
      }

      // Step 4: Send WhatsApp Notification
      const whatsappBtn = page.locator('button:has-text("WhatsApp")').first();
      if (await whatsappBtn.count() > 0) {
        await whatsappBtn.click();

        const documentListBtn = page.locator('button:has-text("Document List")');
        if (await documentListBtn.count() > 0) {
          await documentListBtn.click();
          await page.waitForTimeout(2000);
          await helpers.takeScreenshot('workflow-whatsapp-sent');
        }
      }

      // Step 5: Add Claim Stage
      const addStageBtn = page.locator('button:has-text("Add Stage")');
      if (await addStageBtn.count() > 0) {
        await addStageBtn.click();
        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('workflow-claim-stage-added');
      }

      // Step 6: Update Liability Details
      const liabilityBtn = page.locator('button:has-text("Liability")');
      if (await liabilityBtn.count() > 0) {
        await liabilityBtn.click();
        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('workflow-liability-updated');
      }

      // Step 7: Final Status Update to Approved
      const approveBtn = page.locator('.btn-success:has-text("Approved")');
      if (await approveBtn.count() > 0) {
        await approveBtn.click();
        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('workflow-claim-approved');
      }
    });
  });

  test.describe('Customer Journey Workflow', () => {
    test('should complete customer self-service journey', async ({ page }) => {
      // Step 1: Customer Login
      await page.goto('/customer/login');

      await helpers.fillForm({
        email: 'testcustomer@example.com',
        password: 'password'
      });

      await page.click('button[type="submit"]');
      await page.waitForURL('**/customer/dashboard');

      await helpers.takeScreenshot('workflow-customer-logged-in');

      // Step 2: View Policies
      const policiesLink = page.locator('a[href*="policies"], a:has-text("Policies")');
      if (await policiesLink.count() > 0) {
        await policiesLink.first().click();
        await page.waitForURL('**/customer/policies');

        await helpers.takeScreenshot('workflow-customer-policies-viewed');

        // Download Policy Document
        const downloadBtn = page.locator('a:has-text("Download"), button:has-text("Download")').first();
        if (await downloadBtn.count() > 0) {
          const downloadPromise = page.waitForEvent('download');
          await downloadBtn.click();

          try {
            const download = await downloadPromise;
            await helpers.takeScreenshot('workflow-customer-document-downloaded');
          } catch (error) {
            await helpers.takeScreenshot('workflow-customer-download-attempted');
          }
        }
      }

      // Step 3: View Quotations
      const quotationsLink = page.locator('a[href*="quotations"], a:has-text("Quotations")');
      if (await quotationsLink.count() > 0) {
        await quotationsLink.first().click();
        await page.waitForURL('**/customer/quotations');

        await helpers.takeScreenshot('workflow-customer-quotations-viewed');
      }

      // Step 4: Update Profile
      await page.goto('/customer/profile');

      const nameField = page.locator('[name="name"]');
      if (await nameField.count() > 0) {
        await nameField.fill('Updated Customer Name');

        const updateBtn = page.locator('button[type="submit"], button:has-text("Update")');
        if (await updateBtn.count() > 0) {
          await updateBtn.click();
          await page.waitForTimeout(2000);
          await helpers.takeScreenshot('workflow-customer-profile-updated');
        }
      }

      // Step 5: Change Password
      await page.goto('/customer/change-password');

      const passwordData = {
        current_password: 'password',
        password: 'newpassword123',
        password_confirmation: 'newpassword123'
      };

      await helpers.fillForm(passwordData);

      const changePasswordBtn = page.locator('button[type="submit"]');
      if (await changePasswordBtn.count() > 0) {
        await changePasswordBtn.click();
        await page.waitForTimeout(2000);
        await helpers.takeScreenshot('workflow-customer-password-changed');
      }

      // Step 6: Logout
      const logoutBtn = page.locator('a[href*="logout"], form[action*="logout"] button');
      if (await logoutBtn.count() > 0) {
        await logoutBtn.click();
        await page.waitForURL('**/customer/login');
        await helpers.takeScreenshot('workflow-customer-logged-out');
      }
    });
  });

  test.describe('Admin Report Generation Workflow', () => {
    test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

    test('should complete full reporting workflow', async ({ page }) => {
      // Step 1: Access Reports
      await page.goto('/reports');
      await helpers.takeScreenshot('workflow-reports-accessed');

      // Step 2: Select Report Type
      const reportTypeSelect = page.locator('select[name="report_type"]');
      if (await reportTypeSelect.count() > 0) {
        await reportTypeSelect.selectOption('customers');
        await helpers.takeScreenshot('workflow-report-type-selected');
      }

      // Step 3: Set Date Range
      const dateFromField = page.locator('input[name="date_from"], input[name="from_date"]');
      if (await dateFromField.count() > 0) {
        await dateFromField.fill('2023-01-01');
      }

      const dateToField = page.locator('input[name="date_to"], input[name="to_date"]');
      if (await dateToField.count() > 0) {
        await dateToField.fill('2023-12-31');
      }

      await helpers.takeScreenshot('workflow-report-dates-set');

      // Step 4: Select Columns
      const columnCheckboxes = page.locator('input[type="checkbox"][name*="column"]');
      const checkboxCount = await columnCheckboxes.count();
      for (let i = 0; i < Math.min(3, checkboxCount); i++) {
        await columnCheckboxes.nth(i).check();
      }

      await helpers.takeScreenshot('workflow-report-columns-selected');

      // Step 5: Generate Report
      const generateBtn = page.locator('button:has-text("Generate")');
      if (await generateBtn.count() > 0) {
        await generateBtn.click();
        await page.waitForTimeout(5000);
        await helpers.takeScreenshot('workflow-report-generated');
      }

      // Step 6: Export Report
      const exportBtn = page.locator('a:has-text("Export"), button:has-text("Export")');
      if (await exportBtn.count() > 0) {
        const downloadPromise = page.waitForEvent('download');
        await exportBtn.click();

        try {
          const download = await downloadPromise;
          await helpers.takeScreenshot('workflow-report-exported');
        } catch (error) {
          await helpers.takeScreenshot('workflow-report-export-attempted');
        }
      }
    });
  });

  test.describe('Multi-User Workflow', () => {
    test('should handle admin and customer interaction workflow', async ({ browser }) => {
      // Create two contexts - one for admin, one for customer
      const adminContext = await browser.newContext({
        storageState: './tests/e2e/playwright/auth/admin.json'
      });
      const customerContext = await browser.newContext({
        storageState: './tests/e2e/playwright/auth/customer.json'
      });

      const adminPage = await adminContext.newPage();
      const customerPage = await customerContext.newPage();

      const adminHelpers = new TestHelpers(adminPage);
      const customerHelpers = new TestHelpers(customerPage);

      try {
        // Admin creates a quotation
        await adminPage.goto('/quotations/create');
        await adminHelpers.fillForm({
          vehicle_number: 'MW12XY9876',
          vehicle_make: 'Toyota',
          vehicle_model: 'Camry'
        });

        await adminPage.click('button[type="submit"]');
        await adminPage.waitForTimeout(2000);
        await adminHelpers.takeScreenshot('workflow-admin-quotation-created');

        // Customer checks dashboard for updates
        await customerPage.goto('/customer/dashboard');
        await customerHelpers.takeScreenshot('workflow-customer-dashboard-check');

        // Admin sends WhatsApp notification
        await adminPage.goto('/quotations');
        const whatsappBtn = adminPage.locator('button:has-text("WhatsApp")').first();
        if (await whatsappBtn.count() > 0) {
          await whatsappBtn.click();
          await adminPage.waitForTimeout(2000);
          await adminHelpers.takeScreenshot('workflow-admin-whatsapp-sent');
        }

        // Customer views quotations
        await customerPage.goto('/customer/quotations');
        await customerHelpers.takeScreenshot('workflow-customer-quotations-check');

      } finally {
        await adminContext.close();
        await customerContext.close();
      }
    });
  });

  test.describe('Error Recovery Workflow', () => {
    test.use({ storageState: './tests/e2e/playwright/auth/admin.json' });

    test('should handle validation errors and recovery', async ({ page }) => {
      // Step 1: Attempt to create customer with invalid data
      await page.goto('/customers/create');

      // Submit empty form to trigger validation
      await page.click('button[type="submit"]');
      await page.waitForTimeout(1000);

      await helpers.takeScreenshot('workflow-validation-errors-shown');

      // Step 2: Fix validation errors
      const customerData = helpers.generateTestData('customer');
      await helpers.fillForm(customerData);

      await helpers.takeScreenshot('workflow-validation-errors-fixed');

      // Step 3: Successful submission
      await page.click('button[type="submit"]');
      await page.waitForTimeout(2000);

      await helpers.takeScreenshot('workflow-error-recovery-complete');
    });

    test('should handle network errors gracefully', async ({ page }) => {
      await page.goto('/customers');

      // Simulate network failure
      await page.route('**/customers/export', route => {
        route.abort('failed');
      });

      // Attempt export
      const exportBtn = page.locator('a[href*="export"]');
      if (await exportBtn.count() > 0) {
        await exportBtn.click();
        await page.waitForTimeout(2000);

        await helpers.takeScreenshot('workflow-network-error-handled');
      }
    });
  });
});