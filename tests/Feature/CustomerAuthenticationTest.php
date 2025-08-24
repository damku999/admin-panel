<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\FamilyGroup;
use App\Models\FamilyMember;
use App\Models\CustomerAuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test family setup
        $this->familyGroup = FamilyGroup::factory()->create([
            'name' => 'Test Family',
            'status' => true
        ]);
        
        $this->activeCustomer = Customer::factory()->create([
            'email' => 'active@example.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'family_group_id' => $this->familyGroup->id,
            'must_change_password' => false
        ]);
        
        FamilyMember::create([
            'family_group_id' => $this->familyGroup->id,
            'customer_id' => $this->activeCustomer->id,
            'relationship' => 'head',
            'is_head' => true,
            'status' => true
        ]);
        
        $this->familyGroup->update(['family_head_id' => $this->activeCustomer->id]);
    }

    public function test_customer_can_view_login_page(): void
    {
        $response = $this->get(route('customer.login'));
        
        $response->assertStatus(200);
        $response->assertViewIs('customer.auth.login');
        $response->assertSee('Customer Login');
        $response->assertSee('Access your family insurance policies');
    }

    public function test_customer_can_login_with_valid_credentials(): void
    {
        $response = $this->post(route('customer.login'), [
            'email' => 'active@example.com',
            'password' => 'password123',
            'remember' => false
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticatedAs($this->activeCustomer, 'customer');
        
        // Check audit log was created
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $this->activeCustomer->id,
            'action' => 'login',
            'success' => true
        ]);
    }

    public function test_customer_cannot_login_with_invalid_password(): void
    {
        $response = $this->post(route('customer.login'), [
            'email' => 'active@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest('customer');
        
        // Check failed login was logged
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $this->activeCustomer->id,
            'action' => 'login_failed',
            'success' => false
        ]);
    }

    public function test_customer_cannot_login_with_invalid_email(): void
    {
        $response = $this->post(route('customer.login'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest('customer');
    }

    public function test_inactive_customer_cannot_login(): void
    {
        $inactiveCustomer = Customer::factory()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'status' => false,
            'family_group_id' => $this->familyGroup->id
        ]);

        $response = $this->post(route('customer.login'), [
            'email' => 'inactive@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest('customer');
    }

    public function test_customer_with_inactive_family_group_cannot_login(): void
    {
        $this->familyGroup->update(['status' => false]);

        $response = $this->post(route('customer.login'), [
            'email' => 'active@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest('customer');
    }

    public function test_customer_requiring_password_change_is_redirected(): void
    {
        $this->activeCustomer->update(['must_change_password' => true]);

        $response = $this->post(route('customer.login'), [
            'email' => 'active@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('customer.change-password'));
        $response->assertSessionHas('warning');
        $this->assertAuthenticatedAs($this->activeCustomer, 'customer');
    }

    public function test_customer_without_verified_email_is_redirected(): void
    {
        $this->activeCustomer->update([
            'email_verified_at' => null,
            'email_verification_token' => 'test-token'
        ]);

        $response = $this->post(route('customer.login'), [
            'email' => 'active@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('customer.verify-email-notice'));
        $response->assertSessionHas('info');
        $this->assertAuthenticatedAs($this->activeCustomer, 'customer');
    }

    public function test_customer_can_logout(): void
    {
        $this->actingAs($this->activeCustomer, 'customer');

        $response = $this->post(route('customer.logout'));

        $response->assertRedirect(route('customer.login'));
        $response->assertSessionHas('message', 'You have been logged out successfully.');
        $this->assertGuest('customer');
        
        // Check logout was logged
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $this->activeCustomer->id,
            'action' => 'logout',
            'success' => true
        ]);
    }

    public function test_authenticated_customer_cannot_access_login_page(): void
    {
        $this->actingAs($this->activeCustomer, 'customer');

        $response = $this->get(route('customer.login'));
        
        $response->assertRedirect(route('customer.dashboard'));
    }

    public function test_guest_cannot_access_protected_routes(): void
    {
        $protectedRoutes = [
            'customer.dashboard',
            'customer.policies',
            'customer.profile',
            'customer.change-password'
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get(route($route));
            $response->assertRedirect(route('customer.login'));
        }
    }

    public function test_customer_can_access_dashboard_after_login(): void
    {
        $this->actingAs($this->activeCustomer, 'customer');

        $response = $this->get(route('customer.dashboard'));
        
        $response->assertStatus(200);
        $response->assertViewIs('customer.dashboard');
        $response->assertSee('Family Insurance Dashboard');
        $response->assertSee($this->activeCustomer->name);
    }

    public function test_login_form_validation(): void
    {
        // Test missing email
        $response = $this->post(route('customer.login'), [
            'password' => 'password123'
        ]);
        $response->assertSessionHasErrors(['email']);

        // Test invalid email format
        $response = $this->post(route('customer.login'), [
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);
        $response->assertSessionHasErrors(['email']);

        // Test missing password
        $response = $this->post(route('customer.login'), [
            'email' => 'test@example.com'
        ]);
        $response->assertSessionHasErrors(['password']);

        // Test short password
        $response = $this->post(route('customer.login'), [
            'email' => 'test@example.com',
            'password' => '123'
        ]);
        $response->assertSessionHasErrors(['password']);
    }

    public function test_login_rate_limiting(): void
    {
        // Attempt multiple failed logins
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post(route('customer.login'), [
                'email' => 'active@example.com',
                'password' => 'wrongpassword'
            ]);
        }

        // Next attempt should be rate limited
        $response = $this->post(route('customer.login'), [
            'email' => 'active@example.com',
            'password' => 'password123' // Even correct password should be blocked
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    public function test_remember_me_functionality(): void
    {
        $response = $this->post(route('customer.login'), [
            'email' => 'active@example.com',
            'password' => 'password123',
            'remember' => true
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticatedAs($this->activeCustomer, 'customer');
        
        // Check that remember token was set
        $this->activeCustomer->refresh();
        $this->assertNotNull($this->activeCustomer->remember_token);
    }

    public function test_session_regeneration_on_login(): void
    {
        $oldSessionId = session()->getId();

        $response = $this->post(route('customer.login'), [
            'email' => 'active@example.com',
            'password' => 'password123'
        ]);

        $newSessionId = session()->getId();
        $this->assertNotEquals($oldSessionId, $newSessionId);
    }

    public function test_login_clears_previous_login_attempts(): void
    {
        // Make some failed attempts
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('customer.login'), [
                'email' => 'active@example.com',
                'password' => 'wrongpassword'
            ]);
        }

        // Successful login should clear attempts
        $response = $this->post(route('customer.login'), [
            'email' => 'active@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        
        // Should be able to make failed attempts again (counter was reset)
        $this->post('/customer/logout');
        
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post(route('customer.login'), [
                'email' => 'active@example.com',
                'password' => 'wrongpassword'
            ]);
            $response->assertRedirect(); // Should not be rate limited yet
        }
    }

    public function test_audit_log_records_login_metadata(): void
    {
        $response = $this->post(route('customer.login'), [
            'email' => 'active@example.com',
            'password' => 'password123',
            'remember' => true
        ]);

        $auditLog = CustomerAuditLog::where([
            'customer_id' => $this->activeCustomer->id,
            'action' => 'login'
        ])->first();

        $this->assertNotNull($auditLog);
        $this->assertTrue($auditLog->success);
        $this->assertEquals('Customer logged in successfully', $auditLog->description);
        $this->assertNotNull($auditLog->ip_address);
        $this->assertNotNull($auditLog->session_id);
        
        $metadata = $auditLog->metadata;
        $this->assertEquals('email_password', $metadata['login_method']);
        $this->assertTrue($metadata['remember_me']);
    }

    public function test_customer_without_family_group_can_still_login(): void
    {
        $independentCustomer = Customer::factory()->create([
            'email' => 'independent@example.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'family_group_id' => null
        ]);

        $response = $this->post(route('customer.login'), [
            'email' => 'independent@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticatedAs($independentCustomer, 'customer');
    }
}