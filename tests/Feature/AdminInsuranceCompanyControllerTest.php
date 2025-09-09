<?php

namespace Tests\Feature;

use App\Models\InsuranceCompany;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminInsuranceCompanyControllerTest extends TestCase
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

    public function test_insurance_company_index_page_loads_successfully()
    {
        // Create test data in isolated test database
        InsuranceCompany::factory()->count(3)->create();
        
        $response = $this->get(route('insurance-companies.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('insurance-companies.index');
        $response->assertViewHas('insuranceCompanies');
    }

    public function test_insurance_company_create_page_loads_successfully()
    {
        $response = $this->get(route('insurance-companies.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('insurance-companies.create');
    }

    public function test_insurance_company_store_creates_new_company()
    {
        $companyData = [
            'name' => 'Test Insurance Co.',
            'contact_person' => 'John Doe',
            'email' => 'john@testinsurance.com',
            'phone' => '1234567890',
            'address' => '123 Insurance Street',
            'license_number' => 'LIC123456',
            'status' => 1
        ];
        
        $response = $this->post(route('insurance-companies.store'), $companyData);
        
        $response->assertRedirect(route('insurance-companies.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('insurance_companies', [
            'name' => 'Test Insurance Co.',
            'email' => 'john@testinsurance.com',
            'license_number' => 'LIC123456'
        ]);
    }

    public function test_insurance_company_store_validates_required_fields()
    {
        $response = $this->post(route('insurance-companies.store'), []);
        
        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_insurance_company_store_validates_unique_email()
    {
        $existingCompany = InsuranceCompany::factory()->create();
        
        $companyData = [
            'name' => 'New Insurance Co.',
            'email' => $existingCompany->email, // Duplicate email
            'phone' => '1234567890'
        ];
        
        $response = $this->post(route('insurance-companies.store'), $companyData);
        
        $response->assertSessionHasErrors(['email']);
    }

    public function test_insurance_company_show_displays_company_details()
    {
        $company = InsuranceCompany::factory()->create();
        
        $response = $this->get(route('insurance-companies.show', $company));
        
        $response->assertStatus(200);
        $response->assertViewIs('insurance-companies.show');
        $response->assertViewHas('insuranceCompany', $company);
    }

    public function test_insurance_company_edit_page_loads_successfully()
    {
        $company = InsuranceCompany::factory()->create();
        
        $response = $this->get(route('insurance-companies.edit', $company));
        
        $response->assertStatus(200);
        $response->assertViewIs('insurance-companies.edit');
        $response->assertViewHas('insuranceCompany', $company);
    }

    public function test_insurance_company_update_modifies_existing_company()
    {
        $company = InsuranceCompany::factory()->create();
        
        $updateData = [
            'name' => 'Updated Insurance Co.',
            'contact_person' => 'Jane Smith',
            'email' => $company->email, // Keep same email
            'phone' => '9876543210',
            'address' => 'Updated Address',
            'license_number' => 'LIC999999',
            'status' => 1
        ];
        
        $response = $this->put(route('insurance-companies.update', $company), $updateData);
        
        $response->assertRedirect(route('insurance-companies.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('insurance_companies', [
            'id' => $company->id,
            'name' => 'Updated Insurance Co.',
            'license_number' => 'LIC999999'
        ]);
    }

    public function test_insurance_company_destroy_soft_deletes_company()
    {
        $company = InsuranceCompany::factory()->create();
        
        $response = $this->delete(route('insurance-companies.destroy', $company));
        
        $response->assertRedirect(route('insurance-companies.index'));
        $response->assertSessionHas('success');
        
        $this->assertSoftDeleted('insurance_companies', ['id' => $company->id]);
    }

    public function test_insurance_company_status_update_changes_status()
    {
        $company = InsuranceCompany::factory()->create(['status' => 1]);
        
        $response = $this->patch(route('insurance-companies.updateStatus', [$company, 0]));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('insurance_companies', [
            'id' => $company->id,
            'status' => 0
        ]);
    }

    public function test_insurance_company_export_generates_excel_file()
    {
        InsuranceCompany::factory()->count(5)->create();
        
        $response = $this->get(route('insurance-companies.export'));
        
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_insurance_company_search_filters_results()
    {
        InsuranceCompany::factory()->create(['name' => 'ABC Insurance']);
        InsuranceCompany::factory()->create(['name' => 'XYZ Insurance']);
        
        $response = $this->get(route('insurance-companies.index', ['search' => 'ABC']));
        
        $response->assertStatus(200);
        $response->assertSee('ABC Insurance');
        $response->assertDontSee('XYZ Insurance');
    }

    public function test_insurance_company_status_filter_works()
    {
        InsuranceCompany::factory()->create(['name' => 'Active Company', 'status' => 1]);
        InsuranceCompany::factory()->create(['name' => 'Inactive Company', 'status' => 0]);
        
        $response = $this->get(route('insurance-companies.index', ['status' => 1]));
        
        $response->assertStatus(200);
        $response->assertSee('Active Company');
        $response->assertDontSee('Inactive Company');
    }

    public function test_unauthorized_user_cannot_access_insurance_company_pages()
    {
        auth()->logout();
        
        $response = $this->get(route('insurance-companies.index'));
        
        $response->assertRedirect(route('login'));
    }
}