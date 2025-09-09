<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\BrokerRepositoryInterface;
use App\Http\Requests\StoreBrokerRequest;
use App\Http\Requests\UpdateBrokerRequest;
use App\Models\Broker;
use App\Services\BrokerService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class BrokerServiceTest extends TestCase
{
    protected BrokerService $brokerService;
    protected $brokerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->brokerRepository = Mockery::mock(BrokerRepositoryInterface::class);
        
        $this->brokerService = new BrokerService(
            $this->brokerRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_brokers_returns_paginated_data()
    {
        $request = new Request([
            'search' => 'ABC Broker',
            'status' => 1
        ]);

        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);
        
        $this->brokerRepository
            ->shouldReceive('getPaginated')
            ->once()
            ->with(
                [
                    'search' => 'ABC Broker',
                    'status' => 1,
                    'from_date' => null,
                    'to_date' => null,
                    'sort_field' => 'name',
                    'sort_order' => 'asc'
                ],
                10
            )
            ->andReturn($expectedPaginator);

        $result = $this->brokerService->getBrokers($request);

        $this->assertSame($expectedPaginator, $result);
    }

    public function test_create_broker()
    {
        $request = Mockery::mock(StoreBrokerRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'name' => 'ABC Broker',
            'contact_person' => 'John Doe',
            'email' => 'john@abcbroker.com',
            'phone' => '1234567890'
        ]);
        
        $broker = new Broker(['id' => 1, 'name' => 'ABC Broker']);
        
        $this->brokerRepository
            ->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'ABC Broker',
                'contact_person' => 'John Doe',
                'email' => 'john@abcbroker.com',
                'phone' => '1234567890'
            ])
            ->andReturn($broker);

        $result = $this->brokerService->createBroker($request);

        $this->assertInstanceOf(Broker::class, $result);
    }

    public function test_update_broker()
    {
        $request = Mockery::mock(UpdateBrokerRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'name' => 'Updated Broker',
            'contact_person' => 'Jane Doe'
        ]);
        
        $broker = new Broker(['id' => 1, 'name' => 'Updated Broker']);
        
        $this->brokerRepository
            ->shouldReceive('update')
            ->once()
            ->with(1, [
                'name' => 'Updated Broker',
                'contact_person' => 'Jane Doe'
            ])
            ->andReturn($broker);

        $result = $this->brokerService->updateBroker(1, $request);

        $this->assertInstanceOf(Broker::class, $result);
    }

    public function test_delete_broker()
    {
        $broker = new Broker(['id' => 1]);
        
        $this->brokerRepository
            ->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->brokerService->deleteBroker($broker);

        $this->assertTrue($result);
    }

    public function test_get_active_brokers()
    {
        $expectedCollection = new Collection([new Broker(['id' => 1, 'name' => 'ABC Broker'])]);
        
        $this->brokerRepository
            ->shouldReceive('getActive')
            ->once()
            ->andReturn($expectedCollection);

        $result = $this->brokerService->getActiveBrokers();

        $this->assertSame($expectedCollection, $result);
    }

    public function test_update_broker_status()
    {
        $this->brokerRepository
            ->shouldReceive('updateStatus')
            ->once()
            ->with(1, 1)
            ->andReturn(true);

        $result = $this->brokerService->updateBrokerStatus(1, 1);

        $this->assertTrue($result);
    }

    public function test_update_broker_status_throws_exception_for_invalid_status()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->brokerService->updateBrokerStatus(1, 3); // Invalid status
    }

    public function test_broker_exists()
    {
        $this->brokerRepository
            ->shouldReceive('exists')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->brokerService->brokerExists(1);

        $this->assertTrue($result);
    }

    public function test_search_brokers()
    {
        $expectedCollection = new Collection([new Broker(['id' => 1, 'name' => 'ABC'])]);
        
        $this->brokerRepository
            ->shouldReceive('search')
            ->once()
            ->with('ABC')
            ->andReturn($expectedCollection);

        $result = $this->brokerService->searchBrokers('ABC');

        $this->assertSame($expectedCollection, $result);
    }
}