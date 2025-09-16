const { execSync } = require('child_process');

/**
 * Test Data Manager for Playwright Tests
 * Handles database seeding, cleanup, and test data generation
 */
class TestDataManager {
  constructor() {
    this.generatedData = {
      customers: [],
      insuranceCompanies: [],
      brokers: [],
      quotations: [],
      policies: [],
      claims: []
    };
  }

  /**
   * Initialize test database with fresh data
   */
  async initializeTestData() {
    console.log('🔄 Initializing test database...');

    try {
      // Run fresh migrations and seeders
      execSync('php artisan migrate:fresh --seed --env=testing', {
        stdio: 'inherit',
        cwd: process.cwd()
      });

      console.log('✅ Test database initialized successfully');
      return true;
    } catch (error) {
      console.error('❌ Failed to initialize test database:', error.message);
      return false;
    }
  }

  /**
   * Create test customer data
   */
  async createTestCustomer(customData = {}) {
    const customerData = {
      name: customData.name || `Test Customer ${Date.now()}`,
      email: customData.email || `customer${Date.now()}@example.com`,
      mobile_number: customData.mobile_number || '9999999999',
      address: customData.address || 'Test Address',
      city: customData.city || 'Test City',
      state: customData.state || 'Test State',
      pincode: customData.pincode || '123456',
      status: customData.status || 'active',
      ...customData
    };

    try {
      const command = `php artisan tinker --execute="
        use App\\Models\\Customer;
        use App\\Models\\FamilyGroup;
        use App\\Models\\FamilyMember;
        use Illuminate\\Support\\Facades\\Hash;

        // Create family group
        \\$familyGroup = FamilyGroup::create([
          'family_head_name' => '${customerData.name}',
          'family_head_mobile' => '${customerData.mobile_number}',
          'family_head_email' => '${customerData.email}'
        ]);

        // Create customer
        \\$customer = Customer::create([
          'name' => '${customerData.name}',
          'email' => '${customerData.email}',
          'mobile_number' => '${customerData.mobile_number}',
          'address' => '${customerData.address}',
          'city' => '${customerData.city}',
          'state' => '${customerData.state}',
          'pincode' => '${customerData.pincode}',
          'password' => Hash::make('password'),
          'email_verified_at' => now(),
          'status' => '${customerData.status}'
        ]);

        // Create family member relationship
        FamilyMember::create([
          'family_group_id' => \\$familyGroup->id,
          'customer_id' => \\$customer->id,
          'is_family_head' => true,
          'relation' => 'self'
        ]);

        echo json_encode([
          'customer_id' => \\$customer->id,
          'family_group_id' => \\$familyGroup->id,
          'email' => '${customerData.email}'
        ]);
      "`;

      const result = execSync(command, { encoding: 'utf8' });
      const customerInfo = JSON.parse(result.trim());

      this.generatedData.customers.push(customerInfo);

      console.log(`✅ Created test customer: ${customerData.email}`);
      return customerInfo;

    } catch (error) {
      console.error('❌ Failed to create test customer:', error.message);
      return null;
    }
  }

  /**
   * Create test insurance company
   */
  async createTestInsuranceCompany(customData = {}) {
    const companyData = {
      name: customData.name || `Test Insurance Company ${Date.now()}`,
      code: customData.code || `TIC${Date.now()}`,
      address: customData.address || 'Test Company Address',
      contact_person: customData.contact_person || 'Test Contact Person',
      mobile: customData.mobile || '9999999999',
      email: customData.email || `company${Date.now()}@example.com`,
      status: customData.status || 'active',
      ...customData
    };

    try {
      const command = `php artisan tinker --execute="
        use App\\Models\\InsuranceCompany;

        \\$company = InsuranceCompany::create([
          'name' => '${companyData.name}',
          'code' => '${companyData.code}',
          'address' => '${companyData.address}',
          'contact_person' => '${companyData.contact_person}',
          'mobile' => '${companyData.mobile}',
          'email' => '${companyData.email}',
          'status' => '${companyData.status}'
        ]);

        echo json_encode([
          'company_id' => \\$company->id,
          'name' => '${companyData.name}',
          'code' => '${companyData.code}'
        ]);
      "`;

      const result = execSync(command, { encoding: 'utf8' });
      const companyInfo = JSON.parse(result.trim());

      this.generatedData.insuranceCompanies.push(companyInfo);

      console.log(`✅ Created test insurance company: ${companyData.name}`);
      return companyInfo;

    } catch (error) {
      console.error('❌ Failed to create test insurance company:', error.message);
      return null;
    }
  }

