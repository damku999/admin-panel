<?php

namespace Tests\Feature\Modules\Customer;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_get_customers_list()
    {
        // Arrange
        Customer::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/customers');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'email',
                             'mobile_number',
                             'status',
                             'type'
                         ]
                     ],
                     'pagination'
                 ]);

        $this->assertTrue($response->json('success'));
    }

    public function test_can_create_customer()
    {
        // Arrange
        $customerData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'mobile_number' => '9876543210',
            'status' => 1,
            'type' => 'Retail',
            'date_of_birth' => '1990-01-01',
        ];

        // Act
        $response = $this->postJson('/api/customers', $customerData);

        // Assert
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'name',
                         'email',
                         'mobile_number',
                         'status',
                         'type'
                     ]
                 ]);

        $this->assertTrue($response->json('success'));
        $this->assertDatabaseHas('customers', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_can_get_single_customer()
    {
        // Arrange
        $customer = Customer::factory()->create();

        // Act
        $response = $this->getJson("/api/customers/{$customer->id}");

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'id',
                         'name',
                         'email',
                         'mobile_number',
                         'status',
                         'type'
                     ]
                 ]);

        $this->assertEquals($customer->id, $response->json('data.id'));
    }

    public function test_can_update_customer()
    {
        // Arrange
        $customer = Customer::factory()->create();
        $updateData = [
            'name' => 'Updated Name',
            'email' => $customer->email, // Keep original email
            'mobile_number' => $customer->mobile_number,
            'status' => 1,
            'type' => $customer->type,
        ];

        // Act
        $response = $this->putJson("/api/customers/{$customer->id}", $updateData);

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_can_delete_customer()
    {
        // Arrange
        $customer = Customer::factory()->create();

        // Act
        $response = $this->deleteJson("/api/customers/{$customer->id}");

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    public function test_can_update_customer_status()
    {
        // Arrange
        $customer = Customer::factory()->create(['status' => 1]);

        // Act
        $response = $this->patchJson("/api/customers/{$customer->id}/status", [
            'status' => 0
        ]);

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'status' => 0,
        ]);
    }

    public function test_can_search_customers()
    {
        // Arrange
        $customer1 = Customer::factory()->create(['name' => 'John Doe']);
        $customer2 = Customer::factory()->create(['name' => 'Jane Smith']);

        // Act
        $response = $this->getJson('/api/customers/search/John');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data',
                     'query',
                     'count'
                 ]);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('John Doe', $data[0]['name']);
    }

    public function test_can_get_customers_by_type()
    {
        // Arrange
        Customer::factory()->create(['type' => 'Retail']);
        Customer::factory()->create(['type' => 'Corporate']);

        // Act
        $response = $this->getJson('/api/customers/type/Retail');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data',
                     'type',
                     'count'
                 ]);

        $data = $response->json('data');
        $this->assertEquals('Retail', $data[0]['type']);
    }

    public function test_can_get_customer_statistics()
    {
        // Arrange
        Customer::factory()->count(3)->create(['type' => 'Retail', 'status' => 1]);
        Customer::factory()->count(2)->create(['type' => 'Corporate']);

        // Act
        $response = $this->getJson('/api/customers/stats/overview');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'total',
                         'active',
                         'corporate'
                     ]
                 ]);

        $statistics = $response->json('data');
        $this->assertEquals(5, $statistics['total']);
        $this->assertEquals(2, $statistics['corporate']);
    }

    public function test_requires_authentication()
    {
        // Arrange - Clear authentication
        $this->app['auth']->forgetGuards();

        // Act
        $response = $this->getJson('/api/customers');

        // Assert
        $response->assertStatus(401);
    }

    public function test_validates_customer_creation_data()
    {
        // Act
        $response = $this->postJson('/api/customers', [
            'name' => '', // Required field empty
            'email' => 'invalid-email', // Invalid email
        ]);

        // Assert
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_can_get_active_customers_for_selection()
    {
        // Arrange
        Customer::factory()->create(['status' => 1, 'name' => 'Active Customer']);
        Customer::factory()->create(['status' => 0, 'name' => 'Inactive Customer']);

        // Act
        $response = $this->getJson('/api/customers/active/list');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'email',
                             'mobile_number',
                             'type'
                         ]
                     ],
                     'count'
                 ]);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Active Customer', $data[0]['name']);
    }
}