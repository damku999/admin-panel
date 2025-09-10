<?php

namespace Tests\Unit\Repositories;

use App\Contracts\Repositories\QuotationRepositoryInterface;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\QuotationCompany;
use App\Models\InsuranceCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;
use Carbon\Carbon;

class QuotationRepositoryTest extends TestCase
{
    use RefreshDatabase;
    
    private QuotationRepositoryInterface $repository;
    private Customer $customer;
    private InsuranceCompany $insuranceCompany;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = app(QuotationRepositoryInterface::class);
        
        // Create test data
        $this->customer = Customer::factory()->create();
        $this->insuranceCompany = InsuranceCompany::factory()->create();
    }

    public function test_get_paginated_returns_paginated_quotations()
    {
        // Arrange
        Quotation::factory()->count(15)->create();
        $request = new Request();

        // Act
        $result = $this->repository->getPaginated($request);

        // Assert
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(15, $result->total());
        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $result);
    }

    public function test_get_paginated_with_customer_filter()
    {
        // Arrange
        $targetCustomer = Customer::factory()->create();
        $matchingQuotations = Quotation::factory()->count(3)->create([
            'customer_id' => $targetCustomer->id
        ]);
        
        Quotation::factory()->count(2)->create(); // Different customers
        
        $request = new Request(['customer_id' => $targetCustomer->id]);

        // Act
        $result = $this->repository->getPaginated($request);

        // Assert
        $this->assertEquals(3, $result->total());
        $result->each(function ($quotation) use ($targetCustomer) {
            $this->assertEquals($targetCustomer->id, $quotation->customer_id);
        });
    }

    public function test_get_paginated_with_status_filter()
    {
        // Arrange
        Quotation::factory()->count(2)->create(['status' => 'Draft']);
        Quotation::factory()->count(3)->create(['status' => 'Generated']);
        Quotation::factory()->count(1)->create(['status' => 'Sent']);
        
        $request = new Request(['status' => 'Generated']);

        // Act
        $result = $this->repository->getPaginated($request);

        // Assert
        $this->assertEquals(3, $result->total());
        $result->each(function ($quotation) {
            $this->assertEquals('Generated', $quotation->status);
        });
    }

    public function test_create_quotation_successfully()
    {
        // Arrange
        $data = [
            'customer_id' => $this->customer->id,
            'quotation_no' => 'QUO-2024-001',
            'quotation_date' => '2024-01-15',
            'vehicle_make_model' => 'Honda City',
            'registration_no' => 'MH01AB1234',
            'fuel_type' => 'Petrol',
            'policy_type' => 'Comprehensive',
            'status' => 'Draft',
            'total_premium' => 25000.00
        ];

        // Act
        $quotation = $this->repository->create($data);

        // Assert
        $this->assertInstanceOf(Quotation::class, $quotation);
        $this->assertEquals($data['quotation_no'], $quotation->quotation_no);
        $this->assertEquals($data['customer_id'], $quotation->customer_id);
        $this->assertDatabaseHas('quotations', [
            'quotation_no' => 'QUO-2024-001',
            'customer_id' => $this->customer->id
        ]);
    }

    public function test_update_quotation_successfully()
    {
        // Arrange
        $quotation = Quotation::factory()->create([
            'status' => 'Draft',
            'total_premium' => 20000.00
        ]);

        $updateData = [
            'status' => 'Generated',
            'total_premium' => 25000.00
        ];

        // Act
        $updated = $this->repository->update($quotation, $updateData);

        // Assert
        $this->assertEquals('Generated', $updated->status);
        $this->assertEquals(25000.00, $updated->total_premium);
        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
            'status' => 'Generated',
            'total_premium' => 25000.00
        ]);
    }

    public function test_delete_quotation_successfully()
    {
        // Arrange
        $quotation = Quotation::factory()->create();

        // Act
        $result = $this->repository->delete($quotation);

        // Assert
        $this->assertTrue($result);
        $this->assertSoftDeleted('quotations', ['id' => $quotation->id]);
    }

    public function test_find_by_id_returns_quotation()
    {
        // Arrange
        $quotation = Quotation::factory()->create();

        // Act
        $found = $this->repository->findById($quotation->id);

        // Assert
        $this->assertInstanceOf(Quotation::class, $found);
        $this->assertEquals($quotation->id, $found->id);
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
        $quotation = Quotation::factory()->create(['status' => 'Draft']);

        // Act
        $result = $this->repository->updateStatus($quotation->id, 'Generated');

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
            'status' => 'Generated'
        ]);
    }

    public function test_get_by_customer_id_returns_customer_quotations()
    {
        // Arrange
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();
        
        Quotation::factory()->count(4)->create(['customer_id' => $customer1->id]);
        Quotation::factory()->count(2)->create(['customer_id' => $customer2->id]);

        // Act
        $customer1Quotations = $this->repository->getByCustomerId($customer1->id);

        // Assert
        $this->assertCount(4, $customer1Quotations);
        $customer1Quotations->each(function ($quotation) use ($customer1) {
            $this->assertEquals($customer1->id, $quotation->customer_id);
        });
    }

    public function test_get_with_companies_loads_quotation_companies()
    {
        // Arrange
        $quotation = Quotation::factory()->create();
        
        QuotationCompany::factory()->count(3)->create([
            'quotation_id' => $quotation->id,
            'insurance_company_id' => $this->insuranceCompany->id
        ]);

        // Act
        $found = $this->repository->getWithCompanies($quotation->id);

        // Assert
        $this->assertInstanceOf(Quotation::class, $found);
        $this->assertTrue($found->relationLoaded('quotationCompanies'));
        $this->assertCount(3, $found->quotationCompanies);
    }

    public function test_get_by_status_filters_quotations_correctly()
    {
        // Arrange
        Quotation::factory()->count(2)->create(['status' => 'Draft']);
        Quotation::factory()->count(3)->create(['status' => 'Generated']);
        Quotation::factory()->count(1)->create(['status' => 'Accepted']);

        // Act
        $generatedQuotations = $this->repository->getByStatus('Generated');

        // Assert
        $this->assertCount(3, $generatedQuotations);
        $generatedQuotations->each(function ($quotation) {
            $this->assertEquals('Generated', $quotation->status);
        });
    }

    public function test_get_recent_quotations_returns_latest_records()
    {
        // Arrange
        Quotation::factory()->create(['created_at' => Carbon::now()->subDays(5)]);
        Quotation::factory()->create(['created_at' => Carbon::now()->subDays(2)]);
        $recentQuotation = Quotation::factory()->create(['created_at' => Carbon::now()]);

        // Act
        $recentQuotations = $this->repository->getRecent(2);

        // Assert
        $this->assertCount(2, $recentQuotations);
        $this->assertEquals($recentQuotation->id, $recentQuotations->first()->id);
    }

    public function test_search_quotations_by_quotation_number()
    {
        // Arrange
        Quotation::factory()->create(['quotation_no' => 'QUO-2024-001']);
        Quotation::factory()->create(['quotation_no' => 'QUO-2024-002']);
        Quotation::factory()->create(['quotation_no' => 'INV-2024-001']);

        $request = new Request(['search' => 'QUO-2024']);

        // Act
        $result = $this->repository->getPaginated($request);

        // Assert
        $this->assertEquals(2, $result->total());
        $result->each(function ($quotation) {
            $this->assertStringContains('QUO-2024', $quotation->quotation_no);
        });
    }

    public function test_get_expiring_quotations_returns_old_draft_quotations()
    {
        // Arrange - Quotations older than 30 days in draft status
        $oldDraft = Quotation::factory()->create([
            'status' => 'Draft',
            'created_at' => Carbon::now()->subDays(35)
        ]);

        $recentDraft = Quotation::factory()->create([
            'status' => 'Draft',
            'created_at' => Carbon::now()->subDays(10)
        ]);

        $oldGenerated = Quotation::factory()->create([
            'status' => 'Generated',
            'created_at' => Carbon::now()->subDays(35)
        ]);

        // Act
        $expiringQuotations = $this->repository->getExpiringQuotations(30);

        // Assert
        $this->assertCount(1, $expiringQuotations);
        $this->assertEquals($oldDraft->id, $expiringQuotations->first()->id);
    }

    public function test_find_with_relations_loads_specified_relationships()
    {
        // Arrange
        $quotation = Quotation::factory()->create([
            'customer_id' => $this->customer->id
        ]);

        QuotationCompany::factory()->create([
            'quotation_id' => $quotation->id,
            'insurance_company_id' => $this->insuranceCompany->id
        ]);

        // Act
        $found = $this->repository->findWithRelations(
            $quotation->id, 
            ['customer', 'quotationCompanies.insuranceCompany']
        );

        // Assert
        $this->assertInstanceOf(Quotation::class, $found);
        $this->assertTrue($found->relationLoaded('customer'));
        $this->assertTrue($found->relationLoaded('quotationCompanies'));
        $this->assertEquals($this->customer->id, $found->customer->id);
    }

    public function test_repository_handles_foreign_key_constraints()
    {
        // Test foreign key constraint handling
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Try to create quotation with non-existent customer
        $this->repository->create([
            'customer_id' => 99999,
            'quotation_no' => 'QUO-INVALID',
            'quotation_date' => '2024-01-01',
            'status' => 'Draft'
        ]);
    }

    public function test_get_quotations_by_date_range()
    {
        // Arrange
        $startDate = Carbon::now()->subDays(10);
        $endDate = Carbon::now()->subDays(5);

        Quotation::factory()->create(['quotation_date' => $startDate->format('Y-m-d')]);
        Quotation::factory()->create(['quotation_date' => $startDate->addDays(2)->format('Y-m-d')]);
        Quotation::factory()->create(['quotation_date' => Carbon::now()->subDays(2)->format('Y-m-d')]); // Outside range

        // Act
        $quotationsInRange = $this->repository->getByDateRange(
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        );

        // Assert
        $this->assertCount(2, $quotationsInRange);
    }

    public function test_get_quotations_count_by_status()
    {
        // Arrange
        Quotation::factory()->count(5)->create(['status' => 'Draft']);
        Quotation::factory()->count(3)->create(['status' => 'Generated']);
        Quotation::factory()->count(2)->create(['status' => 'Accepted']);

        // Act
        $statusCounts = $this->repository->getCountByStatus();

        // Assert
        $this->assertEquals(5, $statusCounts['Draft'] ?? 0);
        $this->assertEquals(3, $statusCounts['Generated'] ?? 0);
        $this->assertEquals(2, $statusCounts['Accepted'] ?? 0);
    }
}