  /**
   * Create test broker
   */
  async createTestBroker(customData = {}) {
    const brokerData = {
      name: customData.name || `Test Broker ${Date.now()}`,
      code: customData.code || `TB${Date.now()}`,
      mobile: customData.mobile || '9999999999',
      email: customData.email || `broker${Date.now()}@example.com`,
      address: customData.address || 'Test Broker Address',
      status: customData.status || 'active',
      ...customData
    };

    try {
      const command = `php artisan tinker --execute="
        use App\\Models\\Broker;

        \\$broker = Broker::create([
          'name' => '${brokerData.name}',
          'code' => '${brokerData.code}',
          'mobile' => '${brokerData.mobile}',
          'email' => '${brokerData.email}',
          'address' => '${brokerData.address}',
          'status' => '${brokerData.status}'
        ]);

        echo json_encode([
          'broker_id' => \\$broker->id,
          'name' => '${brokerData.name}',
          'code' => '${brokerData.code}'
        ]);
      "`;

      const result = execSync(command, { encoding: 'utf8' });
      const brokerInfo = JSON.parse(result.trim());

      this.generatedData.brokers.push(brokerInfo);

      console.log(`✅ Created test broker: ${brokerData.name}`);
      return brokerInfo;

    } catch (error) {
      console.error('❌ Failed to create test broker:', error.message);
      return null;
    }
  }

  /**
   * Create test quotation
   */
  async createTestQuotation(customerId, customData = {}) {
    const quotationData = {
      customer_id: customerId,
      vehicle_number: customData.vehicle_number || `TD${Date.now()}`,
      vehicle_make: customData.vehicle_make || 'Toyota',
      vehicle_model: customData.vehicle_model || 'Corolla',
      manufacturing_year: customData.manufacturing_year || 2023,
      engine_cc: customData.engine_cc || 1800,
      fuel_type: customData.fuel_type || 'Petrol',
      vehicle_value: customData.vehicle_value || 500000,
      status: customData.status || 'active',
      ...customData
    };

    try {
      const command = `php artisan tinker --execute="
        use App\\Models\\Quotation;

        \\$quotation = Quotation::create([
          'customer_id' => ${quotationData.customer_id},
          'vehicle_number' => '${quotationData.vehicle_number}',
          'vehicle_make' => '${quotationData.vehicle_make}',
          'vehicle_model' => '${quotationData.vehicle_model}',
          'manufacturing_year' => ${quotationData.manufacturing_year},
          'engine_cc' => ${quotationData.engine_cc},
          'fuel_type' => '${quotationData.fuel_type}',
          'vehicle_value' => ${quotationData.vehicle_value},
          'status' => '${quotationData.status}'
        ]);

        echo json_encode([
          'quotation_id' => \\$quotation->id,
          'vehicle_number' => '${quotationData.vehicle_number}',
          'customer_id' => ${quotationData.customer_id}
        ]);
      "`;

      const result = execSync(command, { encoding: 'utf8' });
      const quotationInfo = JSON.parse(result.trim());

      this.generatedData.quotations.push(quotationInfo);

      console.log(`✅ Created test quotation: ${quotationData.vehicle_number}`);
      return quotationInfo;

    } catch (error) {
      console.error('❌ Failed to create test quotation:', error.message);
      return null;
    }
  }

  /**
   * Create test insurance policy
   */
  async createTestPolicy(customerId, customData = {}) {
    const policyData = {
      customer_id: customerId,
      policy_number: customData.policy_number || `POL${Date.now()}`,
      insurance_company_id: customData.insurance_company_id || 1,
      premium_amount: customData.premium_amount || 25000,
      coverage_amount: customData.coverage_amount || 500000,
      start_date: customData.start_date || new Date().toISOString().split('T')[0],
      end_date: customData.end_date || new Date(Date.now() + 365*24*60*60*1000).toISOString().split('T')[0],
      status: customData.status || 'active',
      ...customData
    };

    try {
      const command = `php artisan tinker --execute="
        use App\\Models\\CustomerInsurance;

        \\$policy = CustomerInsurance::create([
          'customer_id' => ${policyData.customer_id},
          'policy_number' => '${policyData.policy_number}',
          'insurance_company_id' => ${policyData.insurance_company_id},
          'premium_amount' => ${policyData.premium_amount},
          'coverage_amount' => ${policyData.coverage_amount},
          'start_date' => '${policyData.start_date}',
          'end_date' => '${policyData.end_date}',
          'status' => '${policyData.status}'
        ]);

        echo json_encode([
          'policy_id' => \\$policy->id,
          'policy_number' => '${policyData.policy_number}',
          'customer_id' => ${policyData.customer_id}
        ]);
      "`;

      const result = execSync(command, { encoding: 'utf8' });
      const policyInfo = JSON.parse(result.trim());

      this.generatedData.policies.push(policyInfo);

      console.log(`✅ Created test policy: ${policyData.policy_number}`);
      return policyInfo;

    } catch (error) {
      console.error('❌ Failed to create test policy:', error.message);
      return null;
    }
  }

