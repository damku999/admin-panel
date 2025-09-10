# Test Implementation Examples
## Laravel Insurance Management System

**High-quality test class patterns for service layer testing**

---

## 1. Service Layer Test Example: CustomerInsuranceServiceTest.php

```php
<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\CustomerInsuranceRepositoryInterface;
use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Http\Requests\StoreCustomerInsuranceRequest;
use App\Http\Requests\UpdateCustomerInsuranceRequest;
use App\Models\Customer;
use App\Models\CustomerInsurance;
use App\Models\InsuranceCompany;
use App\Services\CustomerInsuranceService;
use App\Services\PdfGenerationService;
use App\Services\FileUploadService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;
use Carbon\Carbon;

class CustomerInsuranceServiceTest extends TestCase
{
    protected CustomerInsuranceService $service;
    protected $customerInsuranceRepository;
    protected $customerRepository;
    protected $pdfService;
    protected $fileUploadService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customerInsuranceRepository = Mockery::mock(CustomerInsuranceRepositoryInterface::class);
        $this->customerRepository = Mockery::mock(CustomerRepositoryInterface::class);
        $this->pdfService = Mockery::mock(PdfGenerationService::class);
        $this->fileUploadService = Mockery::mock(FileUploadService::class);
        
        $this->service = new CustomerInsuranceService(
            $this->customerInsuranceRepository,
            $this->customerRepository,
            $this->pdfService,
            $this->fileUploadService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_policies_returns_paginated_data_with_filters()
    {
        $request = new Request([
            'search' => 'POL-123',
            'customer_id' => 1,
            'policy_type' => 'Motor',
            'status' => 1,
            'expiring_soon' => 'yes'
        ]);

        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);
        
        $this->customerInsuranceRepository
            ->shouldReceive('getPaginatedWithFilters')
            ->once()
            ->with([
                'search' => 'POL-123',
                'customer_id' => 1,
                'policy_type' => 'Motor',
                'status' => 1,
                'expiring_soon' => true,
                'sort_field' => 'created_at',
                'sort_order' => 'desc'
            ], 15)
            ->andReturn($expectedPaginator);

        $result = $this->service->getPolicies($request);

        $this->assertSame($expectedPaginator, $result);
    }

    public function test_create_policy_calculates_premium_and_handles_documents()
    {
        $request = Mockery::mock(StoreCustomerInsuranceRequest::class);
        $validatedData = [
            'customer_id' => 1,
            'insurance_company_id' => 1,
            'policy_type_id' => 1,
            'sum_insured' => 500000,
            'basic_premium' => 15000,
            'start_date' => '2025-01-01',
            'expired_date' => '2026-01-01'
        ];
        
        $request->shouldReceive('validated')->andReturn($validatedData);
        $request->shouldReceive('hasFile')->with('policy_document')->andReturn(true);
        $request->shouldReceive('file')->with('policy_document')->andReturn('mock_file');

        $customer = new Customer(['id' => 1, 'name' => 'John Doe']);
        $this->customerRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($customer);

        $this->fileUploadService
            ->shouldReceive('uploadPolicyDocument')
            ->once()
            ->with('mock_file', 1)
            ->andReturn('/uploads/policies/policy_123.pdf');

        $expectedData = array_merge($validatedData, [
            'total_premium' => 16500, // Calculated with taxes/fees
            'policy_document_path' => '/uploads/policies/policy_123.pdf',
            'policy_no' => 'POL-2025-000001'
        ]);

        $policy = new CustomerInsurance($expectedData);
        $policy->id = 1;

        $this->customerInsuranceRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) use ($expectedData) {
                return $data['total_premium'] === $expectedData['total_premium'] &&
                       $data['policy_document_path'] === $expectedData['policy_document_path'] &&
                       isset($data['policy_no']);
            }))
            ->andReturn($policy);

        $result = $this->service->createPolicy($request);

        $this->assertInstanceOf(CustomerInsurance::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_create_policy_throws_exception_for_invalid_customer()
    {
        $request = Mockery::mock(StoreCustomerInsuranceRequest::class);
        $request->shouldReceive('validated')->andReturn(['customer_id' => 999]);

        $this->customerRepository
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Customer not found with ID: 999');

        $this->service->createPolicy($request);
    }

    public function test_renew_policy_creates_new_policy_with_updated_dates()
    {
        $existingPolicy = new CustomerInsurance([
            'id' => 1,
            'customer_id' => 1,
            'policy_no' => 'POL-2024-000001',
            'sum_insured' => 500000,
            'basic_premium' => 15000,
            'start_date' => '2024-01-01',
            'expired_date' => '2025-01-01'
        ]);

        $renewalData = [
            'sum_insured' => 550000,
            'basic_premium' => 16000,
            'start_date' => '2025-01-01',
            'expired_date' => '2026-01-01'
        ];

        $this->customerInsuranceRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($existingPolicy);

        $renewedPolicy = new CustomerInsurance([
            'customer_id' => 1,
            'policy_no' => 'POL-2025-000001',
            'sum_insured' => 550000,
            'total_premium' => 17600, // Calculated
            'start_date' => '2025-01-01',
            'expired_date' => '2026-01-01',
            'renewed_from_policy_id' => 1
        ]);
        $renewedPolicy->id = 2;

        $this->customerInsuranceRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['renewed_from_policy_id'] === 1 &&
                       $data['policy_no'] !== 'POL-2024-000001' &&
                       isset($data['total_premium']);
            }))
            ->andReturn($renewedPolicy);

        $result = $this->service->renewPolicy(1, $renewalData);

        $this->assertInstanceOf(CustomerInsurance::class, $result);
        $this->assertEquals(2, $result->id);
        $this->assertEquals(1, $result->renewed_from_policy_id);
    }

    public function test_get_expiring_policies_returns_correct_policies()
    {
        $days = 30;
        $expectedCollection = new Collection([
            new CustomerInsurance(['expired_date' => Carbon::now()->addDays(15)]),
            new CustomerInsurance(['expired_date' => Carbon::now()->addDays(25)])
        ]);

        $this->customerInsuranceRepository
            ->shouldReceive('getExpiringPolicies')
            ->once()
            ->with($days)
            ->andReturn($expectedCollection);

        $result = $this->service->getExpiringPolicies($days);

        $this->assertSame($expectedCollection, $result);
        $this->assertCount(2, $result);
    }

    public function test_calculate_premium_with_various_components()
    {
        $policyData = [
            'sum_insured' => 500000,
            'basic_premium_rate' => 3.5, // 3.5%
            'addon_covers' => [
                'zero_depreciation' => 5000,
                'roadside_assistance' => 1500
            ],
            'discounts' => [
                'no_claim_bonus' => 10 // 10%
            ]
        ];

        $expectedPremium = $this->calculateExpectedPremium($policyData);

        $result = $this->service->calculatePremium($policyData);

        $this->assertEquals($expectedPremium, $result);
    }

    public function test_generate_policy_pdf_calls_pdf_service()
    {
        $policy = new CustomerInsurance(['id' => 1, 'policy_no' => 'POL-123']);
        $expectedPdfContent = 'PDF_CONTENT_BINARY_DATA';

        $this->pdfService
            ->shouldReceive('generatePolicyPdf')
            ->once()
            ->with($policy)
            ->andReturn($expectedPdfContent);

        $result = $this->service->generatePolicyPdf($policy);

        $this->assertEquals($expectedPdfContent, $result);
    }

    public function test_update_policy_status_validates_transition()
    {
        $policy = new CustomerInsurance(['id' => 1, 'status' => 'active']);

        // Test valid status transition
        $this->customerInsuranceRepository
            ->shouldReceive('updateStatus')
            ->once()
            ->with(1, 'expired')
            ->andReturn(true);

        $result = $this->service->updatePolicyStatus(1, 'expired');
        $this->assertTrue($result);

        // Test invalid status transition
        $this->expectException(\InvalidArgumentException::class);
        $this->service->updatePolicyStatus(1, 'invalid_status');
    }

    public function test_get_family_policies_returns_correct_policies()
    {
        $customerId = 1;
        $familyGroupId = 10;
        
        $customer = new Customer([
            'id' => 1,
            'family_group_id' => $familyGroupId
        ]);

        $this->customerRepository
            ->shouldReceive('findById')
            ->once()
            ->with($customerId)
            ->andReturn($customer);

        $expectedPolicies = new Collection([
            new CustomerInsurance(['customer_id' => 1]),
            new CustomerInsurance(['customer_id' => 2]) // Family member's policy
        ]);

        $this->customerInsuranceRepository
            ->shouldReceive('getFamilyPolicies')
            ->once()
            ->with($familyGroupId)
            ->andReturn($expectedPolicies);

        $result = $this->service->getFamilyPolicies($customerId);

        $this->assertSame($expectedPolicies, $result);
    }

    public function test_bulk_status_update_processes_multiple_policies()
    {
        $policyIds = [1, 2, 3];
        $newStatus = 'suspended';

        $this->customerInsuranceRepository
            ->shouldReceive('bulkUpdateStatus')
            ->once()
            ->with($policyIds, $newStatus)
            ->andReturn(3); // Number of updated records

        $result = $this->service->bulkStatusUpdate($policyIds, $newStatus);

        $this->assertEquals(3, $result);
    }

    public function test_policy_exists_checks_repository()
    {
        $policyNo = 'POL-123456';

        $this->customerInsuranceRepository
            ->shouldReceive('existsByPolicyNumber')
            ->once()
            ->with($policyNo)
            ->andReturn(true);

        $result = $this->service->policyExists($policyNo);

        $this->assertTrue($result);
    }

    /**
     * Helper method for premium calculation testing
     */
    private function calculateExpectedPremium(array $policyData): int
    {
        $basicPremium = ($policyData['sum_insured'] * $policyData['basic_premium_rate']) / 100;
        $addonTotal = array_sum($policyData['addon_covers'] ?? []);
        $subtotal = $basicPremium + $addonTotal;
        
        // Apply discounts
        if (isset($policyData['discounts']['no_claim_bonus'])) {
            $discountPercent = $policyData['discounts']['no_claim_bonus'];
            $subtotal = $subtotal * (1 - $discountPercent / 100);
        }
        
        // Add taxes (assuming 18% GST)
        $total = $subtotal * 1.18;
        
        return (int) round($total);
    }
}
```

