<?php

namespace Tests\Unit\Repositories;

use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class CustomerRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CustomerRepository();
    }

    public function test_get_all_returns_customers()
    {
        Customer::factory()->count(3)->create();

        $customers = $this->repository->getAll();

        $this->assertCount(3, $customers);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $customers);
    }

    public function test_get_all_with_type_filter()
    {
        Customer::factory()->create(['type' => 'Retail']);
        Customer::factory()->create(['type' => 'Corporate']);

        $customers = $this->repository->getAll(['type' => 'Retail']);

        $this->assertCount(1, $customers);
        $this->assertEquals('Retail', $customers->first()->type);
    }

    public function test_get_paginated_returns_paginator()
    {
        Customer::factory()->count(15)->create();

        $result = $this->repository->getPaginated([], 10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(15, $result->total());
    }

    public function test_get_paginated_with_search()
    {
        Customer::factory()->create(['name' => 'John Doe']);
        Customer::factory()->create(['name' => 'Jane Smith']);

        $result = $this->repository->getPaginated(['search' => 'John']);

        $this->assertEquals(1, $result->total());
        $this->assertEquals('John Doe', $result->items()[0]->name);
    }

    public function test_find_by_id()
    {
        $customer = Customer::factory()->create();

        $result = $this->repository->findById($customer->id);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals($customer->id, $result->id);
    }

    public function test_find_by_id_returns_null_when_not_found()
    {
        $result = $this->repository->findById(999);

        $this->assertNull($result);
    }

    public function test_find_by_email()
    {
        $customer = Customer::factory()->create(['email' => 'test@example.com']);

        $result = $this->repository->findByEmail('test@example.com');

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals('test@example.com', $result->email);
    }

    public function test_find_by_mobile_number()
    {
        $customer = Customer::factory()->create(['mobile_number' => '1234567890']);

        $result = $this->repository->findByMobileNumber('1234567890');

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals('1234567890', $result->mobile_number);
    }

    public function test_create()
    {
        $data = [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'mobile_number' => '1234567890',
            'type' => 'Retail',
            'status' => 1
        ];

        $customer = $this->repository->create($data);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals('Test Customer', $customer->name);
        $this->assertDatabaseHas('customers', $data);
    }

    public function test_update()
    {
        $customer = Customer::factory()->create(['name' => 'Original Name']);

        $result = $this->repository->update($customer->id, ['name' => 'Updated Name']);

        $this->assertTrue($result);
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Name'
        ]);
    }

    public function test_delete()
    {
        $customer = Customer::factory()->create();

        $result = $this->repository->delete($customer->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    public function test_get_active()
    {
        Customer::factory()->create(['status' => 1]);
        Customer::factory()->create(['status' => 0]);

        $activeCustomers = $this->repository->getActive();

        $this->assertCount(1, $activeCustomers);
        $this->assertEquals(1, $activeCustomers->first()->status);
    }

    public function test_get_by_type()
    {
        Customer::factory()->create(['type' => 'Retail']);
        Customer::factory()->create(['type' => 'Corporate']);

        $retailCustomers = $this->repository->getByType('Retail');

        $this->assertCount(1, $retailCustomers);
        $this->assertEquals('Retail', $retailCustomers->first()->type);
    }

    public function test_search()
    {
        Customer::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        Customer::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

        $results = $this->repository->search('John');

        $this->assertCount(1, $results);
        $this->assertEquals('John Doe', $results->first()->name);
    }

    public function test_update_status()
    {
        $customer = Customer::factory()->create(['status' => 0]);

        $result = $this->repository->updateStatus($customer->id, 1);

        $this->assertTrue($result);
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'status' => 1
        ]);
    }

    public function test_exists()
    {
        $customer = Customer::factory()->create();

        $this->assertTrue($this->repository->exists($customer->id));
        $this->assertFalse($this->repository->exists(999));
    }

    public function test_count()
    {
        Customer::factory()->count(5)->create();

        $count = $this->repository->count();

        $this->assertEquals(5, $count);
    }
}