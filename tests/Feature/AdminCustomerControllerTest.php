<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use App\Models\FamilyGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCustomerControllerTest extends TestCase
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

    public function test_customer_index_page_loads_successfully()
    {
        // Create test data in isolated test database
        Customer::factory()->count(3)->create();
        
        $response = $this->get(route('customers.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('customers.index');
        $response->assertViewHas('customers');
    }

    public function test_customer_create_page_loads_successfully()
    {
        $response = $this->get(route('customers.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('customers.create');
    }

    public function test_customer_store_creates_new_customer()
    {
        Storage::fake('public');
        
        $customerData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'mobile_number' => '1234567890',
            'address' => '123 Test Street',
            'customer_type' => 'Retail',
            'status' => 1,
            'aadhar_card' => UploadedFile::fake()->create('aadhar.pdf', 100),
            'pan_card' => UploadedFile::fake()->create('pan.pdf', 100)
        ];
        
        $response = $this->post(route('customers.store'), $customerData);
        
        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('customers', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'mobile_number' => '1234567890'
        ]);
        
        // Check file uploads
        $customer = Customer::where('email', 'john@example.com')->first();
        $this->assertNotNull($customer->aadhar_card_path);
        $this->assertNotNull($customer->pan_card_path);
    }

    public function test_customer_store_validates_required_fields()
    {
        $response = $this->post(route('customers.store'), []);
        
        $response->assertSessionHasErrors(['name', 'email', 'mobile_number']);
    }

    public function test_customer_store_validates_unique_email()
    {
        $existingCustomer = Customer::factory()->create();
        
        $customerData = [
            'name' => 'Jane Doe',
            'email' => $existingCustomer->email, // Duplicate email
            'mobile_number' => '9876543210'
        ];
        
        $response = $this->post(route('customers.store'), $customerData);
        
        $response->assertSessionHasErrors(['email']);
    }

    public function test_customer_show_displays_customer_details()
    {
        $customer = Customer::factory()->create();
        
        $response = $this->get(route('customers.show', $customer));
        
        $response->assertStatus(200);
        $response->assertViewIs('customers.show');
        $response->assertViewHas('customer', $customer);
    }

    public function test_customer_edit_page_loads_successfully()
    {
        $customer = Customer::factory()->create();
        
        $response = $this->get(route('customers.edit', $customer));
        
        $response->assertStatus(200);
        $response->assertViewIs('customers.edit');
        $response->assertViewHas('customer', $customer);
    }

    public function test_customer_update_modifies_existing_customer()
    {
        $customer = Customer::factory()->create();
        
        $updateData = [
            'name' => 'Updated John Doe',
            'email' => $customer->email, // Keep same email
            'mobile_number' => '9876543210',
            'address' => 'Updated Address',
            'customer_type' => 'Corporate',
            'status' => 1
        ];
        
        $response = $this->put(route('customers.update', $customer), $updateData);
        
        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated John Doe',
            'customer_type' => 'Corporate'
        ]);
    }

    public function test_customer_destroy_soft_deletes_customer()
    {
        $customer = Customer::factory()->create();
        
        $response = $this->delete(route('customers.destroy', $customer));
        
        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success');
        
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    public function test_customer_status_update_changes_status()
    {
        $customer = Customer::factory()->create(['status' => 1]);
        
        $response = $this->patch(route('customers.updateStatus', [$customer, 0]));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'status' => 0
        ]);
    }

    public function test_customer_export_generates_excel_file()
    {
        Customer::factory()->count(5)->create();
        
        $response = $this->get(route('customers.export'));
        
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_customer_search_filters_results()
    {
        Customer::factory()->create(['name' => 'John Smith']);
        Customer::factory()->create(['name' => 'Jane Doe']);
        
        $response = $this->get(route('customers.index', ['search' => 'John']));
        
        $response->assertStatus(200);
        $response->assertSee('John Smith');
        $response->assertDontSee('Jane Doe');
    }

    public function test_customer_type_filter_works()
    {
        Customer::factory()->create(['name' => 'Retail Customer', 'customer_type' => 'Retail']);
        Customer::factory()->create(['name' => 'Corporate Customer', 'customer_type' => 'Corporate']);
        
        $response = $this->get(route('customers.index', ['type' => 'Retail']));
        
        $response->assertStatus(200);
        $response->assertSee('Retail Customer');
        $response->assertDontSee('Corporate Customer');
    }

    public function test_customer_family_group_assignment()
    {
        $familyGroup = FamilyGroup::factory()->create();
        $customer = Customer::factory()->create();
        
        $updateData = [
            'name' => $customer->name,
            'email' => $customer->email,
            'mobile_number' => $customer->mobile_number,
            'family_group_id' => $familyGroup->id,
            'status' => 1
        ];
        
        $response = $this->put(route('customers.update', $customer), $updateData);
        
        $response->assertRedirect(route('customers.index'));
        
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'family_group_id' => $familyGroup->id
        ]);
    }

    public function test_unauthorized_user_cannot_access_customer_pages()
    {
        auth()->logout();
        
        $response = $this->get(route('customers.index'));
        
        $response->assertRedirect(route('login'));
    }
}