  /**
   * Create test claim
   */
  async createTestClaim(policyId, customData = {}) {
    const claimData = {
      policy_id: policyId,
      claim_number: customData.claim_number || `CLM${Date.now()}`,
      claim_amount: customData.claim_amount || 50000,
      incident_date: customData.incident_date || new Date().toISOString().split('T')[0],
      incident_description: customData.incident_description || 'Test incident for claim processing',
      claim_type: customData.claim_type || 'vehicle_damage',
      status: customData.status || 'pending',
      ...customData
    };

    try {
      const command = `php artisan tinker --execute="
        use App\\Models\\Claim;

        \\$claim = Claim::create([
          'policy_id' => ${claimData.policy_id},
          'claim_number' => '${claimData.claim_number}',
          'claim_amount' => ${claimData.claim_amount},
          'incident_date' => '${claimData.incident_date}',
          'incident_description' => '${claimData.incident_description}',
          'claim_type' => '${claimData.claim_type}',
          'status' => '${claimData.status}'
        ]);

        echo json_encode([
          'claim_id' => \\$claim->id,
          'claim_number' => '${claimData.claim_number}',
          'policy_id' => ${claimData.policy_id}
        ]);
      "`;

      const result = execSync(command, { encoding: 'utf8' });
      const claimInfo = JSON.parse(result.trim());

      this.generatedData.claims.push(claimInfo);

      console.log(`✅ Created test claim: ${claimData.claim_number}`);
      return claimInfo;

    } catch (error) {
      console.error('❌ Failed to create test claim:', error.message);
      return null;
    }
  }

  /**
   * Create comprehensive test dataset
   */
  async createComprehensiveTestData() {
    console.log('🏗️  Creating comprehensive test dataset...');

    // Create insurance companies
    const company1 = await this.createTestInsuranceCompany({
      name: 'Test Insurance Corp',
      code: 'TIC'
    });

    const company2 = await this.createTestInsuranceCompany({
      name: 'Demo Insurance Ltd',
      code: 'DIL'
    });

    // Create brokers
    const broker1 = await this.createTestBroker({
      name: 'Test Broker Services',
      code: 'TBS'
    });

    // Create customers
    const customer1 = await this.createTestCustomer({
      name: 'John Doe',
      email: 'john.doe@example.com'
    });

    const customer2 = await this.createTestCustomer({
      name: 'Jane Smith',
      email: 'jane.smith@example.com'
    });

    // Create quotations
    if (customer1) {
      await this.createTestQuotation(customer1.customer_id, {
        vehicle_number: 'MH12AB1234',
        vehicle_make: 'Honda',
        vehicle_model: 'City'
      });
    }

    if (customer2) {
      await this.createTestQuotation(customer2.customer_id, {
        vehicle_number: 'MH12XY9876',
        vehicle_make: 'Toyota',
        vehicle_model: 'Camry'
      });
    }

    // Create policies
    if (customer1 && company1) {
      const policy1 = await this.createTestPolicy(customer1.customer_id, {
        policy_number: 'POL001',
        insurance_company_id: company1.company_id
      });

      // Create claim for this policy
      if (policy1) {
        await this.createTestClaim(policy1.policy_id, {
          claim_number: 'CLM001',
          claim_amount: 75000
        });
      }
    }

    console.log('✅ Comprehensive test dataset created successfully');
    return this.generatedData;
  }

  /**
   * Clean up test data
   */
  async cleanupTestData() {
    console.log('🧹 Cleaning up test data...');

    try {
      // Clean up in reverse dependency order
      execSync('php artisan tinker --execute="
        use App\\Models\\Claim;
        use App\\Models\\CustomerInsurance;
        use App\\Models\\Quotation;
        use App\\Models\\FamilyMember;
        use App\\Models\\Customer;
        use App\\Models\\FamilyGroup;
        use App\\Models\\Broker;
        use App\\Models\\InsuranceCompany;

        // Delete in dependency order
        Claim::whereIn(\\'claim_number\\', [\\'CLM001\\', \\'CLM002\\'])->delete();
        CustomerInsurance::whereIn(\\'policy_number\\', [\\'POL001\\', \\'POL002\\'])->delete();
        Quotation::whereIn(\\'vehicle_number\\', [\\'MH12AB1234\\', \\'MH12XY9876\\'])->delete();
        FamilyMember::whereIn(\\'customer_id\\', Customer::whereIn(\\'email\\', [\\'john.doe@example.com\\', \\'jane.smith@example.com\\'])->pluck(\\'id\\'))->delete();
        Customer::whereIn(\\'email\\', [\\'john.doe@example.com\\', \\'jane.smith@example.com\\'])->delete();
        FamilyGroup::whereIn(\\'family_head_email\\', [\\'john.doe@example.com\\', \\'jane.smith@example.com\\'])->delete();
        Broker::whereIn(\\'code\\', [\\'TBS\\'])->delete();
        InsuranceCompany::whereIn(\\'code\\', [\\'TIC\\', \\'DIL\\'])->delete();

        echo \\'Test data cleaned up\\';
      "', { stdio: 'inherit' });

      console.log('✅ Test data cleanup completed');

    } catch (error) {
      console.error('❌ Failed to cleanup test data:', error.message);
    }
  }

  /**
   * Get generated test data
   */
  getGeneratedData() {
    return this.generatedData;
  }

  /**
   * Reset generated data tracker
   */
  resetGeneratedData() {
    this.generatedData = {
      customers: [],
      insuranceCompanies: [],
      brokers: [],
      quotations: [],
      policies: [],
      claims: []
    };
  }
}

module.exports = { TestDataManager };