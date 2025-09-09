<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\InsuranceCompanyRepositoryInterface;
use App\Http\Requests\StoreInsuranceCompanyRequest;
use App\Http\Requests\UpdateInsuranceCompanyRequest;
use App\Models\InsuranceCompany;
use App\Services\InsuranceCompanyService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class InsuranceCompanyServiceTest extends TestCase
{
    protected InsuranceCompanyService $insuranceCompanyService;
    protected $insuranceCompanyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->insuranceCompanyRepository = Mockery::mock(InsuranceCompanyRepositoryInterface::class);
        
        $this->insuranceCompanyService = new InsuranceCompanyService(
            $this->insuranceCompanyRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_insurance_companies_returns_paginated_data()
    {
        $request = new Request([
            'search' => 'ABC Insurance',
            'status' => 1
        ]);

        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);
        
        $this->insuranceCompanyRepository
            ->shouldReceive('getPaginated')
            ->once()
            ->with(
                [
                    'search' => 'ABC Insurance',
                    'status' => 1,
                    'from_date' => null,
                    'to_date' => null,
                    'sort_field' => 'name',
                    'sort_order' => 'asc'
                ],
                10
            )
            ->andReturn($expectedPaginator);

        $result = $this->insuranceCompanyService->getInsuranceCompanies($request);

        $this->assertSame($expectedPaginator, $result);
    }

    public function test_create_insurance_company()
    {
        $request = Mockery::mock(StoreInsuranceCompanyRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'name' => 'ABC Insurance',
            'contact_person' => 'John Doe',
            'email' => 'john@abcinsurance.com',
            'phone' => '1234567890'
        ]);
        
        $company = new InsuranceCompany(['id' => 1, 'name' => 'ABC Insurance']);
        
        $this->insuranceCompanyRepository
            ->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'ABC Insurance',
                'contact_person' => 'John Doe',
                'email' => 'john@abcinsurance.com',
                'phone' => '1234567890'
            ])
            ->andReturn($company);

        $result = $this->insuranceCompanyService->createInsuranceCompany($request);

        $this->assertInstanceOf(InsuranceCompany::class, $result);
    }

    public function test_update_insurance_company()
    {
        $request = Mockery::mock(UpdateInsuranceCompanyRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'name' => 'Updated Insurance',
            'contact_person' => 'Jane Doe'
        ]);
        
        $company = new InsuranceCompany(['id' => 1, 'name' => 'Updated Insurance']);
        
        $this->insuranceCompanyRepository
            ->shouldReceive('update')
            ->once()
            ->with(1, [
                'name' => 'Updated Insurance',
                'contact_person' => 'Jane Doe'
            ])
            ->andReturn($company);

        $result = $this->insuranceCompanyService->updateInsuranceCompany(1, $request);

        $this->assertInstanceOf(InsuranceCompany::class, $result);
    }

    public function test_delete_insurance_company()
    {
        $company = new InsuranceCompany(['id' => 1]);
        
        $this->insuranceCompanyRepository
            ->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->insuranceCompanyService->deleteInsuranceCompany($company);

        $this->assertTrue($result);
    }

    public function test_get_active_insurance_companies()
    {
        $expectedCollection = new Collection([new InsuranceCompany(['id' => 1, 'name' => 'ABC Insurance'])]);
        
        $this->insuranceCompanyRepository
            ->shouldReceive('getActive')
            ->once()
            ->andReturn($expectedCollection);

        $result = $this->insuranceCompanyService->getActiveInsuranceCompanies();

        $this->assertSame($expectedCollection, $result);
    }

    public function test_update_insurance_company_status()
    {
        $this->insuranceCompanyRepository
            ->shouldReceive('updateStatus')
            ->once()
            ->with(1, 1)
            ->andReturn(true);

        $result = $this->insuranceCompanyService->updateInsuranceCompanyStatus(1, 1);

        $this->assertTrue($result);
    }

    public function test_update_insurance_company_status_throws_exception_for_invalid_status()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->insuranceCompanyService->updateInsuranceCompanyStatus(1, 3); // Invalid status
    }

    public function test_insurance_company_exists()
    {
        $this->insuranceCompanyRepository
            ->shouldReceive('exists')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->insuranceCompanyService->insuranceCompanyExists(1);

        $this->assertTrue($result);
    }

    public function test_search_insurance_companies()
    {
        $expectedCollection = new Collection([new InsuranceCompany(['id' => 1, 'name' => 'ABC'])]);
        
        $this->insuranceCompanyRepository
            ->shouldReceive('search')
            ->once()
            ->with('ABC')
            ->andReturn($expectedCollection);

        $result = $this->insuranceCompanyService->searchInsuranceCompanies('ABC');

        $this->assertSame($expectedCollection, $result);
    }
}