<?php

namespace Tests\Unit\Modules\Customer;

use Tests\TestCase;
use App\Modules\Customer\Services\CustomerService;
use App\Modules\Customer\Contracts\CustomerServiceInterface;
use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Services\FileUploadService;
use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;

class CustomerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerServiceInterface $customerService;
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

    public function test_can_get_customers_with_pagination()
    {
        // Arrange
        $request = new Request([
            'search' => 'John',
            'type' => 'Retail',
            'status' => '1'
        ]);
        
        $mockPaginator = Mockery::mock(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class);
        
        $this->customerRepository
            ->shouldReceive('getPaginated')
            ->once()
            ->with([
                'search' => 'John',
                'type' => 'Retail',
                'status' => '1',
                'from_date' => null,
                'to_date' => null,
                'sort_field' => 'name',
                'sort_order' => 'asc',
            ], 10)
            ->andReturn($mockPaginator);

        // Act
        $result = $this->customerService->getCustomers($request);

        // Assert
        $this->assertSame($mockPaginator, $result);
    }

    public function test_can_create_customer_successfully()
    {
        // Arrange
        $requestData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'mobile_number' => '9876543210',
            'status' => 1,
            'type' => 'Retail',
        ];

        $request = Mockery::mock(StoreCustomerRequest::class);
        foreach ($requestData as $key => $value) {
            $request->$key = $value;
        }

        $customer = new Customer($requestData);
        $customer->id = 1;

        $this->customerRepository
            ->shouldReceive('create')
            ->once()
            ->with($requestData)
            ->andReturn($customer);

        // Act
        $result = $this->customerService->createCustomer($request);

        // Assert
        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals('john@example.com', $result->email);
    }

    public function test_can_update_customer_successfully()
    {
        // Arrange
        $customer = new Customer([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 1
        ]);

        $updateData = [
            'name' => 'John Updated',
            'email' => 'john.updated@example.com',
            'status' => 1,
        ];

        $request = Mockery::mock(UpdateCustomerRequest::class);
        foreach ($updateData as $key => $value) {
            $request->$key = $value;
        }

        $customer->shouldReceive('only')
            ->once()
            ->andReturn(['name' => 'John Doe', 'email' => 'john@example.com']);

        $this->customerRepository
            ->shouldReceive('update')
            ->once()
            ->with(1, Mockery::type('array'))
            ->andReturn(true);

        $customer->shouldReceive('fresh')
            ->once()
            ->andReturn($customer);

        // Act
        $result = $this->customerService->updateCustomer($request, $customer);

        // Assert
        $this->assertTrue($result);
    }

    public function test_can_delete_customer_successfully()
    {
        // Arrange
        $customer = new Customer(['id' => 1]);

        $this->customerRepository
            ->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        // Act
        $result = $this->customerService->deleteCustomer($customer);

        // Assert
        $this->assertTrue($result);
    }

    public function test_can_get_customer_statistics()
    {
        // Arrange
        $this->customerRepository
            ->shouldReceive('count')
            ->once()
            ->andReturn(100);

        $mockRetailCustomers = collect([
            (object)['status' => 1],
            (object)['status' => 1],
        ]);

        $this->customerRepository
            ->shouldReceive('getByType')
            ->once()
            ->with('Retail')
            ->andReturn($mockRetailCustomers);

        $mockCorporateCustomers = collect([
            (object)['id' => 1],
        ]);

        $this->customerRepository
            ->shouldReceive('getByType')
            ->once()
            ->with('Corporate')
            ->andReturn($mockCorporateCustomers);

        // Act
        $statistics = $this->customerService->getCustomerStatistics();

        // Assert
        $this->assertIsArray($statistics);
        $this->assertEquals(100, $statistics['total']);
        $this->assertEquals(2, $statistics['active']);
        $this->assertEquals(1, $statistics['corporate']);
    }

    public function test_can_search_customers()
    {
        // Arrange
        $searchResults = collect([
            new Customer(['name' => 'John Doe']),
            new Customer(['name' => 'Jane Doe']),
        ]);

        $this->customerRepository
            ->shouldReceive('search')
            ->once()
            ->with('Doe')
            ->andReturn($searchResults);

        // Act
        $result = $this->customerService->searchCustomers('Doe');

        // Assert
        $this->assertCount(2, $result);
    }

    public function test_customer_exists_check()
    {
        // Arrange
        $this->customerRepository
            ->shouldReceive('exists')
            ->once()
            ->with(1)
            ->andReturn(true);

        // Act
        $result = $this->customerService->customerExists(1);

        // Assert
        $this->assertTrue($result);
    }

    public function test_find_customer_by_email()
    {
        // Arrange
        $customer = new Customer(['email' => 'john@example.com']);

        $this->customerRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with('john@example.com')
            ->andReturn($customer);

        // Act
        $result = $this->customerService->findByEmail('john@example.com');

        // Assert
        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals('john@example.com', $result->email);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}