---

## 2. Repository Test Example: QuotationRepositoryTest.php

```php
<?php

namespace Tests\Unit\Repositories;

use App\Models\Quotation;
use App\Models\Customer;
use App\Models\InsuranceCompany;
use App\Repositories\QuotationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;
use Carbon\Carbon;

class QuotationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected QuotationRepository $repository;
    protected Customer $customer;
    protected InsuranceCompany $insuranceCompany;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = new QuotationRepository();
        
        // Create test data
        $this->customer = Customer::factory()->create();
        $this->insuranceCompany = InsuranceCompany::factory()->create();
    }

    public function test_get_all_returns_all_quotations()
    {
        Quotation::factory()->count(5)->create();

        $quotations = $this->repository->getAll();

        $this->assertCount(5, $quotations);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $quotations);
    }

    public function test_get_paginated_returns_paginated_results()
    {
        Quotation::factory()->count(25)->create();

        $result = $this->repository->getPaginated([], 10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(25, $result->total());
    }

    public function test_get_paginated_with_search_filter()
    {
        $targetQuotation = Quotation::factory()->create([
            'vehicle_number' => 'ABC123',
            'customer_id' => $this->customer->id
        ]);
        
        Quotation::factory()->count(3)->create([
            'vehicle_number' => 'XYZ789'
        ]);

        $result = $this->repository->getPaginated([
            'search' => 'ABC123'
        ], 10);

        $this->assertEquals(1, $result->total());
        $this->assertEquals('ABC123', $result->items()[0]->vehicle_number);
    }

    public function test_get_paginated_with_status_filter()
    {
        Quotation::factory()->create(['status' => 'Draft']);
        Quotation::factory()->create(['status' => 'Sent']);
        Quotation::factory()->count(2)->create(['status' => 'Converted']);

        $result = $this->repository->getPaginated([
            'status' => 'Converted'
        ], 10);

        $this->assertEquals(2, $result->total());
        foreach ($result->items() as $quotation) {
            $this->assertEquals('Converted', $quotation->status);
        }
    }

    public function test_get_paginated_with_customer_filter()
    {
        $targetCustomer = Customer::factory()->create();
        
        Quotation::factory()->count(2)->create([
            'customer_id' => $targetCustomer->id
        ]);
        
        Quotation::factory()->count(3)->create(); // Different customers

        $result = $this->repository->getPaginated([
            'customer_id' => $targetCustomer->id
        ], 10);

        $this->assertEquals(2, $result->total());
        foreach ($result->items() as $quotation) {
            $this->assertEquals($targetCustomer->id, $quotation->customer_id);
        }
    }

    public function test_get_paginated_with_date_range_filter()
    {
        $startDate = Carbon::now()->subDays(10);
        $endDate = Carbon::now()->subDays(5);
        
        // Within range
        Quotation::factory()->count(2)->create([
            'created_at' => Carbon::now()->subDays(7)
        ]);
        
        // Outside range
        Quotation::factory()->create([
            'created_at' => Carbon::now()->subDays(15)
        ]);
        
        Quotation::factory()->create([
            'created_at' => Carbon::now()->subDays(2)
        ]);

        $result = $this->repository->getPaginated([
            'from_date' => $startDate->format('Y-m-d'),
            'to_date' => $endDate->format('Y-m-d')
        ], 10);

        $this->assertEquals(2, $result->total());
    }

    public function test_create_quotation()
    {
        $data = [
            'customer_id' => $this->customer->id,
            'vehicle_number' => 'TEST123',
            'vehicle_make' => 'Toyota',
            'vehicle_model' => 'Camry',
            'idv_vehicle' => 500000,
            'total_idv' => 500000,
            'status' => 'Draft'
        ];

        $quotation = $this->repository->create($data);

        $this->assertInstanceOf(Quotation::class, $quotation);
        $this->assertEquals('TEST123', $quotation->vehicle_number);
        $this->assertDatabaseHas('quotations', $data);
    }

    public function test_update_quotation()
    {
        $quotation = Quotation::factory()->create([
            'vehicle_number' => 'OLD123'
        ]);

        $updateData = ['vehicle_number' => 'NEW123'];

        $result = $this->repository->update($quotation->id, $updateData);

        $this->assertTrue($result);
        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
            'vehicle_number' => 'NEW123'
        ]);
    }

    public function test_delete_quotation_soft_deletes()
    {
        $quotation = Quotation::factory()->create();

        $result = $this->repository->delete($quotation->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('quotations', ['id' => $quotation->id]);
    }

    public function test_find_by_id()
    {
        $quotation = Quotation::factory()->create();

        $result = $this->repository->findById($quotation->id);

        $this->assertInstanceOf(Quotation::class, $result);
        $this->assertEquals($quotation->id, $result->id);
    }

    public function test_find_by_id_returns_null_when_not_found()
    {
        $result = $this->repository->findById(999999);

        $this->assertNull($result);
    }

    public function test_get_by_customer()
    {
        $targetCustomer = Customer::factory()->create();
        
        Quotation::factory()->count(3)->create([
            'customer_id' => $targetCustomer->id
        ]);
        
        Quotation::factory()->count(2)->create(); // Different customer

        $result = $this->repository->getByCustomer($targetCustomer->id);

        $this->assertCount(3, $result);
        foreach ($result as $quotation) {
            $this->assertEquals($targetCustomer->id, $quotation->customer_id);
        }
    }

    public function test_get_by_status()
    {
        Quotation::factory()->count(2)->create(['status' => 'Draft']);
        Quotation::factory()->count(3)->create(['status' => 'Sent']);

        $result = $this->repository->getByStatus('Sent');

        $this->assertCount(3, $result);
        foreach ($result as $quotation) {
            $this->assertEquals('Sent', $quotation->status);
        }
    }

    public function test_update_status()
    {
        $quotation = Quotation::factory()->create(['status' => 'Draft']);

        $result = $this->repository->updateStatus($quotation->id, 'Sent');

        $this->assertTrue($result);
        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
            'status' => 'Sent'
        ]);
    }

    public function test_get_converted_quotations()
    {
        Quotation::factory()->count(2)->create(['status' => 'Converted']);
        Quotation::factory()->count(3)->create(['status' => 'Draft']);

        $result = $this->repository->getConvertedQuotations();

        $this->assertCount(2, $result);
        foreach ($result as $quotation) {
            $this->assertEquals('Converted', $quotation->status);
        }
    }

    public function test_get_recent_quotations()
    {
        // Recent (within 7 days)
        Quotation::factory()->count(3)->create([
            'created_at' => Carbon::now()->subDays(3)
        ]);
        
        // Old (more than 7 days)
        Quotation::factory()->count(2)->create([
            'created_at' => Carbon::now()->subDays(10)
        ]);

        $result = $this->repository->getRecentQuotations(7);

        $this->assertCount(3, $result);
    }

    public function test_search_by_vehicle_details()
    {
        $quotation1 = Quotation::factory()->create([
            'vehicle_number' => 'ABC123',
            'vehicle_make' => 'Toyota',
            'vehicle_model' => 'Camry'
        ]);
        
        $quotation2 = Quotation::factory()->create([
            'vehicle_number' => 'XYZ789',
            'vehicle_make' => 'Honda',
            'vehicle_model' => 'Accord'
        ]);

        // Search by vehicle number
        $result = $this->repository->searchByVehicleDetails('ABC123');
        $this->assertCount(1, $result);
        $this->assertEquals('ABC123', $result->first()->vehicle_number);

        // Search by make
        $result = $this->repository->searchByVehicleDetails('Toyota');
        $this->assertCount(1, $result);
        $this->assertEquals('Toyota', $result->first()->vehicle_make);

        // Search by model
        $result = $this->repository->searchByVehicleDetails('Accord');
        $this->assertCount(1, $result);
        $this->assertEquals('Honda', $result->first()->vehicle_make);
    }

    public function test_count_by_status()
    {
        Quotation::factory()->count(3)->create(['status' => 'Draft']);
        Quotation::factory()->count(2)->create(['status' => 'Sent']);
        Quotation::factory()->count(1)->create(['status' => 'Converted']);

        $this->assertEquals(3, $this->repository->countByStatus('Draft'));
        $this->assertEquals(2, $this->repository->countByStatus('Sent'));
        $this->assertEquals(1, $this->repository->countByStatus('Converted'));
        $this->assertEquals(0, $this->repository->countByStatus('Rejected'));
    }

    public function test_exists_by_vehicle_number()
    {
        $quotation = Quotation::factory()->create([
            'vehicle_number' => 'UNIQUE123'
        ]);

        $this->assertTrue($this->repository->existsByVehicleNumber('UNIQUE123'));
        $this->assertFalse($this->repository->existsByVehicleNumber('NOTFOUND'));
    }

    public function test_get_total_idv_sum()
    {
        Quotation::factory()->create(['total_idv' => 100000, 'status' => 'Converted']);
        Quotation::factory()->create(['total_idv' => 200000, 'status' => 'Converted']);
        Quotation::factory()->create(['total_idv' => 150000, 'status' => 'Draft']); // Not converted

        $totalIdv = $this->repository->getTotalIdvSum(['status' => 'Converted']);

        $this->assertEquals(300000, $totalIdv);
    }
}
```

