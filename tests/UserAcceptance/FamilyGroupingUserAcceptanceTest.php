<?php

namespace Tests\UserAcceptance;

use App\Models\Customer;
use App\Models\FamilyGroup;
use App\Models\FamilyMember;
use App\Models\CustomerInsurance;
use App\Models\InsuranceCompany;
use App\Models\CustomerAuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FamilyGroupingUserAcceptanceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createSampleFamilyData();
    }

    public function test_user_story_1_family_head_can_login_and_view_dashboard(): void
    {
        // US-001: As a family head, I want to login to see my family dashboard
        
        // Given: I am a family head with valid credentials
        $familyHead = Customer::where('email', 'johnson.head@example.com')->first();
        
        // When: I visit the login page and submit my credentials
        $response = $this->get(route('customer.login'));
        $response->assertStatus(200);
        $response->assertSee('Customer Login');
        $response->assertSee('Access your family insurance policies');
        
        $loginResponse = $this->post(route('customer.login'), [
            'email' => 'johnson.head@example.com',
            'password' => 'SecurePassword123!'
        ]);
        
        // Then: I should be redirected to my dashboard
        $loginResponse->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticatedAs($familyHead, 'customer');
        
        // And: I should see my family dashboard with welcome message
        $dashboardResponse = $this->get(route('customer.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Family Insurance Dashboard');
        $dashboardResponse->assertSee('Welcome, Johnson Family Head');
        $dashboardResponse->assertSee('Johnson Family');
        
        // And: My login should be logged for security
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $familyHead->id,
            'action' => 'login',
            'success' => true
        ]);
    }

    public function test_user_story_2_family_head_can_view_all_family_policies(): void
    {
        // US-002: As a family head, I want to see all policies for my family members
        
        $familyHead = Customer::where('email', 'johnson.head@example.com')->first();
        $this->actingAs($familyHead, 'customer');
        
        // When: I navigate to the family policies page
        $response = $this->get(route('customer.policies'));
        
        // Then: I should see all family insurance policies
        $response->assertStatus(200);
        $response->assertSee('Family Insurance Policies');
        $response->assertSee('Johnson Family Policies');
        
        // And: I should see policies for all family members
        $response->assertSee('HEAD-LIFE-001'); // My life insurance
        $response->assertSee('HEAD-AUTO-002'); // My auto insurance
        $response->assertSee('SPOUSE-HEALTH-003'); // Spouse's health insurance
        $response->assertSee('CHILD1-HEALTH-004'); // Child's health insurance
        
        // And: I should see policy owner information
        $response->assertSee('Johnson Family Head'); // My name
        $response->assertSee('Johnson Family Spouse'); // Spouse name
        $response->assertSee('Johnson Family Child'); // Child name
        
        // And: I should see policy status indicators
        $response->assertSee('Active'); // Active policies
        $response->assertSee('Premium Due'); // Policies needing payment
    }

    public function test_user_story_3_family_head_can_view_policy_details(): void
    {
        // US-003: As a family head, I want to view detailed information for any family policy
        
        $familyHead = Customer::where('email', 'johnson.head@example.com')->first();
        $this->actingAs($familyHead, 'customer');
        
        $spousePolicy = CustomerInsurance::where('policy_no', 'SPOUSE-HEALTH-003')->first();
        
        // When: I click on a family member's policy to view details
        $response = $this->get(route('customer.policies.detail', $spousePolicy->id));
        
        // Then: I should see comprehensive policy information
        $response->assertStatus(200);
        $response->assertSee('Policy Details');
        $response->assertSee('SPOUSE-HEALTH-003');
        $response->assertSee('Health Plus Gold Plan');
        $response->assertSee('Johnson Family Spouse');
        $response->assertSee('Secure Health Insurance');
        $response->assertSee('$2,400.00'); // Annual premium
        $response->assertSee('Active'); // Policy status
        
        // And: I should see coverage details
        $response->assertSee('Coverage Amount: $500,000');
        $response->assertSee('Deductible: $1,000');
        $response->assertSee('In-Network Benefits: 90%');
        
        // And: My access should be logged
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $familyHead->id,
            'action' => 'view_policy_detail',
            'resource_type' => 'policy',
            'resource_id' => $spousePolicy->id,
            'success' => true
        ]);
    }

    public function test_user_story_4_family_member_can_view_own_policies_only(): void
    {
        // US-004: As a regular family member, I want to see only my own policies
        
        $familyMember = Customer::where('email', 'johnson.spouse@example.com')->first();
        $this->actingAs($familyMember, 'customer');
        
        // When: I access the policies page
        $response = $this->get(route('customer.policies'));
        
        // Then: I should see only my policies, not other family members'
        $response->assertStatus(200);
        $response->assertSee('My Insurance Policies');
        $response->assertSee('SPOUSE-HEALTH-003'); // My health policy
        
        // And: I should NOT see other family members' policies
        $response->assertDontSee('HEAD-LIFE-001'); // Family head's policy
        $response->assertDontSee('HEAD-AUTO-002'); // Family head's auto
        $response->assertDontSee('CHILD1-HEALTH-004'); // Child's policy
        
        // And: I should see my name prominently
        $response->assertSee('Johnson Family Spouse');
    }

    public function test_user_story_5_family_member_cannot_access_other_member_policies(): void
    {
        // US-005: As a regular family member, I should be blocked from viewing other members' policy details
        
        $familyMember = Customer::where('email', 'johnson.spouse@example.com')->first();
        $this->actingAs($familyMember, 'customer');
        
        $headPolicy = CustomerInsurance::where('policy_no', 'HEAD-LIFE-001')->first();
        
        // When: I try to directly access another family member's policy details
        $response = $this->get(route('customer.policies.detail', $headPolicy->id));
        
        // Then: I should be blocked with a 403 Forbidden response
        $response->assertStatus(403);
        
        // And: The unauthorized attempt should be logged
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $familyMember->id,
            'action' => 'view_policy_detail',
            'success' => false,
            'failure_reason' => 'Unauthorized access attempt'
        ]);
    }

    public function test_user_story_6_customer_without_family_cannot_access_family_features(): void
    {
        // US-006: As an independent customer (no family), I should not be able to access family features
        
        $independentCustomer = Customer::where('email', 'independent.customer@example.com')->first();
        $this->actingAs($independentCustomer, 'customer');
        
        // When: I try to access family policies page
        $response = $this->get(route('customer.policies'));
        
        // Then: I should be redirected to dashboard with an error message
        $response->assertRedirect(route('customer.dashboard'));
        $response->assertSessionHas('error', 'You do not have permission to view family policies.');
        
        // When: I view my dashboard
        $dashboardResponse = $this->get(route('customer.dashboard'));
        
        // Then: I should see individual customer interface, not family interface
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('My Account Dashboard');
        $dashboardResponse->assertDontSee('Family');
        $dashboardResponse->assertSee('Independent Customer');
    }

    public function test_user_story_7_policy_download_security_and_audit(): void
    {
        // US-007: As a family head, I want to download policy documents securely with proper audit trail
        
        $familyHead = Customer::where('email', 'johnson.head@example.com')->first();
        $this->actingAs($familyHead, 'customer');
        
        $policy = CustomerInsurance::where('policy_no', 'HEAD-LIFE-001')->first();
        $this->createMockPolicyDocument($policy);
        
        // When: I download a policy document
        $response = $this->get(route('customer.policies.download', $policy->id));
        
        // Then: The download should succeed
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        
        // And: The download should be logged with full details
        $auditLog = CustomerAuditLog::where([
            'customer_id' => $familyHead->id,
            'action' => 'download_policy',
            'resource_type' => 'policy',
            'resource_id' => $policy->id,
            'success' => true
        ])->first();
        
        $this->assertNotNull($auditLog);
        $this->assertStringContainsString('Policy document downloaded', $auditLog->description);
    }

    public function test_user_story_8_login_rate_limiting_protects_accounts(): void
    {
        // US-008: As a system user, I want protection against brute force attacks
        
        // When: Multiple failed login attempts are made
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post(route('customer.login'), [
                'email' => 'johnson.head@example.com',
                'password' => 'wrongpassword'
            ]);
        }
        
        // Then: Further login attempts should be rate limited
        $response = $this->post(route('customer.login'), [
            'email' => 'johnson.head@example.com',
            'password' => 'SecurePassword123!' // Even correct password should be blocked
        ]);
        
        $response->assertStatus(429); // Too Many Requests
        
        // And: Failed attempts should be logged
        $failedAttempts = CustomerAuditLog::where([
            'action' => 'login_failed',
            'success' => false
        ])->count();
        
        $this->assertGreaterThanOrEqual(6, $failedAttempts);
    }

    public function test_user_story_9_session_security_and_timeout(): void
    {
        // US-009: As a security-conscious user, I want my session to be secure and timeout appropriately
        
        $familyHead = Customer::where('email', 'johnson.head@example.com')->first();
        
        // Given: I am logged in
        $this->actingAs($familyHead, 'customer');
        
        // When: I access a protected page
        $response = $this->get(route('customer.dashboard'));
        $response->assertStatus(200);
        
        // Then: My session should have security headers
        $this->assertNotNull(session()->getId());
        
        // And: Logout should clear session and redirect properly
        $logoutResponse = $this->post(route('customer.logout'));
        $logoutResponse->assertRedirect(route('customer.login'));
        $logoutResponse->assertSessionHas('message', 'You have been logged out successfully.');
        $this->assertGuest('customer');
    }

    public function test_user_story_10_audit_trail_for_compliance(): void
    {
        // US-010: As a compliance officer, I want complete audit trails of all customer actions
        
        $familyHead = Customer::where('email', 'johnson.head@example.com')->first();
        
        // Given: A family head performs multiple actions
        $this->actingAs($familyHead, 'customer');
        
        // Login
        $this->post(route('customer.login'), [
            'email' => 'johnson.head@example.com',
            'password' => 'SecurePassword123!'
        ]);
        
        // View policies
        $this->get(route('customer.policies'));
        
        // View specific policy
        $policy = CustomerInsurance::where('policy_no', 'HEAD-LIFE-001')->first();
        $this->get(route('customer.policies.detail', $policy->id));
        
        // Logout
        $this->post(route('customer.logout'));
        
        // Then: All actions should be properly logged with required details
        $auditLogs = CustomerAuditLog::where('customer_id', $familyHead->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $this->assertGreaterThanOrEqual(4, $auditLogs->count());
        
        // Verify each log has required fields
        foreach ($auditLogs as $log) {
            $this->assertNotNull($log->customer_id);
            $this->assertNotNull($log->action);
            $this->assertNotNull($log->ip_address);
            $this->assertNotNull($log->session_id);
            $this->assertNotNull($log->created_at);
            $this->assertIsString($log->description);
            $this->assertIsBool($log->success);
        }
    }

    public function test_user_story_11_mobile_responsive_interface(): void
    {
        // US-011: As a mobile user, I want the interface to work well on my phone
        
        $familyHead = Customer::where('email', 'johnson.head@example.com')->first();
        $this->actingAs($familyHead, 'customer');
        
        // When: I access pages with mobile user agent
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X) AppleWebKit/605.1.15'
        ])->get(route('customer.dashboard'));
        
        // Then: Pages should load successfully
        $response->assertStatus(200);
        
        // And: Should contain mobile-responsive elements
        $response->assertSee('class="container"');
        $response->assertSee('responsive');
        
        // Policies page should also be mobile responsive
        $policiesResponse = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X) AppleWebKit/605.1.15'
        ])->get(route('customer.policies'));
        
        $policiesResponse->assertStatus(200);
    }

    public function test_user_story_12_privacy_data_protection(): void
    {
        // US-012: As a privacy-conscious customer, I want my personal data to be properly protected
        
        $familyHead = Customer::where('email', 'johnson.head@example.com')->first();
        
        // When: I request privacy-safe version of my data
        $safeData = $familyHead->getPrivacySafeData();
        
        // Then: Sensitive data should be properly masked
        $this->assertStringContainsString('*', $safeData['email']); // Email masked
        $this->assertStringContainsString('*', $safeData['mobile_number']); // Phone masked
        $this->assertStringNotContainsString($familyHead->date_of_birth->year, $safeData['date_of_birth']); // Year hidden
        
        // And: Full name should remain visible for identification
        $this->assertEquals($familyHead->name, $safeData['name']);
    }

    protected function createSampleFamilyData(): void
    {
        // Create insurance companies
        $lifeInsuranceCo = InsuranceCompany::create([
            'name' => 'Premier Life Insurance',
            'code' => 'PLIFE',
            'status' => true
        ]);

        $autoInsuranceCo = InsuranceCompany::create([
            'name' => 'Secure Auto Insurance',
            'code' => 'SAUTO',
            'status' => true
        ]);

        $healthInsuranceCo = InsuranceCompany::create([
            'name' => 'Secure Health Insurance',
            'code' => 'SHEALTH',
            'status' => true
        ]);

        // Create Johnson Family
        $johnsonFamily = FamilyGroup::create([
            'name' => 'Johnson Family',
            'status' => true,
            'created_by' => 1
        ]);

        // Create family head
        $familyHead = Customer::create([
            'name' => 'Johnson Family Head',
            'email' => 'johnson.head@example.com',
            'mobile_number' => '+1-555-0101',
            'date_of_birth' => '1980-05-15',
            'gender' => 'M',
            'password' => Hash::make('SecurePassword123!'),
            'family_group_id' => $johnsonFamily->id,
            'status' => true,
            'email_verified_at' => now()
        ]);

        // Create family members
        $spouse = Customer::create([
            'name' => 'Johnson Family Spouse',
            'email' => 'johnson.spouse@example.com',
            'mobile_number' => '+1-555-0102',
            'date_of_birth' => '1982-08-22',
            'gender' => 'F',
            'password' => Hash::make('SecurePassword123!'),
            'family_group_id' => $johnsonFamily->id,
            'status' => true,
            'email_verified_at' => now()
        ]);

        $child1 = Customer::create([
            'name' => 'Johnson Family Child',
            'email' => 'johnson.child1@example.com',
            'mobile_number' => '+1-555-0103',
            'date_of_birth' => '2010-12-10',
            'gender' => 'M',
            'password' => Hash::make('SecurePassword123!'),
            'family_group_id' => $johnsonFamily->id,
            'status' => true,
            'email_verified_at' => now()
        ]);

        // Set family head
        $johnsonFamily->update(['family_head_id' => $familyHead->id]);

        // Create family member relationships
        FamilyMember::create([
            'family_group_id' => $johnsonFamily->id,
            'customer_id' => $familyHead->id,
            'relationship' => 'head',
            'is_head' => true,
            'status' => true
        ]);

        FamilyMember::create([
            'family_group_id' => $johnsonFamily->id,
            'customer_id' => $spouse->id,
            'relationship' => 'spouse',
            'is_head' => false,
            'status' => true
        ]);

        FamilyMember::create([
            'family_group_id' => $johnsonFamily->id,
            'customer_id' => $child1->id,
            'relationship' => 'child',
            'is_head' => false,
            'status' => true
        ]);

        // Create insurance policies
        // Family Head's policies
        CustomerInsurance::create([
            'customer_id' => $familyHead->id,
            'insurance_company_id' => $lifeInsuranceCo->id,
            'policy_no' => 'HEAD-LIFE-001',
            'policy_name' => 'Life Protection Plus',
            'coverage_amount' => 1000000.00,
            'annual_premium' => 3600.00,
            'start_date' => '2023-01-01',
            'expired_date' => '2024-12-31',
            'status' => 'active',
            'policy_document_path' => 'policies/head-life-001.pdf'
        ]);

        CustomerInsurance::create([
            'customer_id' => $familyHead->id,
            'insurance_company_id' => $autoInsuranceCo->id,
            'policy_no' => 'HEAD-AUTO-002',
            'policy_name' => 'Auto Comprehensive Plus',
            'coverage_amount' => 100000.00,
            'annual_premium' => 1800.00,
            'start_date' => '2023-06-01',
            'expired_date' => '2024-05-31',
            'status' => 'active'
        ]);

        // Spouse's policies
        CustomerInsurance::create([
            'customer_id' => $spouse->id,
            'insurance_company_id' => $healthInsuranceCo->id,
            'policy_no' => 'SPOUSE-HEALTH-003',
            'policy_name' => 'Health Plus Gold Plan',
            'coverage_amount' => 500000.00,
            'annual_premium' => 2400.00,
            'start_date' => '2023-03-01',
            'expired_date' => '2024-02-29',
            'status' => 'active'
        ]);

        // Child's policies
        CustomerInsurance::create([
            'customer_id' => $child1->id,
            'insurance_company_id' => $healthInsuranceCo->id,
            'policy_no' => 'CHILD1-HEALTH-004',
            'policy_name' => 'Child Health Basic Plan',
            'coverage_amount' => 250000.00,
            'annual_premium' => 1200.00,
            'start_date' => '2023-09-01',
            'expired_date' => '2024-08-31',
            'status' => 'active'
        ]);

        // Create independent customer (no family)
        Customer::create([
            'name' => 'Independent Customer',
            'email' => 'independent.customer@example.com',
            'mobile_number' => '+1-555-9999',
            'date_of_birth' => '1975-03-20',
            'gender' => 'F',
            'password' => Hash::make('SecurePassword123!'),
            'family_group_id' => null,
            'status' => true,
            'email_verified_at' => now()
        ]);
    }

    protected function createMockPolicyDocument(CustomerInsurance $policy): void
    {
        $documentPath = storage_path('app/public/' . $policy->policy_document_path);
        $directory = dirname($documentPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($documentPath, '%PDF-1.4 Mock PDF Content for testing');
    }

    protected function tearDown(): void
    {
        // Clean up test files
        $testDir = storage_path('app/public/policies');
        if (is_dir($testDir)) {
            $files = glob("$testDir/*");
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        parent::tearDown();
    }
}