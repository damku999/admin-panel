<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\FamilyGroup;
use App\Models\FamilyMember;
use App\Models\CustomerInsurance;
use App\Models\CustomerAuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create active family group
        $this->familyGroup = FamilyGroup::factory()->create([
            'name' => 'Test Family',
            'status' => true
        ]);
        
        // Create family head
        $this->familyHead = Customer::factory()->create([
            'email' => 'head@example.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'family_group_id' => $this->familyGroup->id
        ]);
        
        FamilyMember::create([
            'family_group_id' => $this->familyGroup->id,
            'customer_id' => $this->familyHead->id,
            'relationship' => 'head',
            'is_head' => true,
            'status' => true
        ]);
        
        $this->familyGroup->update(['family_head_id' => $this->familyHead->id]);
        
        // Create regular family member
        $this->familyMember = Customer::factory()->create([
            'email' => 'member@example.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'family_group_id' => $this->familyGroup->id
        ]);
        
        FamilyMember::create([
            'family_group_id' => $this->familyGroup->id,
            'customer_id' => $this->familyMember->id,
            'relationship' => 'spouse',
            'is_head' => false,
            'status' => true
        ]);
        
        // Create independent customer (no family)
        $this->independentCustomer = Customer::factory()->create([
            'email' => 'independent@example.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'family_group_id' => null
        ]);
        
        // Create insurance policies
        $this->headPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->familyHead->id,
            'policy_no' => 'POL001',
            'policy_document_path' => 'documents/pol001.pdf'
        ]);
        
        $this->memberPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->familyMember->id,
            'policy_no' => 'POL002',
            'policy_document_path' => 'documents/pol002.pdf'
        ]);
        
        $this->independentPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->independentCustomer->id,
            'policy_no' => 'POL003'
        ]);
    }

    public function test_family_head_can_access_policies_page(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        $response = $this->get(route('customer.policies'));
        
        $response->assertStatus(200);
        $response->assertViewIs('customer.policies');
        $response->assertSee('Family Insurance Policies');
        
        // Should log the access
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $this->familyHead->id,
            'action' => 'view_policies',
            'success' => true
        ]);
    }

    public function test_family_member_can_access_policies_page(): void
    {
        $this->actingAs($this->familyMember, 'customer');
        
        $response = $this->get(route('customer.policies'));
        
        $response->assertStatus(200);
        $response->assertViewIs('customer.policies');
    }

    public function test_independent_customer_cannot_access_policies_page(): void
    {
        $this->actingAs($this->independentCustomer, 'customer');
        
        $response = $this->get(route('customer.policies'));
        
        $response->assertRedirect(route('customer.dashboard'));
        $response->assertSessionHas('error', 'You do not have permission to view family policies.');
    }

    public function test_family_head_can_view_all_family_policies(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        $response = $this->get(route('customer.policies'));
        
        $response->assertSee('POL001'); // Own policy
        $response->assertSee('POL002'); // Family member's policy
    }

    public function test_family_member_can_only_view_own_policies(): void
    {
        $this->actingAs($this->familyMember, 'customer');
        
        $response = $this->get(route('customer.policies'));
        
        $response->assertDontSee('POL001'); // Head's policy
        $response->assertSee('POL002');     // Own policy
    }

    public function test_family_head_can_view_specific_family_policy_detail(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        $response = $this->get(route('customer.policies.detail', $this->memberPolicy->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('customer.policy-detail');
        $response->assertSee($this->memberPolicy->policy_no);
        
        // Should log policy detail access
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $this->familyHead->id,
            'action' => 'view_policy_detail',
            'resource_type' => 'policy',
            'resource_id' => $this->memberPolicy->id,
            'success' => true
        ]);
    }

    public function test_family_member_cannot_view_other_family_member_policy_detail(): void
    {
        $this->actingAs($this->familyMember, 'customer');
        
        $response = $this->get(route('customer.policies.detail', $this->headPolicy->id));
        
        $response->assertStatus(403);
        
        // Should log unauthorized access attempt
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $this->familyMember->id,
            'action' => 'view_policy_detail',
            'success' => false,
            'failure_reason' => 'Unauthorized access attempt'
        ]);
    }

    public function test_family_member_can_view_own_policy_detail(): void
    {
        $this->actingAs($this->familyMember, 'customer');
        
        $response = $this->get(route('customer.policies.detail', $this->memberPolicy->id));
        
        $response->assertStatus(200);
        $response->assertSee($this->memberPolicy->policy_no);
    }

    public function test_customer_cannot_view_outside_family_policy(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        $response = $this->get(route('customer.policies.detail', $this->independentPolicy->id));
        
        $response->assertStatus(403);
    }

    public function test_family_head_can_download_family_policy_documents(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Mock file existence
        $this->mockPolicyDocumentExists($this->memberPolicy);
        
        $response = $this->get(route('customer.policies.download', $this->memberPolicy->id));
        
        $response->assertStatus(200);
        
        // Should log download
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $this->familyHead->id,
            'action' => 'download_policy',
            'resource_type' => 'policy',
            'resource_id' => $this->memberPolicy->id,
            'success' => true
        ]);
    }

    public function test_family_member_cannot_download_other_family_member_policy_documents(): void
    {
        $this->actingAs($this->familyMember, 'customer');
        
        $response = $this->get(route('customer.policies.download', $this->headPolicy->id));
        
        $response->assertStatus(403);
        
        // Should log unauthorized download attempt
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $this->familyMember->id,
            'action' => 'download_policy',
            'success' => false
        ]);
    }

    public function test_customer_with_inactive_family_group_is_blocked(): void
    {
        $this->familyGroup->update(['status' => false]);
        
        $this->actingAs($this->familyHead, 'customer');
        
        $response = $this->get(route('customer.policies'));
        
        $response->assertRedirect(route('customer.dashboard'));
        $response->assertSessionHas('error', 'Your family group is currently inactive.');
    }

    public function test_inactive_customer_session_is_terminated(): void
    {
        $this->familyHead->update(['status' => false]);
        
        $this->actingAs($this->familyHead, 'customer');
        
        $response = $this->get(route('customer.policies'));
        
        $response->assertRedirect(route('customer.login'));
        $response->assertSessionHas('error', 'Your account has been deactivated.');
    }

    public function test_rate_limiting_on_policy_downloads(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        $this->mockPolicyDocumentExists($this->headPolicy);
        
        // Make multiple download requests
        for ($i = 0; $i < 11; $i++) {
            $response = $this->get(route('customer.policies.download', $this->headPolicy->id));
        }
        
        // 11th request should be rate limited
        $response = $this->get(route('customer.policies.download', $this->headPolicy->id));
        $response->assertStatus(429); // Too Many Requests
    }

    public function test_customer_policy_access_through_gate(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Family head should be able to view family member's policy
        $this->assertTrue($this->familyHead->can('viewPolicy', $this->memberPolicy));
        $this->assertTrue($this->familyHead->can('downloadPolicy', $this->memberPolicy));
        
        // Family head should be able to view own policy
        $this->assertTrue($this->familyHead->can('viewPolicy', $this->headPolicy));
        
        // Family head should NOT be able to view outside policy
        $this->assertFalse($this->familyHead->can('viewPolicy', $this->independentPolicy));
    }

    public function test_family_member_policy_access_through_gate(): void
    {
        $this->actingAs($this->familyMember, 'customer');
        
        // Family member should be able to view own policy
        $this->assertTrue($this->familyMember->can('viewPolicy', $this->memberPolicy));
        $this->assertTrue($this->familyMember->can('downloadPolicy', $this->memberPolicy));
        
        // Family member should NOT be able to view family head's policy
        $this->assertFalse($this->familyMember->can('viewPolicy', $this->headPolicy));
        
        // Family member should NOT be able to view outside policy
        $this->assertFalse($this->familyMember->can('viewPolicy', $this->independentPolicy));
    }

    public function test_independent_customer_policy_access_through_gate(): void
    {
        $this->actingAs($this->independentCustomer, 'customer');
        
        // Independent customer should be able to view own policy
        $this->assertTrue($this->independentCustomer->can('viewPolicy', $this->independentPolicy));
        
        // Independent customer should NOT be able to view family policies
        $this->assertFalse($this->independentCustomer->can('viewPolicy', $this->headPolicy));
        $this->assertFalse($this->independentCustomer->can('viewPolicy', $this->memberPolicy));
    }

    public function test_family_data_viewing_authorization(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        $this->assertTrue($this->familyHead->can('viewFamilyData'));
        
        $this->actingAs($this->familyMember, 'customer');
        $this->assertTrue($this->familyMember->can('viewFamilyData'));
        
        $this->actingAs($this->independentCustomer, 'customer');
        $this->assertFalse($this->independentCustomer->can('viewFamilyData'));
    }

    public function test_all_family_policies_viewing_authorization(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        $this->assertTrue($this->familyHead->can('viewAllFamilyPolicies'));
        
        $this->actingAs($this->familyMember, 'customer');
        $this->assertFalse($this->familyMember->can('viewAllFamilyPolicies'));
        
        $this->actingAs($this->independentCustomer, 'customer');
        $this->assertFalse($this->independentCustomer->can('viewAllFamilyPolicies'));
    }

    public function test_password_change_authorization(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        $this->assertTrue($this->familyHead->can('changePassword'));
        
        // Inactive customer should not be able to change password
        $this->familyHead->update(['status' => false]);
        $this->assertFalse($this->familyHead->can('changePassword'));
    }

    public function test_customer_profile_viewing_authorization(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Can view own profile
        $this->assertTrue($this->familyHead->can('view', $this->familyHead));
        
        // Family head can view family member profile
        $this->assertTrue($this->familyHead->can('view', $this->familyMember));
        
        // Cannot view outside customer profile
        $this->assertFalse($this->familyHead->can('view', $this->independentCustomer));
    }

    public function test_regular_member_profile_viewing_authorization(): void
    {
        $this->actingAs($this->familyMember, 'customer');
        
        // Can view own profile
        $this->assertTrue($this->familyMember->can('view', $this->familyMember));
        
        // Regular member cannot view other family member profiles
        $this->assertFalse($this->familyMember->can('view', $this->familyHead));
        
        // Cannot view outside customer profile
        $this->assertFalse($this->familyMember->can('view', $this->independentCustomer));
    }

    public function test_middleware_chain_blocks_unauthorized_access(): void
    {
        // Test without family access middleware
        $customerWithoutFamily = Customer::factory()->create([
            'email' => 'nofamily@example.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'family_group_id' => null
        ]);
        
        $this->actingAs($customerWithoutFamily, 'customer');
        
        $response = $this->get(route('customer.policies'));
        
        $response->assertRedirect(route('customer.dashboard'));
        $response->assertSessionHas('warning', 'You need to be part of a family group to access this feature.');
    }

    public function test_audit_log_captures_unauthorized_access_attempts(): void
    {
        $this->actingAs($this->familyMember, 'customer');
        
        // Try to access unauthorized policy
        $response = $this->get(route('customer.policies.detail', $this->headPolicy->id));
        
        $auditLog = CustomerAuditLog::where([
            'customer_id' => $this->familyMember->id,
            'action' => 'view_policy_detail'
        ])->latest()->first();
        
        $this->assertNotNull($auditLog);
        $this->assertFalse($auditLog->success);
        $this->assertEquals('Unauthorized access attempt', $auditLog->failure_reason);
        $this->assertEquals('policy', $auditLog->resource_type);
        $this->assertEquals($this->headPolicy->id, $auditLog->resource_id);
    }

    protected function mockPolicyDocumentExists($policy): void
    {
        // Create the mock file path for testing
        $filePath = storage_path('app/public/' . $policy->policy_document_path);
        $directory = dirname($filePath);
        
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        file_put_contents($filePath, 'Mock PDF content');
    }

    protected function tearDown(): void
    {
        // Clean up test files
        $testDir = storage_path('app/public/documents');
        if (is_dir($testDir)) {
            array_map('unlink', glob("$testDir/*"));
        }
        
        parent::tearDown();
    }
}