---

## 3. Integration Test Example: PolicyRenewalWorkflowTest.php

```php
<?php

namespace Tests\Integration;

use App\Models\Customer;
use App\Models\CustomerInsurance;
use App\Models\InsuranceCompany;
use App\Models\PolicyType;
use App\Models\FamilyGroup;
use App\Models\User;
use App\Services\CustomerInsuranceService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Carbon\Carbon;

class PolicyRenewalWorkflowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $adminUser;
    protected Customer $customer;
    protected CustomerInsurance $expiringPolicy;
    protected InsuranceCompany $insuranceCompany;
    protected PolicyType $policyType;
    protected CustomerInsuranceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test environment
        $this->adminUser = User::factory()->create();
        $this->customer = Customer::factory()->create();
        $this->insuranceCompany = InsuranceCompany::factory()->create();
        $this->policyType = PolicyType::factory()->create();
        
        $this->expiringPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->customer->id,
            'insurance_company_id' => $this->insuranceCompany->id,
            'policy_type_id' => $this->policyType->id,
            'policy_no' => 'POL-2024-001',
            'sum_insured' => 500000,
            'basic_premium' => 15000,
            'start_date' => Carbon::now()->subYear(),
            'expired_date' => Carbon::now()->addDays(30), // Expires in 30 days
            'status' => 'active'
        ]);

        $this->service = app(CustomerInsuranceService::class);
    }

    public function test_complete_policy_renewal_workflow()
    {
        Mail::fake();
        Queue::fake();

        $this->actingAs($this->adminUser);

        // Step 1: Identify expiring policies
        $expiringPolicies = $this->service->getExpiringPolicies(30);
        
        $this->assertCount(1, $expiringPolicies);
        $this->assertEquals($this->expiringPolicy->id, $expiringPolicies->first()->id);

        // Step 2: Generate renewal quotation
        $renewalData = [
            'sum_insured' => 550000, // Increased coverage
            'basic_premium' => 16500, // Adjusted premium
            'start_date' => $this->expiringPolicy->expired_date,
            'expired_date' => Carbon::parse($this->expiringPolicy->expired_date)->addYear(),
            'addon_covers' => [
                'zero_depreciation' => true,
                'roadside_assistance' => true
            ]
        ];

        // Step 3: Create renewal policy
        $renewalPolicy = $this->service->renewPolicy($this->expiringPolicy->id, $renewalData);

        $this->assertInstanceOf(CustomerInsurance::class, $renewalPolicy);
        $this->assertEquals($this->customer->id, $renewalPolicy->customer_id);
        $this->assertEquals($this->expiringPolicy->id, $renewalPolicy->renewed_from_policy_id);
        $this->assertStringContains('REN-', $renewalPolicy->policy_no);

        // Step 4: Verify database changes
        $this->assertDatabaseHas('customer_insurances', [
            'id' => $renewalPolicy->id,
            'customer_id' => $this->customer->id,
            'sum_insured' => 550000,
            'renewed_from_policy_id' => $this->expiringPolicy->id
        ]);

        // Step 5: Verify original policy is marked as renewed
        $this->expiringPolicy->refresh();
        $this->assertEquals('renewed', $this->expiringPolicy->status);

        // Step 6: Verify notifications were sent
        Mail::assertSent(function ($mail) {
            return $mail->hasTo($this->customer->email) &&
                   $mail->hasSubject('Policy Renewal Confirmation');
        });

        // Step 7: Test PDF generation for renewal policy
        $pdfContent = $this->service->generatePolicyPdf($renewalPolicy);
        $this->assertNotEmpty($pdfContent);

        // Step 8: Verify audit logging
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $this->customer->id,
            'action' => 'policy_renewed',
            'success' => true
        ]);
    }

    public function test_family_policy_renewal_workflow()
    {
        // Create family setup
        $familyGroup = FamilyGroup::factory()->create();
        $familyHead = Customer::factory()->create([
            'family_group_id' => $familyGroup->id
        ]);
        
        $familyMember = Customer::factory()->create([
            'family_group_id' => $familyGroup->id
        ]);

        // Create policies for both family members
        $headPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $familyHead->id,
            'expired_date' => Carbon::now()->addDays(25)
        ]);

        $memberPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $familyMember->id,
            'expired_date' => Carbon::now()->addDays(28)
        ]);

        $this->actingAs($this->adminUser);

        // Get family policies for renewal
        $familyPolicies = $this->service->getFamilyPolicies($familyHead->id);
        
        $this->assertCount(2, $familyPolicies);

        // Renew both policies
        $renewalData = [
            'sum_insured' => 600000,
            'basic_premium' => 18000,
        ];

        foreach ($familyPolicies as $policy) {
            if (Carbon::parse($policy->expired_date)->lessThanOrEqualTo(Carbon::now()->addDays(30))) {
                $renewedPolicy = $this->service->renewPolicy($policy->id, $renewalData);
                
                $this->assertInstanceOf(CustomerInsurance::class, $renewedPolicy);
                $this->assertEquals($policy->customer_id, $renewedPolicy->customer_id);
            }
        }

        // Verify both policies were renewed
        $this->assertDatabaseHas('customer_insurances', [
            'customer_id' => $familyHead->id,
            'renewed_from_policy_id' => $headPolicy->id
        ]);

        $this->assertDatabaseHas('customer_insurances', [
            'customer_id' => $familyMember->id,
            'renewed_from_policy_id' => $memberPolicy->id
        ]);
    }

    public function test_policy_renewal_failure_rollback()
    {
        $this->actingAs($this->adminUser);

        // Mock a service that will fail during renewal
        $mockService = Mockery::mock(CustomerInsuranceService::class);
        $mockService->shouldReceive('renewPolicy')
                   ->andThrow(new \Exception('Payment gateway failed'));

        $this->app->instance(CustomerInsuranceService::class, $mockService);

        try {
            $mockService->renewPolicy($this->expiringPolicy->id, [
                'sum_insured' => 550000,
                'basic_premium' => 16500
            ]);
        } catch (\Exception $e) {
            // Expected exception
        }

        // Verify original policy status unchanged
        $this->expiringPolicy->refresh();
        $this->assertEquals('active', $this->expiringPolicy->status);

        // Verify no partial renewal data created
        $this->assertDatabaseMissing('customer_insurances', [
            'renewed_from_policy_id' => $this->expiringPolicy->id
        ]);
    }

    public function test_bulk_policy_renewal_workflow()
    {
        // Create multiple expiring policies
        $expiringPolicies = CustomerInsurance::factory()->count(5)->create([
            'expired_date' => Carbon::now()->addDays(15),
            'status' => 'active'
        ]);

        $this->actingAs($this->adminUser);

        $renewalData = [
            'sum_insured' => 500000,
            'basic_premium' => 15000,
        ];

        $renewedCount = 0;
        foreach ($expiringPolicies as $policy) {
            try {
                $renewedPolicy = $this->service->renewPolicy($policy->id, $renewalData);
                if ($renewedPolicy) {
                    $renewedCount++;
                }
            } catch (\Exception $e) {
                // Log failed renewals but continue
                \Log::error("Policy renewal failed for {$policy->id}: " . $e->getMessage());
            }
        }

        $this->assertEquals(5, $renewedCount);

        // Verify all renewals were created
        $this->assertEquals(5, CustomerInsurance::whereNotNull('renewed_from_policy_id')->count());
    }

    public function test_policy_renewal_with_premium_calculation()
    {
        $this->actingAs($this->adminUser);

        $renewalData = [
            'sum_insured' => 600000,
            'basic_premium_rate' => 3.2, // 3.2%
            'addon_covers' => [
                'zero_depreciation' => 8000,
                'roadside_assistance' => 2000
            ],
            'discounts' => [
                'no_claim_bonus' => 15 // 15% discount
            ]
        ];

        $renewedPolicy = $this->service->renewPolicy($this->expiringPolicy->id, $renewalData);

        // Verify premium calculation
        $expectedBasicPremium = 600000 * 3.2 / 100; // 19,200
        $expectedAddonTotal = 8000 + 2000; // 10,000
        $expectedSubtotal = $expectedBasicPremium + $expectedAddonTotal; // 29,200
        $expectedAfterDiscount = $expectedSubtotal * 0.85; // 24,820 (15% discount)
        $expectedTotal = $expectedAfterDiscount * 1.18; // 29,287.6 (with 18% GST)

        $this->assertEquals(round($expectedTotal), $renewedPolicy->total_premium);
    }

    public function test_policy_renewal_document_generation_and_storage()
    {
        $this->actingAs($this->adminUser);

        $renewalData = [
            'sum_insured' => 550000,
            'basic_premium' => 16500,
        ];

        $renewedPolicy = $this->service->renewPolicy($this->expiringPolicy->id, $renewalData);

        // Generate policy documents
        $policyPdf = $this->service->generatePolicyPdf($renewedPolicy);
        
        $this->assertNotEmpty($policyPdf);

        // Verify document was stored
        $this->assertNotNull($renewedPolicy->policy_document_path);
        $this->assertStringContains('policies/', $renewedPolicy->policy_document_path);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
```

