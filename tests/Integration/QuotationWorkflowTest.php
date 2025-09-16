<?php

namespace Tests\Integration;

use Tests\BaseTestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\QuotationCompany;
use App\Models\InsuranceCompany;
use App\Models\AddonCover;
use App\Services\QuotationService;
use App\Services\PdfGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

/**
 * Integration tests for the complete quotation workflow
 * Tests end-to-end business processes from creation to delivery
 */
class QuotationWorkflowTest extends BaseTestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Customer $customer;
    private array $insuranceCompanies;
    private array $addonCovers;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up test data
        $this->adminUser = $this->createAdminUser();
        $this->customer = $this->createCustomer([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'mobile_number' => '9876543210',
        ]);

        $this->insuranceCompanies = $this->createInsuranceCompanies();
        $this->addonCovers = $this->createAddonCovers();
    }

    // =============================================================================
    // COMPLETE QUOTATION WORKFLOW TESTS
    // =============================================================================

    /** @test */
    public function admin_can_create_quotation_for_customer_complete_workflow(): void
    {
        $this->actingAsAdmin($this->adminUser);

        // Step 1: Create quotation
        $quotationData = [
            'customer_id' => $this->customer->id,
            'vehicle_number' => 'MH12AB1234',
            'make_model_variant' => 'Maruti Swift VDI',
            'rto_location' => 'Mumbai Central',
            'manufacturing_year' => 2020,
            'cubic_capacity_kw' => 1248,
            'seating_capacity' => 5,
            'fuel_type' => 'Diesel',
            'ncb_percentage' => 25,
            'idv_vehicle' => 500000,
            'idv_cng_lpg_kit' => 25000,
            'idv_electrical_accessories' => 30000,
            'idv_non_electrical_accessories' => 15000,
            'addon_covers' => ['Zero Depreciation', 'Engine Protection', 'Road Side Assistance'],
            'policy_type' => 'Comprehensive',
            'policy_tenure_years' => 1,
            'whatsapp_number' => '+919876543210',
            'notes' => 'Customer wants comprehensive coverage',
        ];

        $response = $this->post(route('quotations.store'), $quotationData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify quotation was created
        $this->assertDatabaseHas('quotations', [
            'customer_id' => $this->customer->id,
            'vehicle_number' => 'MH12AB1234',
            'total_idv' => 570000, // Sum of all IDV components
        ]);

        $quotation = Quotation::where('vehicle_number', 'MH12AB1234')->first();
        $this->assertNotNull($quotation);

        // Step 2: Generate company quotes
        $quotationService = app(QuotationService::class);
        $quotationService->generateCompanyQuotes($quotation);

        // Verify company quotes were generated
        $this->assertGreaterThan(0, $quotation->fresh()->quotationCompanies()->count());

        $companyQuotes = $quotation->fresh()->quotationCompanies;
        foreach ($companyQuotes as $quote) {
            $this->assertGreaterThan(0, $quote->basic_od_premium);
            $this->assertGreaterThan(0, $quote->final_premium);
            $this->assertNotNull($quote->quote_number);
        }

        // Step 3: Verify ranking and recommendation
        $bestQuote = $quotation->fresh()->quotationCompanies()->orderBy('final_premium')->first();
        $this->assertTrue($bestQuote->is_recommended);
        $this->assertEquals(1, $bestQuote->ranking);

        // Step 4: Test PDF generation
        $response = $this->get(route('quotations.pdf', $quotation->id));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        // Step 5: Mark quotation as sent
        $quotation->update(['status' => 'Sent', 'sent_at' => now()]);

        $this->assertEquals('Sent', $quotation->fresh()->status);
        $this->assertNotNull($quotation->fresh()->sent_at);
    }

    /** @test */
    public function customer_can_view_their_quotation_and_download_pdf(): void
    {
        // Create quotation with company quotes
        $quotationData = $this->createQuotationWithCompanies($this->customer);
        $quotation = $quotationData['quotation'];

        $this->actingAsCustomer($this->customer);

        // Customer can view quotations list
        $response = $this->get(route('customer.quotations'));
        $response->assertStatus(200);
        $response->assertViewHas('quotations');
        $response->assertSee($quotation->vehicle_number);

        // Customer can view quotation detail
        $response = $this->get(route('customer.quotation.detail', $quotation->id));
        $response->assertStatus(200);
        $response->assertViewHas('quotation');
        $response->assertSee($quotation->vehicle_number);

        // Customer can download quotation PDF
        $response = $this->get(route('customer.quotation.download', $quotation->id));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        // Verify audit log was created
        $this->assertAuditLogCreated('download_quotation');
    }

    /** @test */
    public function family_head_can_view_and_manage_family_quotations(): void
    {
        $family = $this->createFamilyStructure();

        // Create quotations for different family members
        $headQuotation = $this->createQuotationWithCompanies($family['familyHead']);
        $spouseQuotation = $this->createQuotationWithCompanies($family['spouse']);
        $childQuotation = $this->createQuotationWithCompanies($family['child']);

        $this->actingAsCustomer($family['familyHead']);

        // Family head can see all family quotations
        $response = $this->get(route('customer.quotations'));
        $response->assertStatus(200);

        // Should see all family quotations
        $response->assertSee($headQuotation['quotation']->vehicle_number);
        $response->assertSee($spouseQuotation['quotation']->vehicle_number);
        $response->assertSee($childQuotation['quotation']->vehicle_number);

        // Can download any family member's quotation
        $response = $this->get(route('customer.quotation.download', $spouseQuotation['quotation']->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function family_member_can_only_view_own_quotations(): void
    {
        $family = $this->createFamilyStructure();

        // Create quotations for different family members
        $headQuotation = $this->createQuotationWithCompanies($family['familyHead']);
        $spouseQuotation = $this->createQuotationWithCompanies($family['spouse']);

        $this->actingAsCustomer($family['spouse']); // Login as spouse (not head)

        // Spouse can see only their own quotations
        $response = $this->get(route('customer.quotations'));
        $response->assertStatus(200);
        $response->assertSee($spouseQuotation['quotation']->vehicle_number);
        $response->assertDontSee($headQuotation['quotation']->vehicle_number);

        // Cannot access family head's quotation detail
        $response = $this->get(route('customer.quotation.detail', $headQuotation['quotation']->id));
        $response->assertStatus(403);
    }

    // =============================================================================
    // MANUAL QUOTATION ENTRY WORKFLOW
    // =============================================================================

    /** @test */
    public function admin_can_create_manual_quotation_with_company_quotes(): void
    {
        $this->actingAsAdmin($this->adminUser);

        $manualQuotationData = [
            'customer_id' => $this->customer->id,
            'vehicle_number' => 'DL01AB9999',
            'make_model_variant' => 'Honda City ZX',
            'idv_vehicle' => 800000,
            'policy_type' => 'Comprehensive',
            'companies' => [
                [
                    'insurance_company_id' => $this->insuranceCompanies[0]->id,
                    'quote_number' => 'MANUAL001',
                    'basic_od_premium' => 15000,
                    'tp_premium' => 4000,
                    'total_addon_premium' => 3000,
                    'net_premium' => 22000,
                    'sgst_amount' => 1980,
                    'cgst_amount' => 1980,
                    'total_premium' => 25960,
                    'final_premium' => 26200,
                    'is_recommended' => true,
                ],
                [
                    'insurance_company_id' => $this->insuranceCompanies[1]->id,
                    'quote_number' => 'MANUAL002',
                    'basic_od_premium' => 16000,
                    'tp_premium' => 4000,
                    'total_addon_premium' => 3500,
                    'net_premium' => 23500,
                    'sgst_amount' => 2115,
                    'cgst_amount' => 2115,
                    'total_premium' => 27730,
                    'final_premium' => 28000,
                ],
            ]
        ];

        $response = $this->post(route('quotations.store'), $manualQuotationData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify quotation and company quotes were created
        $quotation = Quotation::where('vehicle_number', 'DL01AB9999')->first();
        $this->assertNotNull($quotation);

        $companyQuotes = $quotation->quotationCompanies;
        $this->assertCount(2, $companyQuotes);

        $recommendedQuote = $companyQuotes->where('is_recommended', true)->first();
        $this->assertEquals('MANUAL001', $recommendedQuote->quote_number);
        $this->assertEquals(26200, $recommendedQuote->final_premium);
    }

    // =============================================================================
    // QUOTATION COMPARISON AND SELECTION WORKFLOW
    // =============================================================================

    /** @test */
    public function quotation_comparison_shows_savings_and_recommendations(): void
    {
        $quotationData = $this->createQuotationWithCompanies($this->customer);
        $quotation = $quotationData['quotation'];

        // Create company quotes with different premiums
        QuotationCompany::factory()->create([
            'quotation_id' => $quotation->id,
            'insurance_company_id' => $this->insuranceCompanies[0]->id,
            'final_premium' => 15000,
            'ranking' => 1,
            'is_recommended' => true,
        ]);

        QuotationCompany::factory()->create([
            'quotation_id' => $quotation->id,
            'insurance_company_id' => $this->insuranceCompanies[1]->id,
            'final_premium' => 18000,
            'ranking' => 2,
        ]);

        QuotationCompany::factory()->create([
            'quotation_id' => $quotation->id,
            'insurance_company_id' => $this->insuranceCompanies[2]->id,
            'final_premium' => 20000,
            'ranking' => 3,
        ]);

        $this->actingAsAdmin($this->adminUser);

        // View quotation detail to see comparison
        $response = $this->get(route('quotations.show', $quotation->id));
        $response->assertStatus(200);
        $response->assertSee('15000'); // Best premium
        $response->assertSee('20000'); // Highest premium
        $response->assertSee('5000'); // Potential savings (20000 - 15000)
    }

    // =============================================================================
    // QUOTATION UPDATE AND MODIFICATION WORKFLOW
    // =============================================================================

    /** @test */
    public function admin_can_update_quotation_and_regenerate_quotes(): void
    {
        $quotationData = $this->createQuotationWithCompanies($this->customer);
        $quotation = $quotationData['quotation'];
        $originalCompanyQuotesCount = $quotation->quotationCompanies()->count();

        $this->actingAsAdmin($this->adminUser);

        // Update quotation with new data
        $updateData = [
            'vehicle_number' => 'MH12CD5678',
            'idv_vehicle' => 600000,
            'idv_cng_lpg_kit' => 30000,
            'companies' => [
                [
                    'insurance_company_id' => $this->insuranceCompanies[0]->id,
                    'basic_od_premium' => 18000,
                    'final_premium' => 22000,
                ],
            ]
        ];

        $response = $this->put(route('quotations.update', $quotation->id), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify quotation was updated
        $quotation->refresh();
        $this->assertEquals('MH12CD5678', $quotation->vehicle_number);
        $this->assertEquals(630000, $quotation->total_idv); // 600000 + 30000

        // Verify old company quotes were replaced
        $newCompanyQuotes = $quotation->quotationCompanies;
        $this->assertCount(1, $newCompanyQuotes);
        $this->assertEquals(22000, $newCompanyQuotes->first()->final_premium);
    }

    // =============================================================================
    // QUOTATION DELETION WORKFLOW
    // =============================================================================

    /** @test */
    public function admin_can_delete_quotation_and_cleanup_related_data(): void
    {
        $quotationData = $this->createQuotationWithCompanies($this->customer);
        $quotation = $quotationData['quotation'];
        $quotationId = $quotation->id;

        // Verify related data exists
        $this->assertGreaterThan(0, $quotation->quotationCompanies()->count());

        $this->actingAsAdmin($this->adminUser);

        // Delete quotation
        $response = $this->delete(route('quotations.destroy', $quotation->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify quotation and related data were deleted
        $this->assertDatabaseMissing('quotations', ['id' => $quotationId]);
        $this->assertDatabaseMissing('quotation_companies', ['quotation_id' => $quotationId]);

        // Verify activity logs were cleaned up (if applicable)
        $activityCount = \Spatie\Activitylog\Models\Activity::where('subject_type', Quotation::class)
            ->where('subject_id', $quotationId)
            ->count();
        $this->assertEquals(0, $activityCount);
    }

    // =============================================================================
    // SECURITY AND AUTHORIZATION WORKFLOW
    // =============================================================================

    /** @test */
    public function unauthorized_customer_cannot_access_other_customer_quotations(): void
    {
        $quotationData = $this->createQuotationWithCompanies($this->customer);
        $quotation = $quotationData['quotation'];

        $anotherCustomer = $this->createCustomer(['email' => 'another@customer.com']);
        $this->actingAsCustomer($anotherCustomer);

        // Cannot view other customer's quotation detail
        $response = $this->get(route('customer.quotation.detail', $quotation->id));
        $response->assertStatus(403);

        // Cannot download other customer's quotation
        $response = $this->get(route('customer.quotation.download', $quotation->id));
        $response->assertStatus(403);

        // Verify security violation was logged
        $this->assertSecurityViolationLogged('unauthorized_quotation_access');
    }

    /** @test */
    public function guest_cannot_access_any_quotation_endpoints(): void
    {
        $quotationData = $this->createQuotationWithCompanies($this->customer);
        $quotation = $quotationData['quotation'];

        // Admin endpoints require authentication
        $response = $this->get(route('quotations.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('quotations.show', $quotation->id));
        $response->assertRedirect(route('login'));

        // Customer endpoints require customer authentication
        $response = $this->get(route('customer.quotations'));
        $response->assertRedirect(route('customer.login'));

        $response = $this->get(route('customer.quotation.detail', $quotation->id));
        $response->assertRedirect(route('customer.login'));
    }

    // =============================================================================
    // ERROR HANDLING WORKFLOW
    // =============================================================================

    /** @test */
    public function quotation_creation_handles_invalid_data_gracefully(): void
    {
        $this->actingAsAdmin($this->adminUser);

        // Try to create quotation with invalid data
        $invalidData = [
            'customer_id' => 99999, // Non-existent customer
            'vehicle_number' => '', // Required field missing
            'idv_vehicle' => -1000, // Invalid value
        ];

        $response = $this->post(route('quotations.store'), $invalidData);

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        // Verify no quotation was created
        $this->assertDatabaseMissing('quotations', ['customer_id' => 99999]);
    }

    /** @test */
    public function pdf_generation_handles_missing_data_gracefully(): void
    {
        $this->actingAsAdmin($this->adminUser);

        // Create quotation without company quotes
        $quotation = Quotation::factory()->create([
            'customer_id' => $this->customer->id,
        ]);

        // Try to generate PDF without company quotes
        $response = $this->get(route('quotations.pdf', $quotation->id));

        // Should handle gracefully (may show error message or empty PDF)
        $response->assertStatus(200);
    }

    // =============================================================================
    // HELPER METHODS
    // =============================================================================

    private function createAddonCovers(): array
    {
        return [
            AddonCover::factory()->create(['name' => 'Zero Depreciation', 'status' => 1]),
            AddonCover::factory()->create(['name' => 'Engine Protection', 'status' => 1]),
            AddonCover::factory()->create(['name' => 'Road Side Assistance', 'status' => 1]),
            AddonCover::factory()->create(['name' => 'NCB Protection', 'status' => 1]),
            AddonCover::factory()->create(['name' => 'Invoice Protection', 'status' => 1]),
        ];
    }
}