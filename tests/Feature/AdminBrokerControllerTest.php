<?php

namespace Tests\Feature;

use App\Models\Broker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminBrokerControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker; // Use RefreshDatabase to avoid affecting existing data

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user without affecting existing data
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_broker_index_page_loads_successfully()
    {
        // Create test data in isolated test database
        Broker::factory()->count(3)->create();
        
        $response = $this->get(route('brokers.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('brokers.index');
        $response->assertViewHas('brokers');
    }

    public function test_broker_create_page_loads_successfully()
    {
        $response = $this->get(route('brokers.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('brokers.create');
    }

    public function test_broker_store_creates_new_broker()
    {
        $brokerData = [
            'name' => 'Test Broker',
            'contact_person' => 'John Doe',
            'email' => 'john@testbroker.com',
            'phone' => '1234567890',
            'address' => '123 Test Street',
            'status' => 1
        ];
        
        $response = $this->post(route('brokers.store'), $brokerData);
        
        $response->assertRedirect(route('brokers.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('brokers', [
            'name' => 'Test Broker',
            'email' => 'john@testbroker.com'
        ]);
    }

    public function test_broker_store_validates_required_fields()
    {
        $response = $this->post(route('brokers.store'), []);
        
        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_broker_show_displays_broker_details()
    {
        $broker = Broker::factory()->create();
        
        $response = $this->get(route('brokers.show', $broker));
        
        $response->assertStatus(200);
        $response->assertViewIs('brokers.show');
        $response->assertViewHas('broker', $broker);
    }

    public function test_broker_edit_page_loads_successfully()
    {
        $broker = Broker::factory()->create();
        
        $response = $this->get(route('brokers.edit', $broker));
        
        $response->assertStatus(200);
        $response->assertViewIs('brokers.edit');
        $response->assertViewHas('broker', $broker);
    }

    public function test_broker_update_modifies_existing_broker()
    {
        $broker = Broker::factory()->create();
        
        $updateData = [
            'name' => 'Updated Broker Name',
            'contact_person' => 'Jane Smith',
            'email' => $broker->email, // Keep same email
            'phone' => '9876543210',
            'address' => 'Updated Address',
            'status' => 1
        ];
        
        $response = $this->put(route('brokers.update', $broker), $updateData);
        
        $response->assertRedirect(route('brokers.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('brokers', [
            'id' => $broker->id,
            'name' => 'Updated Broker Name',
            'contact_person' => 'Jane Smith'
        ]);
    }

    public function test_broker_destroy_soft_deletes_broker()
    {
        $broker = Broker::factory()->create();
        
        $response = $this->delete(route('brokers.destroy', $broker));
        
        $response->assertRedirect(route('brokers.index'));
        $response->assertSessionHas('success');
        
        $this->assertSoftDeleted('brokers', ['id' => $broker->id]);
    }

    public function test_broker_status_update_changes_status()
    {
        $broker = Broker::factory()->create(['status' => 1]);
        
        $response = $this->patch(route('brokers.updateStatus', [$broker, 0]));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('brokers', [
            'id' => $broker->id,
            'status' => 0
        ]);
    }

    public function test_broker_export_generates_excel_file()
    {
        Broker::factory()->count(5)->create();
        
        $response = $this->get(route('brokers.export'));
        
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_broker_search_filters_results()
    {
        Broker::factory()->create(['name' => 'ABC Broker']);
        Broker::factory()->create(['name' => 'XYZ Broker']);
        
        $response = $this->get(route('brokers.index', ['search' => 'ABC']));
        
        $response->assertStatus(200);
        $response->assertSee('ABC Broker');
        $response->assertDontSee('XYZ Broker');
    }

    public function test_unauthorized_user_cannot_access_broker_pages()
    {
        auth()->logout();
        
        $response = $this->get(route('brokers.index'));
        
        $response->assertRedirect(route('login'));
    }
}