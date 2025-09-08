<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\FileUploadService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class CustomerServiceTest extends TestCase
{
    protected CustomerService $customerService;
    protected $customerRepository;
    protected $fileUploadService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customerRepository = Mockery::mock(CustomerRepositoryInterface::class);
        $this->fileUploadService = Mockery::mock(FileUploadService::class);
        
        $this->customerService = new CustomerService(
            $this->customerRepository,
            $this->fileUploadService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_customers_returns_paginated_data()
    {
        $request = new Request([
            'search' => 'John',
            'type' => 'Retail',
            'status' => 1
        ]);

        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);
        
        $this->customerRepository
            ->shouldReceive('getPaginated')
            ->once()
            ->with(
                [
                    'search' => 'John',
                    'type' => 'Retail',
                    'status' => 1,
                    'from_date' => null,
                    'to_date' => null,
                    'sort_field' => 'name',
                    'sort_order' => 'asc'
                ],
                10
            )
            ->andReturn($expectedPaginator);

        $result = $this->customerService->getCustomers($request);

        $this->assertSame($expectedPaginator, $result);
    }

    public function test_create_customer_creates_and_handles_documents()
    {
        $request = Mockery::mock(StoreCustomerRequest::class);
        $request->shouldReceive('validated')->andReturn([]);
        $request->shouldReceive('getAttribute')->with('name')->andReturn('John Doe');
        $request->shouldReceive('getAttribute')->with('email')->andReturn('john@example.com');
        $request->shouldReceive('getAttribute')->with('mobile_number')->andReturn('1234567890');
        
        $customer = new Customer(['id' => 1, 'name' => 'John Doe']);
        
        $this->customerRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($customer);

        $request->shouldReceive('hasFile')->andReturn(false);

        $result = $this->customerService->createCustomer($request);

        $this->assertInstanceOf(Customer::class, $result);
    }

    public function test_update_customer_status_validates_and_updates()
    {
        $this->customerRepository
            ->shouldReceive('updateStatus')
            ->once()
            ->with(1, 1)
            ->andReturn(true);

        $result = $this->customerService->updateCustomerStatus(1, 1);

        $this->assertTrue($result);
    }

    public function test_update_customer_status_throws_exception_for_invalid_status()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->customerService->updateCustomerStatus(1, 3); // Invalid status
    }

    public function test_delete_customer_calls_repository()
    {
        $customer = new Customer(['id' => 1]);
        
        $this->customerRepository
            ->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->customerService->deleteCustomer($customer);

        $this->assertTrue($result);
    }

    public function test_get_active_customers_for_selection()
    {
        $expectedCollection = new Collection([new Customer(['id' => 1, 'name' => 'John'])]);
        
        $this->customerRepository
            ->shouldReceive('getActive')
            ->once()
            ->andReturn($expectedCollection);

        $result = $this->customerService->getActiveCustomersForSelection();

        $this->assertSame($expectedCollection, $result);
    }

    public function test_search_customers()
    {
        $expectedCollection = new Collection([new Customer(['id' => 1, 'name' => 'John'])]);
        
        $this->customerRepository
            ->shouldReceive('search')
            ->once()
            ->with('John')
            ->andReturn($expectedCollection);

        $result = $this->customerService->searchCustomers('John');

        $this->assertSame($expectedCollection, $result);
    }

    public function test_customer_exists()
    {
        $this->customerRepository
            ->shouldReceive('exists')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->customerService->customerExists(1);

        $this->assertTrue($result);
    }

    public function test_find_by_email()
    {
        $customer = new Customer(['email' => 'john@example.com']);
        
        $this->customerRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with('john@example.com')
            ->andReturn($customer);

        $result = $this->customerService->findByEmail('john@example.com');

        $this->assertSame($customer, $result);
    }

    public function test_find_by_mobile_number()
    {
        $customer = new Customer(['mobile_number' => '1234567890']);
        
        $this->customerRepository
            ->shouldReceive('findByMobileNumber')
            ->once()
            ->with('1234567890')
            ->andReturn($customer);

        $result = $this->customerService->findByMobileNumber('1234567890');

        $this->assertSame($customer, $result);
    }
}