<?php

namespace Tests\Integration;

use App\Models\Customer;
use App\Models\Quotation;
use App\Models\QuotationCompany;
use App\Models\InsuranceCompany;
use App\Models\PolicyType;
use App\Models\FuelType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuotationWorkflowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Customer $customer;
    protected InsuranceCompany $insuranceCompany1;
    protected InsuranceCompany $insuranceCompany2;
    protected PolicyType $policyType;
    protected FuelType $fuelType;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user and authenticate
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        
        // Create supporting entities
        $this->customer = Customer::factory()->create();
        $this->insuranceCompany1 = InsuranceCompany::factory()->create(['name' => 'Company A']);
        $this->insuranceCompany2 = InsuranceCompany::factory()->create(['name' => 'Company B']);
        $this->policyType = PolicyType::factory()->create();
        $this->fuelType = FuelType::factory()->create();
    }

    public function test_complete_quotation_generation_workflow()
    {
        // Step 1: Create a quotation request
        $quotationData = [
            'customer_id' => $this->customer->id,
            'policy_type_id' => $this->policyType->id,
            'fuel_type_id' => $this->fuelType->id,
            'vehicle_make' => 'Toyota',
            'vehicle_model' => 'Camry',
            'vehicle_year' => 2022,
            'vehicle_value' => 2500000,
            'location' => 'Mumbai',
            'requested_sum_insured' => 2500000,
            'status' => 'pending'
        ];
        
        $response = $this->post(route('quotations.store'), $quotationData);
        
        $response->assertRedirect(route('quotations.index'));
        $response->assertSessionHas('success');
        
        // Verify quotation was created
        $this->assertDatabaseHas('quotations', [
            'customer_id' => $this->customer->id,
            'vehicle_make' => 'Toyota',
            'vehicle_model' => 'Camry'
        ]);
        
        $quotation = Quotation::where('customer_id', $this->customer->id)
            ->where('vehicle_make', 'Toyota')
            ->first();
        
        // Step 2: Add quotation companies (price comparisons)
        $company1Data = [
            'quotation_id' => $quotation->id,
            'insurance_company_id' => $this->insuranceCompany1->id,
            'quoted_premium' => 18000,
            'coverage_details' => 'Comprehensive coverage with zero depreciation',
            'additional_benefits' => 'Free roadside assistance, NCB protection',
            'terms_conditions' => 'Standard terms apply'
        ];
        
        $company2Data = [
            'quotation_id' => $quotation->id,
            'insurance_company_id' => $this->insuranceCompany2->id,
            'quoted_premium' => 16500,
            'coverage_details' => 'Comprehensive coverage',
            'additional_benefits' => 'NCB protection',
            'terms_conditions' => 'Standard terms apply'
        ];
        
        $response1 = $this->post(route('quotation-companies.store'), $company1Data);
        $response2 = $this->post(route('quotation-companies.store'), $company2Data);
        
        $response1->assertRedirect();
        $response2->assertRedirect();
        
        // Verify quotation companies were created
        $this->assertDatabaseHas('quotation_companies', [
            'quotation_id' => $quotation->id,
            'insurance_company_id' => $this->insuranceCompany1->id,
            'quoted_premium' => 18000
        ]);
        
        $this->assertDatabaseHas('quotation_companies', [
            'quotation_id' => $quotation->id,
            'insurance_company_id' => $this->insuranceCompany2->id,
            'quoted_premium' => 16500
        ]);
        
        // Step 3: View quotation comparison
        $response = $this->get(route('quotations.show', $quotation));
        
        $response->assertStatus(200);
        $response->assertSee('Toyota Camry');
        $response->assertSee('Company A');
        $response->assertSee('Company B');
        $response->assertSee('18,000');
        $response->assertSee('16,500');
        
        // Step 4: Update quotation status to approved
        $updateData = array_merge($quotationData, [
            'status' => 'approved',
            'selected_company_id' => $this->insuranceCompany2->id, // Select Company B (lower premium)
            'final_premium' => 16500
        ]);
        
        $response = $this->put(route('quotations.update', $quotation), $updateData);
        
        $response->assertRedirect(route('quotations.index'));
        $response->assertSessionHas('success');
        
        // Verify quotation was updated
        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
            'status' => 'approved',
            'selected_company_id' => $this->insuranceCompany2->id
        ]);
        
        // Step 5: Generate quotation PDF
        $response = $this->get(route('quotations.pdf', $quotation));
        
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_quotation_rejection_workflow()
    {
        // Create a quotation
        $quotation = Quotation::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'pending'
        ]);
        
        // Add a company quote
        QuotationCompany::factory()->create([
            'quotation_id' => $quotation->id,
            'insurance_company_id' => $this->insuranceCompany1->id,
            'quoted_premium' => 25000 // High premium
        ]);
        
        // Customer rejects the quotation
        $updateData = [
            'customer_id' => $quotation->customer_id,
            'policy_type_id' => $quotation->policy_type_id,
            'fuel_type_id' => $quotation->fuel_type_id,
            'vehicle_make' => $quotation->vehicle_make,
            'vehicle_model' => $quotation->vehicle_model,
            'vehicle_year' => $quotation->vehicle_year,
            'vehicle_value' => $quotation->vehicle_value,
            'location' => $quotation->location,
            'requested_sum_insured' => $quotation->requested_sum_insured,
            'status' => 'rejected',
            'rejection_reason' => 'Premium too high'
        ];
        
        $response = $this->put(route('quotations.update', $quotation), $updateData);
        
        $response->assertRedirect(route('quotations.index'));
        $response->assertSessionHas('success');
        
        // Verify quotation status
        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
            'status' => 'rejected',
            'rejection_reason' => 'Premium too high'
        ]);
    }

    public function test_quotation_modification_workflow()
    {
        // Create initial quotation
        $quotation = Quotation::factory()->create([
            'customer_id' => $this->customer->id,
            'vehicle_value' => 1500000,
            'requested_sum_insured' => 1500000,
            'status' => 'pending'
        ]);
        
        // Customer requests modification (higher sum insured)
        $modificationData = [
            'customer_id' => $quotation->customer_id,
            'policy_type_id' => $quotation->policy_type_id,
            'fuel_type_id' => $quotation->fuel_type_id,
            'vehicle_make' => $quotation->vehicle_make,
            'vehicle_model' => $quotation->vehicle_model,
            'vehicle_year' => $quotation->vehicle_year,
            'vehicle_value' => 2000000, // Increased value
            'location' => $quotation->location,
            'requested_sum_insured' => 2000000, // Increased sum insured
            'status' => 'modified',
            'modification_notes' => 'Customer requested higher coverage'
        ];
        
        $response = $this->put(route('quotations.update', $quotation), $modificationData);
        
        $response->assertRedirect(route('quotations.index'));
        $response->assertSessionHas('success');
        
        // Verify modification
        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
            'vehicle_value' => 2000000,
            'requested_sum_insured' => 2000000,
            'status' => 'modified'
        ]);
        
        // Add new quotation companies for modified requirements
        $newQuoteData = [
            'quotation_id' => $quotation->id,
            'insurance_company_id' => $this->insuranceCompany1->id,
            'quoted_premium' => 22000, // Higher premium for higher coverage
            'coverage_details' => 'Comprehensive coverage for 20L sum insured',
            'additional_benefits' => 'Free roadside assistance',
            'terms_conditions' => 'Standard terms apply'
        ];
        
        $response = $this->post(route('quotation-companies.store'), $newQuoteData);
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('quotation_companies', [
            'quotation_id' => $quotation->id,
            'quoted_premium' => 22000
        ]);
    }

    public function test_quotation_search_and_filter_workflow()
    {
        // Create quotations with different characteristics
        $pendingQuotation = Quotation::factory()->create([
            'customer_id' => $this->customer->id,
            'vehicle_make' => 'Honda',
            'vehicle_model' => 'City',
            'status' => 'pending'
        ]);
        
        $approvedQuotation = Quotation::factory()->create([
            'customer_id' => Customer::factory()->create()->id,
            'vehicle_make' => 'Maruti',
            'vehicle_model' => 'Swift',
            'status' => 'approved'
        ]);
        
        // Test search by vehicle make
        $response = $this->get(route('quotations.index', [
            'search' => 'Honda'
        ]));
        
        $response->assertStatus(200);
        $response->assertSee('Honda City');
        $response->assertDontSee('Maruti Swift');
        
        // Test filter by status
        $response = $this->get(route('quotations.index', [
            'status' => 'pending'
        ]));
        
        $response->assertStatus(200);
        $response->assertSee('Honda City');
        $response->assertDontSee('Maruti Swift');
        
        // Test filter by customer
        $response = $this->get(route('quotations.index', [
            'customer_id' => $this->customer->id
        ]));
        
        $response->assertStatus(200);
        $response->assertSee('Honda City');
        $response->assertDontSee('Maruti Swift');
    }

    public function test_quotation_export_workflow()
    {
        // Create multiple quotations
        Quotation::factory()->count(5)->create();
        
        // Test export functionality
        $response = $this->get(route('quotations.export'));
        
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}