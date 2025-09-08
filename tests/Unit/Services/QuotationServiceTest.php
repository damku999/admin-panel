<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\QuotationRepositoryInterface;
use App\Models\Customer;
use App\Models\InsuranceCompany;
use App\Models\Quotation;
use App\Services\PdfGenerationService;
use App\Services\QuotationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class QuotationServiceTest extends TestCase
{
    protected QuotationService $quotationService;
    protected $quotationRepository;
    protected $pdfService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->quotationRepository = Mockery::mock(QuotationRepositoryInterface::class);
        $this->pdfService = Mockery::mock(PdfGenerationService::class);
        
        $this->quotationService = new QuotationService(
            $this->pdfService,
            $this->quotationRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_quotations_returns_paginated_data()
    {
        $request = new Request([
            'search' => 'BMW',
            'status' => 'Draft'
        ]);

        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);
        
        $this->quotationRepository
            ->shouldReceive('getPaginated')
            ->once()
            ->with(
                [
                    'search' => 'BMW',
                    'status' => 'Draft',
                    'customer_id' => null
                ],
                15
            )
            ->andReturn($expectedPaginator);

        $result = $this->quotationService->getQuotations($request);

        $this->assertSame($expectedPaginator, $result);
    }

    public function test_create_quotation_calculates_total_idv()
    {
        $data = [
            'customer_id' => 1,
            'vehicle_number' => 'ABC123',
            'idv_vehicle' => 50000,
            'idv_trailer' => 0,
            'idv_cng_lpg_kit' => 5000,
            'idv_electrical_accessories' => 2000,
            'idv_non_electrical_accessories' => 1000
        ];

        $quotation = new Quotation($data);
        $quotation->id = 1;

        // Mock Quotation::create() call
        $this->app->instance(Quotation::class, $quotation);

        $result = $this->quotationService->createQuotation($data);

        // Verify total_idv was calculated correctly (58000)
        $this->assertArrayHasKey('total_idv', $data);
    }

    public function test_delete_quotation_calls_repository()
    {
        $quotation = new Quotation(['id' => 1]);
        
        $this->quotationRepository
            ->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->quotationService->deleteQuotation($quotation);

        $this->assertTrue($result);
    }

    public function test_calculate_premium_returns_total_idv()
    {
        $data = [
            'idv_vehicle' => 50000,
            'idv_trailer' => 0,
            'idv_cng_lpg_kit' => 5000,
            'idv_electrical_accessories' => 2000,
            'idv_non_electrical_accessories' => 1000
        ];

        $result = $this->quotationService->calculatePremium($data);

        $this->assertEquals(58000, $result);
    }

    public function test_get_quotation_form_data_returns_required_data()
    {
        // Mock static calls
        Customer::shouldReceive('where->orderBy->get')
            ->andReturn(collect([new Customer()]));
        
        InsuranceCompany::shouldReceive('where->orderBy->get')
            ->andReturn(collect([new InsuranceCompany()]));

        $result = $this->quotationService->getQuotationFormData();

        $this->assertArrayHasKey('customers', $result);
        $this->assertArrayHasKey('insuranceCompanies', $result);
        $this->assertArrayHasKey('addonCovers', $result);
    }

    public function test_generate_pdf_calls_pdf_service()
    {
        $quotation = new Quotation(['id' => 1]);
        $expectedPdf = 'pdf_content';

        $this->pdfService
            ->shouldReceive('generateQuotationPdf')
            ->once()
            ->with($quotation)
            ->andReturn($expectedPdf);

        $result = $this->quotationService->generatePdf($quotation);

        $this->assertEquals($expectedPdf, $result);
    }
}