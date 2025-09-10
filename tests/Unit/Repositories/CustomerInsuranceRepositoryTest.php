<?php

namespace Tests\Unit\Repositories;

use App\Contracts\Repositories\CustomerInsuranceRepositoryInterface;
use App\Models\CustomerInsurance;
use App\Models\Customer;
use App\Models\InsuranceCompany;
use App\Models\PremiumType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;
use Carbon\Carbon;

class CustomerInsuranceRepositoryTest extends TestCase
{
    use RefreshDatabase;
    
    private CustomerInsuranceRepositoryInterface $repository;
    private Customer $customer;
    private InsuranceCompany $insuranceCompany;
    private PremiumType $premiumType;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = app(CustomerInsuranceRepositoryInterface::class);
        
        // Create test data
        $this->customer = Customer::factory()->create();
        $this->insuranceCompany = InsuranceCompany::factory()->create();
        $this->premiumType = PremiumType::factory()->create();
    }

    public function test_get_paginated_returns_paginated_results()
    {
        // Arrange
        CustomerInsurance::factory()->count(15)->create();
        $request = new Request();

        // Act
        $result = $this->repository->getPaginated($request);

        // Assert
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(15, $result->total());
        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $result);
    }

    public function test_get_paginated_with_search_filters_results()
    {
        // Arrange
        $matchingInsurance = CustomerInsurance::factory()->create([
            'policy_no' => 'POLICY123',
            'customer_id' => $this->customer->id
        ]);
        
        CustomerInsurance::factory()->create(['policy_no' => 'DIFFERENT456']);
        
        $request = new Request(['search' => 'POLICY123']);

        // Act
        $result = $this->repository->getPaginated($request);

        // Assert
        $this->assertEquals(1, $result->total());
        $this->assertEquals($matchingInsurance->id, $result->first()->id);
    }

    public function test_create_customer_insurance_successfully()
    {
        // Arrange
        $data = [
            'customer_id' => $this->customer->id,
            'insurance_company_id' => $this->insuranceCompany->id,
            'premium_type_id' => $this->premiumType->id,
            'policy_no' => 'POL-2024-001',
            'registration_no' => 'MH01AB1234',
            'start_date' => '2024-01-01',
            'expired_date' => '2025-01-01',
            'premium_amount' => 25000.00,
            'final_premium_with_gst' => 29500.00,
            'status' => 1
        ];

        // Act
        $customerInsurance = $this->repository->create($data);

        // Assert
        $this->assertInstanceOf(CustomerInsurance::class, $customerInsurance);
        $this->assertEquals($data['policy_no'], $customerInsurance->policy_no);
        $this->assertEquals($data['customer_id'], $customerInsurance->customer_id);
        $this->assertDatabaseHas('customer_insurances', [
            'policy_no' => 'POL-2024-001',
            'customer_id' => $this->customer->id
        ]);
    }

    public function test_update_customer_insurance_successfully()
    {
        // Arrange
        $customerInsurance = CustomerInsurance::factory()->create([
            'policy_no' => 'OLD-POLICY',
            'premium_amount' => 20000.00
        ]);

        $updateData = [
            'policy_no' => 'NEW-POLICY',
            'premium_amount' => 30000.00
        ];

        // Act
        $updated = $this->repository->update($customerInsurance, $updateData);

        // Assert
        $this->assertEquals('NEW-POLICY', $updated->policy_no);
        $this->assertEquals(30000.00, $updated->premium_amount);
        $this->assertDatabaseHas('customer_insurances', [
            'id' => $customerInsurance->id,
            'policy_no' => 'NEW-POLICY',
            'premium_amount' => 30000.00
        ]);
    }

    public function test_delete_customer_insurance_successfully()
    {
        // Arrange
        $customerInsurance = CustomerInsurance::factory()->create();

        // Act
        $result = $this->repository->delete($customerInsurance);

        // Assert
        $this->assertTrue($result);
        $this->assertSoftDeleted('customer_insurances', ['id' => $customerInsurance->id]);
    }

    public function test_find_by_id_returns_customer_insurance()
    {
        // Arrange
        $customerInsurance = CustomerInsurance::factory()->create();

        // Act
        $found = $this->repository->findById($customerInsurance->id);

        // Assert
        $this->assertInstanceOf(CustomerInsurance::class, $found);
        $this->assertEquals($customerInsurance->id, $found->id);
    }

    public function test_find_by_id_returns_null_for_non_existent()
    {
        // Act
        $found = $this->repository->findById(99999);

        // Assert
        $this->assertNull($found);
    }

    public function test_update_status_successfully()
    {
        // Arrange
        $customerInsurance = CustomerInsurance::factory()->create(['status' => 1]);

        // Act
        $result = $this->repository->updateStatus($customerInsurance->id, 0);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('customer_insurances', [
            'id' => $customerInsurance->id,
            'status' => 0
        ]);
    }

    public function test_get_by_customer_id_returns_customer_policies()
    {
        // Arrange
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();
        
        CustomerInsurance::factory()->count(3)->create(['customer_id' => $customer1->id]);
        CustomerInsurance::factory()->count(2)->create(['customer_id' => $customer2->id]);

        // Act
        $customer1Policies = $this->repository->getByCustomerId($customer1->id);

        // Assert
        $this->assertCount(3, $customer1Policies);
        $customer1Policies->each(function ($policy) use ($customer1) {
            $this->assertEquals($customer1->id, $policy->customer_id);
        });
    }

    public function test_get_all_for_export_returns_all_records()
    {
        // Arrange
        CustomerInsurance::factory()->count(5)->create();

        // Act
        $result = $this->repository->getAllForExport();

        // Assert
        $this->assertCount(5, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_expiring_policies_returns_policies_expiring_soon()
    {
        // Arrange
        $expiringIn15Days = CustomerInsurance::factory()->create([
            'expired_date' => Carbon::now()->addDays(15)->format('Y-m-d'),
            'status' => 1
        ]);

        $expiringIn45Days = CustomerInsurance::factory()->create([
            'expired_date' => Carbon::now()->addDays(45)->format('Y-m-d'),
            'status' => 1
        ]);

        $expiredPolicy = CustomerInsurance::factory()->create([
            'expired_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
            'status' => 1
        ]);

        // Act - Get policies expiring within 30 days
        $expiringPolicies = $this->repository->getExpiringPolicies(30);

        // Assert
        $this->assertCount(1, $expiringPolicies);
        $this->assertEquals($expiringIn15Days->id, $expiringPolicies->first()->id);
    }

    public function test_find_with_relations_loads_specified_relationships()
    {
        // Arrange
        $customerInsurance = CustomerInsurance::factory()->create([
            'customer_id' => $this->customer->id,
            'insurance_company_id' => $this->insuranceCompany->id
        ]);

        // Act
        $found = $this->repository->findWithRelations(
            $customerInsurance->id, 
            ['customer', 'insuranceCompany']
        );

        // Assert
        $this->assertInstanceOf(CustomerInsurance::class, $found);
        $this->assertTrue($found->relationLoaded('customer'));
        $this->assertTrue($found->relationLoaded('insuranceCompany'));
        $this->assertEquals($this->customer->id, $found->customer->id);
        $this->assertEquals($this->insuranceCompany->id, $found->insuranceCompany->id);
    }

    public function test_repository_handles_database_constraints()
    {
        // Test foreign key constraint handling
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Try to create insurance with non-existent customer
        $this->repository->create([
            'customer_id' => 99999,
            'policy_no' => 'TEST-POLICY',
            'start_date' => '2024-01-01',
            'expired_date' => '2025-01-01',
            'status' => 1
        ]);
    }

    public function test_repository_respects_soft_deletes()
    {
        // Arrange
        $customerInsurance = CustomerInsurance::factory()->create();
        
        // Act - Soft delete
        $this->repository->delete($customerInsurance);
        
        // Assert - Soft deleted record not included in normal queries
        $found = $this->repository->findById($customerInsurance->id);
        $this->assertNull($found);
        
        // Assert - But exists in database with deleted_at timestamp
        $this->assertDatabaseHas('customer_insurances', [
            'id' => $customerInsurance->id
        ]);
    }

    public function test_pagination_with_sorting()
    {
        // Arrange
        CustomerInsurance::factory()->create(['policy_no' => 'Z-POLICY', 'created_at' => Carbon::now()->subDay()]);
        CustomerInsurance::factory()->create(['policy_no' => 'A-POLICY', 'created_at' => Carbon::now()]);
        
        $request = new Request([
            'sort' => 'policy_no',
            'direction' => 'asc'
        ]);

        // Act
        $result = $this->repository->getPaginated($request);

        // Assert
        $policies = $result->items();
        $this->assertEquals('A-POLICY', $policies[0]->policy_no);
        $this->assertEquals('Z-POLICY', $policies[1]->policy_no);
    }
}