<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\PolicyTypeRepositoryInterface;
use App\Http\Requests\StorePolicyTypeRequest;
use App\Http\Requests\UpdatePolicyTypeRequest;
use App\Models\PolicyType;
use App\Services\PolicyService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class PolicyServiceTest extends TestCase
{
    protected PolicyService $policyService;
    protected $policyTypeRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock repository to avoid database interaction
        $this->policyTypeRepository = Mockery::mock(PolicyTypeRepositoryInterface::class);
        
        $this->policyService = new PolicyService(
            $this->policyTypeRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_policy_types_returns_paginated_data()
    {
        $request = new Request([
            'search' => 'Motor',
            'status' => 1
        ]);

        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);
        
        $this->policyTypeRepository
            ->shouldReceive('getPaginated')
            ->once()
            ->with(
                [
                    'search' => 'Motor',
                    'status' => 1,
                    'from_date' => null,
                    'to_date' => null,
                    'sort_field' => 'name',
                    'sort_order' => 'asc'
                ],
                10
            )
            ->andReturn($expectedPaginator);

        $result = $this->policyService->getPolicyTypes($request);

        $this->assertSame($expectedPaginator, $result);
    }

    public function test_create_policy_type()
    {
        $request = Mockery::mock(StorePolicyTypeRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'name' => 'Motor Insurance',
            'description' => 'Vehicle insurance coverage',
            'status' => 1
        ]);
        
        $policyType = new PolicyType(['id' => 1, 'name' => 'Motor Insurance']);
        
        $this->policyTypeRepository
            ->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'Motor Insurance',
                'description' => 'Vehicle insurance coverage',
                'status' => 1
            ])
            ->andReturn($policyType);

        $result = $this->policyService->createPolicyType($request);

        $this->assertInstanceOf(PolicyType::class, $result);
    }

    public function test_update_policy_type()
    {
        $request = Mockery::mock(UpdatePolicyTypeRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'name' => 'Updated Motor Insurance',
            'description' => 'Updated description'
        ]);
        
        $policyType = new PolicyType(['id' => 1, 'name' => 'Updated Motor Insurance']);
        
        $this->policyTypeRepository
            ->shouldReceive('update')
            ->once()
            ->with(1, [
                'name' => 'Updated Motor Insurance',
                'description' => 'Updated description'
            ])
            ->andReturn($policyType);

        $result = $this->policyService->updatePolicyType(1, $request);

        $this->assertInstanceOf(PolicyType::class, $result);
    }

    public function test_delete_policy_type()
    {
        $policyType = new PolicyType(['id' => 1]);
        
        $this->policyTypeRepository
            ->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->policyService->deletePolicyType($policyType);

        $this->assertTrue($result);
    }

    public function test_get_active_policy_types()
    {
        $expectedCollection = new Collection([new PolicyType(['id' => 1, 'name' => 'Motor'])]);
        
        $this->policyTypeRepository
            ->shouldReceive('getActive')
            ->once()
            ->andReturn($expectedCollection);

        $result = $this->policyService->getActivePolicyTypes();

        $this->assertSame($expectedCollection, $result);
    }

    public function test_update_policy_type_status()
    {
        $this->policyTypeRepository
            ->shouldReceive('updateStatus')
            ->once()
            ->with(1, 1)
            ->andReturn(true);

        $result = $this->policyService->updatePolicyTypeStatus(1, 1);

        $this->assertTrue($result);
    }

    public function test_update_policy_type_status_throws_exception_for_invalid_status()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->policyService->updatePolicyTypeStatus(1, 3); // Invalid status
    }

    public function test_policy_type_exists()
    {
        $this->policyTypeRepository
            ->shouldReceive('exists')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->policyService->policyTypeExists(1);

        $this->assertTrue($result);
    }

    public function test_search_policy_types()
    {
        $expectedCollection = new Collection([new PolicyType(['id' => 1, 'name' => 'Motor'])]);
        
        $this->policyTypeRepository
            ->shouldReceive('search')
            ->once()
            ->with('Motor')
            ->andReturn($expectedCollection);

        $result = $this->policyService->searchPolicyTypes('Motor');

        $this->assertSame($expectedCollection, $result);
    }

    public function test_get_policy_type_by_id()
    {
        $policyType = new PolicyType(['id' => 1, 'name' => 'Motor Insurance']);
        
        $this->policyTypeRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($policyType);

        $result = $this->policyService->getPolicyTypeById(1);

        $this->assertSame($policyType, $result);
    }
}