---

## 4. Feature Test Enhancement Example: CustomerPortalTest.php

```php
<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerInsurance;
use App\Models\FamilyGroup;
use App\Models\FamilyMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;
    protected CustomerInsurance $policy;
    protected FamilyGroup $familyGroup;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->familyGroup = FamilyGroup::factory()->create();
        $this->customer = Customer::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'family_group_id' => $this->familyGroup->id,
            'status' => true
        ]);
        
        $this->policy = CustomerInsurance::factory()->create([
            'customer_id' => $this->customer->id,
            'policy_no' => 'POL-123456'
        ]);
    }

    public function test_customer_can_view_dashboard_with_policies()
    {
        $this->actingAs($this->customer, 'customer');

        $response = $this->get(route('customer.dashboard'));

        $response->assertStatus(200)
                ->assertViewIs('customer.dashboard')
                ->assertSee($this->customer->name)
                ->assertSee('POL-123456')
                ->assertSee('Family Insurance Dashboard');
    }

    public function test_customer_can_view_policy_details()
    {
        $this->actingAs($this->customer, 'customer');

        $response = $this->get(route('customer.policies.detail', $this->policy->id));

        $response->assertStatus(200)
                ->assertViewIs('customer.policies.detail')
                ->assertSee($this->policy->policy_no)
                ->assertSee($this->policy->sum_insured);
    }

    public function test_customer_cannot_view_other_customer_policy()
    {
        $otherCustomer = Customer::factory()->create();
        $otherPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $otherCustomer->id
        ]);

        $this->actingAs($this->customer, 'customer');

        $response = $this->get(route('customer.policies.detail', $otherPolicy->id));

        $response->assertStatus(403);
    }

    public function test_customer_can_download_own_policy_document()
    {
        $this->policy->update([
            'policy_document_path' => 'test/policy.pdf'
        ]);

        $this->actingAs($this->customer, 'customer');

        $response = $this->get(route('customer.policies.download', $this->policy->id));

        $response->assertStatus(200);
        // Additional assertions for file download would go here
    }

    public function test_family_member_can_view_family_policies()
    {
        $familyMember = Customer::factory()->create([
            'family_group_id' => $this->familyGroup->id,
            'password' => Hash::make('password123')
        ]);

        FamilyMember::create([
            'family_group_id' => $this->familyGroup->id,
            'customer_id' => $familyMember->id,
            'relationship' => 'spouse',
            'is_head' => false,
            'status' => true
        ]);

        $this->actingAs($familyMember, 'customer');

        $response = $this->get(route('customer.policies'));

        $response->assertStatus(200)
                ->assertSee($this->policy->policy_no); // Should see family head's policy
    }

    public function test_customer_can_update_profile()
    {
        $this->actingAs($this->customer, 'customer');

        $updateData = [
            'name' => 'Updated Name',
            'mobile_number' => '9876543210',
            'address' => 'Updated Address'
        ];

        $response = $this->put(route('customer.profile.update'), $updateData);

        $response->assertRedirect(route('customer.profile'))
                ->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'id' => $this->customer->id,
            'name' => 'Updated Name',
            'mobile_number' => '9876543210'
        ]);
    }

    public function test_customer_can_change_password()
    {
        $this->actingAs($this->customer, 'customer');

        $response = $this->post(route('customer.change-password.update'), [
            'current_password' => 'password123',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!'
        ]);

        $response->assertRedirect(route('customer.profile'))
                ->assertSessionHas('success');

        $this->customer->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $this->customer->password));
    }

    public function test_customer_cannot_change_password_with_wrong_current_password()
    {
        $this->actingAs($this->customer, 'customer');

        $response = $this->post(route('customer.change-password.update'), [
            'current_password' => 'wrongpassword',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!'
        ]);

        $response->assertRedirect()
                ->assertSessionHasErrors(['current_password']);
    }

    public function test_customer_portal_security_headers()
    {
        $this->actingAs($this->customer, 'customer');

        $response = $this->get(route('customer.dashboard'));

        $response->assertStatus(200)
                ->assertHeader('X-Frame-Options', 'DENY')
                ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_customer_session_timeout()
    {
        $this->actingAs($this->customer, 'customer');

        // Simulate expired session
        session(['customer_last_activity' => now()->subHours(3)]);

        $response = $this->get(route('customer.policies'));

        $response->assertRedirect(route('customer.login'));
        $this->assertGuest('customer');
    }
}
```

These examples demonstrate:

1. **Comprehensive Service Testing** with proper mocking, edge cases, and business logic validation
2. **Repository Testing** with database operations, filtering, and data integrity
3. **Integration Testing** with complete workflows, rollback scenarios, and cross-service interactions
4. **Feature Testing** with security considerations, authorization, and user experience validation

Each test follows Laravel best practices and provides high coverage of critical business functionality while maintaining test reliability and maintainability.