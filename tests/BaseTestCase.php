<?php

namespace Tests;

use App\Models\User;
use App\Models\Customer;
use App\Models\FamilyGroup;
use App\Models\InsuranceCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Enhanced base test case with comprehensive helper methods
 */
abstract class BaseTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();

        // Disable activity logging during tests
        if (class_exists(\Spatie\Activitylog\Models\Activity::class)) {
            config(['activitylog.enabled' => false]);
        }
    }

    protected function tearDown(): void
    {
        // Re-enable activity logging
        if (class_exists(\Spatie\Activitylog\Models\Activity::class)) {
            config(['activitylog.enabled' => true]);
        }
        parent::tearDown();
    }

    // =============================================================================
    // USER & ADMIN HELPERS
    // =============================================================================

    /**
     * Create an admin user with full permissions
     */
    protected function createAdminUser(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
            'status' => 1,
        ], $attributes));

        $adminRole = Role::findByName('admin', 'web');
        $user->assignRole($adminRole);

        return $user;
    }

    /**
     * Create a regular user with limited permissions
     */
    protected function createRegularUser(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'email' => 'user@test.com',
            'role_id' => 2,
            'status' => 1,
        ], $attributes));

        $userRole = Role::findByName('user', 'web');
        $user->assignRole($userRole);

        return $user;
    }

    // =============================================================================
    // CUSTOMER HELPERS
    // =============================================================================

    /**
     * Create a customer with verified email
     */
    protected function createCustomer(array $attributes = []): Customer
    {
        return Customer::factory()->create(array_merge([
            'email' => 'customer@test.com',
            'password' => Hash::make('password'),
            'status' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    /**
     * Create a customer with unverified email
     */
    protected function createUnverifiedCustomer(array $attributes = []): Customer
    {
        return Customer::factory()->create(array_merge([
            'email' => 'unverified@test.com',
            'password' => Hash::make('password'),
            'status' => true,
            'email_verified_at' => null,
            'email_verification_token' => Str::random(60),
        ], $attributes));
    }

    /**
     * Create a complete family structure with head and members
     */
    protected function createFamilyStructure(): array
    {
        $familyGroup = \App\Models\FamilyGroup::factory()->create([
            'name' => 'Test Family',
            'status' => true,
        ]);

        // Family head
        $familyHead = Customer::factory()->create([
            'name' => 'John Doe',
            'email' => 'head@family.com',
            'family_group_id' => $familyGroup->id,
            'status' => true,
            'email_verified_at' => now(),
        ]);

        $headMember = \App\Models\FamilyMember::factory()->create([
            'customer_id' => $familyHead->id,
            'family_group_id' => $familyGroup->id,
            'is_head' => true,
            'relationship' => 'Self',
        ]);

        // Family spouse
        $spouse = Customer::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'spouse@family.com',
            'family_group_id' => $familyGroup->id,
            'status' => true,
            'email_verified_at' => now(),
        ]);

        $spouseMember = \App\Models\FamilyMember::factory()->create([
            'customer_id' => $spouse->id,
            'family_group_id' => $familyGroup->id,
            'is_head' => false,
            'relationship' => 'Spouse',
        ]);

        // Family child
        $child = Customer::factory()->create([
            'name' => 'Johnny Doe Jr.',
            'email' => 'child@family.com',
            'family_group_id' => $familyGroup->id,
            'status' => true,
            'email_verified_at' => now(),
        ]);

        $childMember = \App\Models\FamilyMember::factory()->create([
            'customer_id' => $child->id,
            'family_group_id' => $familyGroup->id,
            'is_head' => false,
            'relationship' => 'Son',
        ]);

        // Update family group with head
        $familyGroup->update(['family_head_id' => $familyHead->id]);

        return [
            'familyGroup' => $familyGroup->refresh(),
            'familyHead' => $familyHead->refresh(),
            'spouse' => $spouse->refresh(),
            'child' => $child->refresh(),
            'headMember' => $headMember,
            'spouseMember' => $spouseMember,
            'childMember' => $childMember,
        ];
    }

    // =============================================================================
    // INSURANCE & QUOTATION HELPERS
    // =============================================================================

    /**
     * Create insurance companies for testing
     */
    protected function createInsuranceCompanies(): array
    {
        return [
            \App\Models\InsuranceCompany::factory()->create(['name' => 'HDFC ERGO', 'status' => 1]),
            \App\Models\InsuranceCompany::factory()->create(['name' => 'ICICI Lombard', 'status' => 1]),
            \App\Models\InsuranceCompany::factory()->create(['name' => 'Bajaj Allianz', 'status' => 1]),
        ];
    }

    /**
     * Create a quotation with company quotes
     */
    protected function createQuotationWithCompanies(Customer $customer = null): array
    {
        $customer = $customer ?: $this->createCustomer();
        $companies = $this->createInsuranceCompanies();

        $quotation = \App\Models\Quotation::factory()->create([
            'customer_id' => $customer->id,
            'vehicle_number' => 'MH12AB1234',
            'make_model_variant' => 'Maruti Swift VDI',
            'total_idv' => 500000,
        ]);

        $companyQuotes = [];
        foreach ($companies as $index => $company) {
            $companyQuotes[] = \App\Models\QuotationCompany::factory()->create([
                'quotation_id' => $quotation->id,
                'insurance_company_id' => $company->id,
                'final_premium' => 15000 + ($index * 500),
                'ranking' => $index + 1,
                'is_recommended' => $index === 0,
            ]);
        }

        return [
            'quotation' => $quotation->refresh(),
            'companies' => $companies,
            'companyQuotes' => $companyQuotes,
            'customer' => $customer,
        ];
    }

    /**
     * Create customer insurance policy
     */
    protected function createCustomerInsurance(Customer $customer = null, InsuranceCompany $company = null): \App\Models\CustomerInsurance
    {
        $customer = $customer ?: $this->createCustomer();
        $company = $company ?: \App\Models\InsuranceCompany::factory()->create();

        return \App\Models\CustomerInsurance::factory()->create([
            'customer_id' => $customer->id,
            'insurance_company_id' => $company->id,
            'policy_no' => 'POL' . time(),
            'expired_date' => now()->addYear(),
        ]);
    }

    // =============================================================================
    // AUTHENTICATION HELPERS
    // =============================================================================

    /**
     * Act as authenticated admin user
     */
    protected function actingAsAdmin(User $user = null): User
    {
        $user = $user ?: $this->createAdminUser();
        $this->actingAs($user, 'web');
        return $user;
    }

    /**
     * Act as authenticated regular user
     */
    protected function actingAsUser(User $user = null): User
    {
        $user = $user ?: $this->createRegularUser();
        $this->actingAs($user, 'web');
        return $user;
    }

    /**
     * Act as authenticated customer
     */
    protected function actingAsCustomer(Customer $customer = null): Customer
    {
        $customer = $customer ?: $this->createCustomer();
        $this->actingAs($customer, 'customer');
        return $customer;
    }

    /**
     * Act as family head customer
     */
    protected function actingAsFamilyHead(): array
    {
        $family = $this->createFamilyStructure();
        $this->actingAs($family['familyHead'], 'customer');
        return $family;
    }

    // =============================================================================
    // FILE & UPLOAD HELPERS
    // =============================================================================

    /**
     * Create a mock PDF file for testing
     */
    protected function createMockPdfFile(string $filename = 'test.pdf'): \Illuminate\Http\UploadedFile
    {
        return \Illuminate\Http\UploadedFile::fake()->create($filename, 1000, 'application/pdf');
    }

    /**
     * Create a mock image file for testing
     */
    protected function createMockImageFile(string $filename = 'test.jpg'): \Illuminate\Http\UploadedFile
    {
        return \Illuminate\Http\UploadedFile::fake()->image($filename, 800, 600);
    }

    /**
     * Create a malicious file for security testing
     */
    protected function createMaliciousFile(string $filename = 'malware.exe'): \Illuminate\Http\UploadedFile
    {
        return \Illuminate\Http\UploadedFile::fake()->create($filename, 1000, 'application/x-msdownload');
    }

    // =============================================================================
    // ASSERTION HELPERS
    // =============================================================================

    /**
     * Assert that audit log was created
     */
    protected function assertAuditLogCreated(string $action, bool $success = true): void
    {
        $this->assertDatabaseHas('customer_audit_logs', [
            'action' => $action,
            'success' => $success,
        ]);
    }

    /**
     * Assert security violation was logged
     */
    protected function assertSecurityViolationLogged(string $securityViolation = null): void
    {
        $conditions = ['success' => false];

        if ($securityViolation) {
            // We'll need to check the metadata JSON column
            $logs = \App\Models\CustomerAuditLog::where('success', false)->get();
            $found = false;
            foreach ($logs as $log) {
                $metadata = json_decode($log->metadata, true);
                if (isset($metadata['security_violation']) && $metadata['security_violation'] === $securityViolation) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, "Security violation '{$securityViolation}' was not logged");
        } else {
            $this->assertDatabaseHas('customer_audit_logs', $conditions);
        }
    }

    /**
     * Assert response has validation errors for specific fields
     */
    protected function assertValidationErrors(array $fields, $response = null): void
    {
        $response = $response ?: $this->response;

        $response->assertSessionHasErrors($fields);

        foreach ($fields as $field) {
            $response->assertSessionHasErrorsIn('default', $field);
        }
    }

    /**
     * Assert model has specific relationships
     */
    protected function assertModelHasRelationships($model, array $relationships): void
    {
        foreach ($relationships as $relationship) {
            $this->assertTrue(
                method_exists($model, $relationship),
                "Model " . get_class($model) . " should have relationship: {$relationship}"
            );
        }
    }

    // =============================================================================
    // PERMISSION & ROLE SETUP
    // =============================================================================

    /**
     * Seed basic roles and permissions for testing
     */
    protected function seedRolesAndPermissions(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Create permissions
        $permissions = [
            'view-users', 'create-users', 'edit-users', 'delete-users',
            'view-customers', 'create-customers', 'edit-customers', 'delete-customers',
            'view-quotations', 'create-quotations', 'edit-quotations', 'delete-quotations',
            'view-policies', 'create-policies', 'edit-policies', 'delete-policies',
            'view-claims', 'create-claims', 'edit-claims', 'delete-claims',
            'view-reports', 'create-reports', 'edit-reports', 'delete-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign all permissions to admin
        $adminRole->syncPermissions(Permission::where('guard_name', 'web')->get());

        // Assign limited permissions to user
        $userRole->syncPermissions([
            'view-customers', 'create-customers', 'edit-customers',
            'view-quotations', 'create-quotations', 'edit-quotations',
            'view-policies', 'create-policies', 'edit-policies',
        ]);
    }

    // =============================================================================
    // UTILITY METHODS
    // =============================================================================

    /**
     * Generate fake data for testing
     */
    protected function fakeData(): \Faker\Generator
    {
        return \Faker\Factory::create();
    }

    /**
     * Convert array to JSON for API testing
     */
    protected function toJson(array $data): string
    {
        return json_encode($data);
    }

    /**
     * Get API headers for testing
     */
    protected function getApiHeaders(array $additional = []): array
    {
        return array_merge([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ], $additional);
    }
}