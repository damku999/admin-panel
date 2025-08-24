<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\FamilyGroup;
use App\Models\FamilyMember;
use App\Models\CustomerInsurance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test family group
        $this->familyGroup = FamilyGroup::factory()->create([
            'name' => 'Test Family',
            'status' => true
        ]);
        
        // Create family head
        $this->familyHead = Customer::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'family_group_id' => $this->familyGroup->id,
            'status' => true
        ]);
        
        // Create family member
        $this->familyMember = FamilyMember::create([
            'family_group_id' => $this->familyGroup->id,
            'customer_id' => $this->familyHead->id,
            'relationship' => 'head',
            'is_head' => true,
            'status' => true
        ]);
        
        // Update family group with head
        $this->familyGroup->update(['family_head_id' => $this->familyHead->id]);
        
        // Create regular family member
        $this->regularMember = Customer::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'family_group_id' => $this->familyGroup->id,
            'status' => true
        ]);
        
        FamilyMember::create([
            'family_group_id' => $this->familyGroup->id,
            'customer_id' => $this->regularMember->id,
            'relationship' => 'spouse',
            'is_head' => false,
            'status' => true
        ]);
    }

    public function test_customer_has_family_relationship(): void
    {
        $this->assertTrue($this->familyHead->hasFamily());
        $this->assertTrue($this->regularMember->hasFamily());
        
        $customerWithoutFamily = Customer::factory()->create(['family_group_id' => null]);
        $this->assertFalse($customerWithoutFamily->hasFamily());
    }

    public function test_customer_is_family_head_detection(): void
    {
        $this->assertTrue($this->familyHead->isFamilyHead());
        $this->assertFalse($this->regularMember->isFamilyHead());
        
        $customerWithoutFamily = Customer::factory()->create(['family_group_id' => null]);
        $this->assertFalse($customerWithoutFamily->isFamilyHead());
    }

    public function test_customers_in_same_family_detection(): void
    {
        $this->assertTrue($this->familyHead->isInSameFamilyAs($this->regularMember));
        $this->assertTrue($this->regularMember->isInSameFamilyAs($this->familyHead));
        
        $outsideCustomer = Customer::factory()->create(['family_group_id' => null]);
        $this->assertFalse($this->familyHead->isInSameFamilyAs($outsideCustomer));
        $this->assertFalse($outsideCustomer->isInSameFamilyAs($this->familyHead));
    }

    public function test_email_masking_for_privacy(): void
    {
        $customer = Customer::factory()->create(['email' => 'testuser@example.com']);
        
        $reflection = new \ReflectionClass($customer);
        $method = $reflection->getMethod('maskEmail');
        $method->setAccessible(true);
        
        $maskedEmail = $method->invoke($customer, 'testuser@example.com');
        $this->assertEquals('te******@example.com', $maskedEmail);
        
        // Test short email
        $shortMasked = $method->invoke($customer, 'ab@example.com');
        $this->assertEquals('ab@example.com', $shortMasked);
        
        // Test null email
        $nullMasked = $method->invoke($customer, null);
        $this->assertNull($nullMasked);
    }

    public function test_mobile_masking_for_privacy(): void
    {
        $customer = Customer::factory()->create();
        
        $reflection = new \ReflectionClass($customer);
        $method = $reflection->getMethod('maskMobile');
        $method->setAccessible(true);
        
        $maskedMobile = $method->invoke($customer, '1234567890');
        $this->assertEquals('12******90', $maskedMobile);
        
        // Test short mobile
        $shortMasked = $method->invoke($customer, '123');
        $this->assertEquals('123', $shortMasked);
        
        // Test null mobile
        $nullMasked = $method->invoke($customer, null);
        $this->assertNull($nullMasked);
    }

    public function test_privacy_safe_data_generation(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'mobile_number' => '1234567890',
            'date_of_birth' => '1990-05-15'
        ]);
        
        $safeData = $customer->getPrivacySafeData();
        
        $this->assertArrayHasKey('name', $safeData);
        $this->assertArrayHasKey('email', $safeData);
        $this->assertArrayHasKey('mobile_number', $safeData);
        $this->assertArrayHasKey('date_of_birth', $safeData);
        
        $this->assertEquals('Test User', $safeData['name']);
        $this->assertEquals('te******@example.com', $safeData['email']);
        $this->assertEquals('12******90', $safeData['mobile_number']);
        $this->assertEquals('May 15', $safeData['date_of_birth']); // Year hidden
    }

    public function test_sensitive_data_viewing_authorization(): void
    {
        // Customer can view own data
        $this->assertTrue($this->familyHead->canViewSensitiveDataOf($this->familyHead));
        $this->assertTrue($this->regularMember->canViewSensitiveDataOf($this->regularMember));
        
        // Family head can view family member data
        $this->assertTrue($this->familyHead->canViewSensitiveDataOf($this->regularMember));
        
        // Regular member cannot view other family member data
        $this->assertFalse($this->regularMember->canViewSensitiveDataOf($this->familyHead));
        
        // Outside customer cannot view family data
        $outsideCustomer = Customer::factory()->create(['family_group_id' => null]);
        $this->assertFalse($outsideCustomer->canViewSensitiveDataOf($this->familyHead));
        $this->assertFalse($this->familyHead->canViewSensitiveDataOf($outsideCustomer));
    }

    public function test_password_change_requirement_detection(): void
    {
        $customer = Customer::factory()->create(['must_change_password' => true]);
        $this->assertTrue($customer->needsPasswordChange());
        
        $customer->update(['must_change_password' => false]);
        $this->assertFalse($customer->needsPasswordChange());
        
        $customer->update(['must_change_password' => null]);
        $this->assertFalse($customer->needsPasswordChange());
    }

    public function test_viewable_insurance_for_family_head(): void
    {
        // Create insurance policies for both customers
        $headPolicy = CustomerInsurance::factory()->create(['customer_id' => $this->familyHead->id]);
        $memberPolicy = CustomerInsurance::factory()->create(['customer_id' => $this->regularMember->id]);
        
        // Family head should be able to view all family policies
        $viewablePolicies = $this->familyHead->getViewableInsurance()->get();
        $this->assertCount(2, $viewablePolicies);
        
        $policyIds = $viewablePolicies->pluck('id')->toArray();
        $this->assertContains($headPolicy->id, $policyIds);
        $this->assertContains($memberPolicy->id, $policyIds);
    }

    public function test_viewable_insurance_for_regular_member(): void
    {
        // Create insurance policies for both customers
        $headPolicy = CustomerInsurance::factory()->create(['customer_id' => $this->familyHead->id]);
        $memberPolicy = CustomerInsurance::factory()->create(['customer_id' => $this->regularMember->id]);
        
        // Regular member should only see their own policies
        $viewablePolicies = $this->regularMember->getViewableInsurance()->get();
        $this->assertCount(1, $viewablePolicies);
        
        $policyIds = $viewablePolicies->pluck('id')->toArray();
        $this->assertNotContains($headPolicy->id, $policyIds);
        $this->assertContains($memberPolicy->id, $policyIds);
    }

    public function test_customer_without_family_has_no_viewable_policies(): void
    {
        $outsideCustomer = Customer::factory()->create(['family_group_id' => null]);
        CustomerInsurance::factory()->create(['customer_id' => $outsideCustomer->id]);
        
        // Customer without family should see their own policies
        $viewablePolicies = $outsideCustomer->getViewableInsurance()->get();
        $this->assertCount(1, $viewablePolicies);
    }

    public function test_family_group_relationship(): void
    {
        $this->assertInstanceOf(FamilyGroup::class, $this->familyHead->familyGroup);
        $this->assertEquals($this->familyGroup->id, $this->familyHead->familyGroup->id);
    }

    public function test_family_member_relationship(): void
    {
        $this->assertInstanceOf(FamilyMember::class, $this->familyHead->familyMember);
        $this->assertTrue($this->familyHead->familyMember->is_head);
        
        $this->assertInstanceOf(FamilyMember::class, $this->regularMember->familyMember);
        $this->assertFalse($this->regularMember->familyMember->is_head);
    }

    public function test_family_members_collection(): void
    {
        $familyMembers = $this->familyHead->familyMembers;
        $this->assertCount(2, $familyMembers); // Head + regular member
        
        $customerIds = $familyMembers->pluck('customer_id')->toArray();
        $this->assertContains($this->familyHead->id, $customerIds);
        $this->assertContains($this->regularMember->id, $customerIds);
    }
}