<?php

namespace Tests\Integration;

use App\Models\Customer;
use App\Models\CustomerInsurance;
use App\Models\InsuranceCompany;
use App\Models\PolicyType;
use App\Models\PremiumType;
use App\Models\FuelType;
use App\Models\Broker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerInsuranceWorkflowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Customer $customer;
    protected InsuranceCompany $insuranceCompany;
    protected PolicyType $policyType;
    protected PremiumType $premiumType;
    protected FuelType $fuelType;
    protected Broker $broker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user and authenticate
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        
        // Create supporting entities
        $this->customer = Customer::factory()->create();
        $this->insuranceCompany = InsuranceCompany::factory()->create();
        $this->policyType = PolicyType::factory()->create();
        $this->premiumType = PremiumType::factory()->create();
        $this->fuelType = FuelType::factory()->create();
        $this->broker = Broker::factory()->create();
    }

    public function test_complete_customer_insurance_creation_workflow()
    {
        // Step 1: Create a new customer insurance policy
        $policyData = [
            'customer_id' => $this->customer->id,
            'insurance_company_id' => $this->insuranceCompany->id,
            'policy_type_id' => $this->policyType->id,
            'premium_type_id' => $this->premiumType->id,
            'fuel_type_id' => $this->fuelType->id,
            'broker_id' => $this->broker->id,
            'policy_no' => 'POL-' . rand(100000, 999999),
            'sum_insured' => 500000,
            'premium_amount' => 15000,
            'start_date' => now()->format('Y-m-d'),
            'expired_date' => now()->addYear()->format('Y-m-d'),
            'status' => 1
        ];
        
        $response = $this->post(route('customer-insurances.store'), $policyData);
        
        $response->assertRedirect(route('customer-insurances.index'));
        $response->assertSessionHas('success');
        
        // Verify policy was created
        $this->assertDatabaseHas('customer_insurances', [
            'customer_id' => $this->customer->id,
            'policy_no' => $policyData['policy_no'],
            'sum_insured' => 500000
        ]);
        
        $policy = CustomerInsurance::where('policy_no', $policyData['policy_no'])->first();
        
        // Step 2: View the policy details
        $response = $this->get(route('customer-insurances.show', $policy));
        $response->assertStatus(200);
        $response->assertSee($policy->policy_no);
        $response->assertSee($this->customer->name);
        
        // Step 3: Update the policy
        $updateData = array_merge($policyData, [
            'sum_insured' => 750000,
            'premium_amount' => 18000
        ]);
        
        $response = $this->put(route('customer-insurances.update', $policy), $updateData);
        
        $response->assertRedirect(route('customer-insurances.index'));
        $response->assertSessionHas('success');
        
        // Verify updates
        $this->assertDatabaseHas('customer_insurances', [
            'id' => $policy->id,
            'sum_insured' => 750000,
            'premium_amount' => 18000
        ]);
        
        // Step 4: Change policy status
        $response = $this->patch(route('customer-insurances.updateStatus', [$policy, 0]));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('customer_insurances', [
            'id' => $policy->id,
            'status' => 0
        ]);
        
        // Step 5: Export policies
        $response = $this->get(route('customer-insurances.export'));
        
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_customer_insurance_with_family_group_workflow()
    {
        // Create family group
        $familyHead = Customer::factory()->create(['is_family_head' => true]);
        $familyMember = Customer::factory()->create([
            'family_group_id' => $familyHead->family_group_id
        ]);
        
        // Create policy for family head
        $policyData = [
            'customer_id' => $familyHead->id,
            'insurance_company_id' => $this->insuranceCompany->id,
            'policy_type_id' => $this->policyType->id,
            'premium_type_id' => $this->premiumType->id,
            'fuel_type_id' => $this->fuelType->id,
            'broker_id' => $this->broker->id,
            'policy_no' => 'FAM-' . rand(100000, 999999),
            'sum_insured' => 1000000,
            'premium_amount' => 25000,
            'start_date' => now()->format('Y-m-d'),
            'expired_date' => now()->addYear()->format('Y-m-d'),
            'status' => 1
        ];
        
        $response = $this->post(route('customer-insurances.store'), $policyData);
        
        $response->assertRedirect(route('customer-insurances.index'));
        
        // Verify family member can view family policies
        $policy = CustomerInsurance::where('policy_no', $policyData['policy_no'])->first();
        
        $this->assertNotNull($policy);
        $this->assertEquals($familyHead->id, $policy->customer_id);
    }

    public function test_policy_expiration_workflow()
    {
        // Create a policy that's about to expire
        $expiringPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->customer->id,
            'expired_date' => now()->addDays(15), // Expires in 15 days
            'status' => 1
        ]);
        
        // Test filtering for expiring policies
        $response = $this->get(route('customer-insurances.index', [
            'expiring_soon' => 'yes'
        ]));
        
        $response->assertStatus(200);
        $response->assertSee($expiringPolicy->policy_no);
        
        // Test renewal workflow
        $renewalData = [
            'customer_id' => $this->customer->id,
            'insurance_company_id' => $expiringPolicy->insurance_company_id,
            'policy_type_id' => $expiringPolicy->policy_type_id,
            'premium_type_id' => $expiringPolicy->premium_type_id,
            'fuel_type_id' => $expiringPolicy->fuel_type_id,
            'broker_id' => $expiringPolicy->broker_id,
            'policy_no' => 'REN-' . $expiringPolicy->policy_no,
            'sum_insured' => $expiringPolicy->sum_insured,
            'premium_amount' => $expiringPolicy->premium_amount * 1.1, // 10% increase
            'start_date' => $expiringPolicy->expired_date,
            'expired_date' => now()->addYear()->addDays(15)->format('Y-m-d'),
            'status' => 1
        ];
        
        $response = $this->post(route('customer-insurances.store'), $renewalData);
        
        $response->assertRedirect(route('customer-insurances.index'));
        $response->assertSessionHas('success');
        
        // Verify renewal policy created
        $this->assertDatabaseHas('customer_insurances', [
            'policy_no' => $renewalData['policy_no'],
            'customer_id' => $this->customer->id
        ]);
    }

    public function test_bulk_operations_workflow()
    {
        // Create multiple policies
        $policies = CustomerInsurance::factory()->count(5)->create([
            'status' => 1
        ]);
        
        // Test bulk status update (this would be implemented in the controller)
        foreach ($policies as $policy) {
            $response = $this->patch(route('customer-insurances.updateStatus', [$policy, 0]));
            $response->assertRedirect();
        }
        
        // Verify all policies are now inactive
        foreach ($policies as $policy) {
            $this->assertDatabaseHas('customer_insurances', [
                'id' => $policy->id,
                'status' => 0
            ]);
        }
        
        // Test bulk export
        $response = $this->get(route('customer-insurances.export'));
        
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_search_and_filter_workflow()
    {
        // Create policies with different characteristics
        $motorPolicy = CustomerInsurance::factory()->create([
            'policy_type_id' => PolicyType::factory()->create(['name' => 'Motor Insurance'])->id,
            'policy_no' => 'MOTOR-123456',
            'status' => 1
        ]);
        
        $healthPolicy = CustomerInsurance::factory()->create([
            'policy_type_id' => PolicyType::factory()->create(['name' => 'Health Insurance'])->id,
            'policy_no' => 'HEALTH-789012',
            'status' => 1
        ]);
        
        // Test search by policy number
        $response = $this->get(route('customer-insurances.index', [
            'search' => 'MOTOR-123456'
        ]));
        
        $response->assertStatus(200);
        $response->assertSee('MOTOR-123456');
        $response->assertDontSee('HEALTH-789012');
        
        // Test filter by policy type
        $response = $this->get(route('customer-insurances.index', [
            'policy_type' => $motorPolicy->policy_type_id
        ]));
        
        $response->assertStatus(200);
        $response->assertSee('MOTOR-123456');
        $response->assertDontSee('HEALTH-789012');
        
        // Test filter by status
        $inactivePolicy = CustomerInsurance::factory()->create(['status' => 0]);
        
        $response = $this->get(route('customer-insurances.index', [
            'status' => 0
        ]));
        
        $response->assertStatus(200);
        $response->assertSee($inactivePolicy->policy_no);
    }
}