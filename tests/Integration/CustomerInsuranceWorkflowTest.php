<?php

namespace Tests\Integration;

use App\Models\Customer;
use App\Models\CustomerInsurance;
use App\Models\InsuranceCompany;
use App\Models\PremiumType;
use App\Models\PolicyType;
use App\Models\FuelType;
use App\Models\Branch;
use App\Models\Broker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Carbon\Carbon;

class CustomerInsuranceWorkflowTest extends TestCase
{
    use RefreshDatabase;
    
    private User $admin;
    private Customer $customer;
    private InsuranceCompany $insuranceCompany;
    private PremiumType $premiumType;
    private PolicyType $policyType;
    private FuelType $fuelType;
    private Branch $branch;
    private Broker $broker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user and authenticate
        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
        
        // Create required entities
        $this->customer = Customer::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'mobile_number' => '9876543210'
        ]);
        
        $this->insuranceCompany = InsuranceCompany::factory()->create();
        $this->premiumType = PremiumType::factory()->create();
        $this->policyType = PolicyType::factory()->create();
        $this->fuelType = FuelType::factory()->create();
        $this->branch = Branch::factory()->create();
        $this->broker = Broker::factory()->create();
        
        // Setup storage for file uploads
        Storage::fake('public');
    }

    public function test_complete_customer_insurance_creation_workflow()
    {
        // Step 1: Access the customer insurance creation page
        $response = $this->get(route('customer_insurances.create'));
        $response->assertStatus(200);
        $response->assertViewIs('customer_insurances.add');
        
        // Step 2: Submit customer insurance creation form
        $policyDocument = UploadedFile::fake()->create('policy.pdf', 100, 'application/pdf');
        
        $insuranceData = [
            'customer_id' => $this->customer->id,
            'insurance_company_id' => $this->insuranceCompany->id,
            'premium_type_id' => $this->premiumType->id,
            'policy_type_id' => $this->policyType->id,
            'fuel_type_id' => $this->fuelType->id,
            'branch_id' => $this->branch->id,
            'broker_id' => $this->broker->id,
            'policy_no' => 'POL-2024-TEST-001',
            'registration_no' => 'MH01AB1234',
            'make_model' => 'Honda City',
            'start_date' => '15/01/2024',
            'expired_date' => '15/01/2025',
            'premium_amount' => '25000',
            'gst' => '4500',
            'final_premium_with_gst' => '29500',
            'my_commission_percentage' => '10',
            'my_commission_amount' => '2500',
            'status' => 1,
            'policy_document' => $policyDocument
        ];
        
        $response = $this->post(route('customer_insurances.store'), $insuranceData);
        
        // Step 3: Verify successful creation
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Customer Insurance Created Successfully.');
        
        // Step 4: Verify database record
        $this->assertDatabaseHas('customer_insurances', [
            'customer_id' => $this->customer->id,
            'policy_no' => 'POL-2024-TEST-001',
            'registration_no' => 'MH01AB1234',
            'premium_amount' => 25000,
            'final_premium_with_gst' => 29500,
            'status' => 1
        ]);
        
        // Step 5: Verify file upload
        $createdInsurance = CustomerInsurance::where('policy_no', 'POL-2024-TEST-001')->first();
        $this->assertNotNull($createdInsurance->policy_document_path);
        Storage::disk('public')->assertExists($createdInsurance->policy_document_path);
        
        // Step 6: Verify audit trail
        $this->assertDatabaseHas('customer_insurances', [
            'id' => $createdInsurance->id,
            'created_by' => $this->admin->id
        ]);
    }

    public function test_customer_insurance_update_workflow()
    {
        // Step 1: Create existing insurance
        $insurance = CustomerInsurance::factory()->create([
            'customer_id' => $this->customer->id,
            'policy_no' => 'OLD-POLICY-001',
            'premium_amount' => 20000,
            'created_by' => $this->admin->id
        ]);
        
        // Step 2: Access edit page
        $response = $this->get(route('customer_insurances.edit', $insurance));
        $response->assertStatus(200);
        $response->assertViewIs('customer_insurances.edit');
        $response->assertViewHas('customer_insurance', $insurance);
        
        // Step 3: Submit update
        $updateData = [
            'customer_id' => $this->customer->id,
            'insurance_company_id' => $this->insuranceCompany->id,
            'premium_type_id' => $this->premiumType->id,
            'policy_type_id' => $this->policyType->id,
            'fuel_type_id' => $this->fuelType->id,
            'branch_id' => $this->branch->id,
            'broker_id' => $this->broker->id,
            'policy_no' => 'UPDATED-POLICY-001',
            'registration_no' => 'MH01CD5678',
            'premium_amount' => '30000',
            'final_premium_with_gst' => '35400',
            'status' => 1
        ];
        
        $response = $this->put(route('customer_insurances.update', $insurance), $updateData);
        
        // Step 4: Verify successful update
        $response->assertRedirect();
        $response->assertSessionHas('success', 'CustomerInsurance Updated Successfully.');
        
        // Step 5: Verify database changes
        $this->assertDatabaseHas('customer_insurances', [
            'id' => $insurance->id,
            'policy_no' => 'UPDATED-POLICY-001',
            'registration_no' => 'MH01CD5678',
            'premium_amount' => 30000,
            'final_premium_with_gst' => 35400,
            'updated_by' => $this->admin->id
        ]);
        
        // Step 6: Verify old data is gone
        $this->assertDatabaseMissing('customer_insurances', [
            'id' => $insurance->id,
            'policy_no' => 'OLD-POLICY-001',
            'premium_amount' => 20000
        ]);
    }

    public function test_customer_insurance_renewal_workflow()
    {
        // Step 1: Create expiring insurance
        $expiringInsurance = CustomerInsurance::factory()->create([
            'customer_id' => $this->customer->id,
            'policy_no' => 'EXPIRING-001',
            'start_date' => Carbon::now()->subYear()->format('Y-m-d'),
            'expired_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
            'premium_amount' => 25000,
            'status' => 1
        ]);
        
        // Step 2: Access renewal page
        $response = $this->get(route('customer_insurances.renew', $expiringInsurance));
        $response->assertStatus(200);
        $response->assertViewIs('customer_insurances.renew');
        
        // Step 3: Submit renewal
        $renewalData = [
            'customer_id' => $this->customer->id,
            'insurance_company_id' => $this->insuranceCompany->id,
            'premium_type_id' => $this->premiumType->id,
            'policy_type_id' => $this->policyType->id,
            'fuel_type_id' => $this->fuelType->id,
            'branch_id' => $this->branch->id,
            'broker_id' => $this->broker->id,
            'policy_no' => 'RENEWED-001',
            'registration_no' => $expiringInsurance->registration_no,
            'start_date' => Carbon::now()->format('d/m/Y'),
            'expired_date' => Carbon::now()->addYear()->format('d/m/Y'),
            'premium_amount' => '28000',
            'final_premium_with_gst' => '33040',
            'status' => 1
        ];
        
        $response = $this->post(route('customer_insurances.store_renew', $expiringInsurance), $renewalData);
        
        // Step 4: Verify successful renewal
        $response->assertRedirect(route('customer_insurances.index'));
        $response->assertSessionHas('success', 'Customer Insurance Renewed Successfully.');
        
        // Step 5: Verify new policy created
        $this->assertDatabaseHas('customer_insurances', [
            'customer_id' => $this->customer->id,
            'policy_no' => 'RENEWED-001',
            'premium_amount' => 28000,
            'final_premium_with_gst' => 33040,
            'status' => 1
        ]);
        
        // Step 6: Verify original policy still exists but not modified
        $this->assertDatabaseHas('customer_insurances', [
            'id' => $expiringInsurance->id,
            'policy_no' => 'EXPIRING-001',
            'premium_amount' => 25000
        ]);
    }

    public function test_customer_insurance_status_update_workflow()
    {
        // Step 1: Create active insurance
        $insurance = CustomerInsurance::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 1
        ]);
        
        // Step 2: Update status to inactive
        $response = $this->get(route('customer_insurances.update_status', [
            'customer_insurance_id' => $insurance->id,
            'status' => 0
        ]));
        
        // Step 3: Verify successful status update
        $response->assertRedirect();
        $response->assertSessionHas('success', 'CustomerInsurance Status Updated Successfully!');
        
        // Step 4: Verify database update
        $this->assertDatabaseHas('customer_insurances', [
            'id' => $insurance->id,
            'status' => 0,
            'updated_by' => $this->admin->id
        ]);
    }

    public function test_customer_insurance_deletion_workflow()
    {
        // Step 1: Create insurance to delete
        $insurance = CustomerInsurance::factory()->create([
            'customer_id' => $this->customer->id,
            'policy_no' => 'TO-DELETE-001'
        ]);
        
        // Step 2: Delete insurance
        $response = $this->delete(route('customer_insurances.delete', $insurance));
        
        // Step 3: Verify successful deletion
        $response->assertRedirect();
        $response->assertSessionHas('success', 'CustomerInsurance Deleted Successfully!.');
        
        // Step 4: Verify soft deletion
        $this->assertSoftDeleted('customer_insurances', [
            'id' => $insurance->id,
            'deleted_by' => $this->admin->id
        ]);
    }

    public function test_customer_insurance_export_workflow()
    {
        // Step 1: Create sample data
        CustomerInsurance::factory()->count(5)->create([
            'customer_id' => $this->customer->id
        ]);
        
        // Step 2: Request export
        $response = $this->get(route('customer_insurances.export'));
        
        // Step 3: Verify export download
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }

    public function test_customer_insurance_search_and_pagination_workflow()
    {
        // Step 1: Create test data
        CustomerInsurance::factory()->create([
            'customer_id' => $this->customer->id,
            'policy_no' => 'SEARCH-POLICY-001',
            'registration_no' => 'MH01SEARCH1'
        ]);
        
        CustomerInsurance::factory()->count(15)->create([
            'customer_id' => $this->customer->id
        ]);
        
        // Step 2: Access index page
        $response = $this->get(route('customer_insurances.index'));
        $response->assertStatus(200);
        $response->assertViewIs('customer_insurances.index');
        
        // Step 3: Test search functionality
        $searchResponse = $this->get(route('customer_insurances.index', ['search' => 'SEARCH-POLICY']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('SEARCH-POLICY-001');
        
        // Step 4: Test pagination
        $page2Response = $this->get(route('customer_insurances.index', ['page' => 2]));
        $page2Response->assertStatus(200);
    }

    public function test_customer_insurance_validation_errors()
    {
        // Test missing required fields
        $response = $this->post(route('customer_insurances.store'), []);
        
        $response->assertSessionHasErrors([
            'customer_id',
            'policy_no',
            'start_date',
            'expired_date'
        ]);
    }

    public function test_customer_insurance_file_upload_validation()
    {
        // Test invalid file type
        $invalidFile = UploadedFile::fake()->create('document.txt', 100, 'text/plain');
        
        $insuranceData = [
            'customer_id' => $this->customer->id,
            'insurance_company_id' => $this->insuranceCompany->id,
            'premium_type_id' => $this->premiumType->id,
            'policy_no' => 'TEST-POLICY-001',
            'start_date' => '15/01/2024',
            'expired_date' => '15/01/2025',
            'premium_amount' => '25000',
            'policy_document' => $invalidFile
        ];
        
        $response = $this->post(route('customer_insurances.store'), $insuranceData);
        
        // Should have validation errors for file type
        $response->assertSessionHasErrors('policy_document');
    }

    public function test_unauthorized_access_protection()
    {
        // Step 1: Logout admin
        auth()->logout();
        
        // Step 2: Try to access protected routes
        $this->get(route('customer_insurances.index'))->assertRedirect(route('login'));
        $this->get(route('customer_insurances.create'))->assertRedirect(route('login'));
        $this->post(route('customer_insurances.store'), [])->assertRedirect(route('login'));
    }

    public function test_whatsapp_document_sending_workflow()
    {
        // Create insurance with document
        $insurance = CustomerInsurance::factory()->create([
            'customer_id' => $this->customer->id,
            'policy_document_path' => 'documents/test-policy.pdf'
        ]);
        
        // Mock file exists
        Storage::disk('public')->put('documents/test-policy.pdf', 'test content');
        
        // Send WhatsApp document
        $response = $this->post(route('customer_insurances.send_wa_document', $insurance));
        
        // Should redirect back with status message
        $response->assertRedirect();
        // Note: Actual WhatsApp sending would be mocked in a more comprehensive test
    }
}