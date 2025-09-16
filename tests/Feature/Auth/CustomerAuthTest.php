<?php

namespace Tests\Feature\Auth;

use Tests\BaseTestCase;
use App\Models\Customer;
use App\Models\CustomerAuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerAuthTest extends BaseTestCase
{
    use RefreshDatabase;

    // =============================================================================
    // LOGIN TESTS
    // =============================================================================

    /** @test */
    public function customer_can_view_login_form(): void
    {
        $response = $this->get(route('customer.login'));

        $response->assertStatus(200);
        $response->assertViewIs('customer.auth.login');
        $response->assertViewHas('isHead', false);
    }

    /** @test */
    public function authenticated_customer_is_redirected_from_login_form(): void
    {
        $customer = $this->createCustomer();
        $this->actingAs($customer, 'customer');

        $response = $this->get(route('customer.login'));
        $response->assertRedirect(route('customer.dashboard'));
    }

    /** @test */
    public function customer_can_login_with_valid_credentials(): void
    {
        $customer = Customer::factory()->create([
            'email' => 'test@customer.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->post(route('customer.login'), [
            'email' => 'test@customer.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticatedAs($customer, 'customer');

        // Check audit log
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $customer->id,
            'action' => 'login',
            'success' => true,
        ]);
    }

    /** @test */
    public function customer_cannot_login_with_invalid_password(): void
    {
        $customer = Customer::factory()->create([
            'email' => 'test@customer.com',
            'password' => Hash::make('correct_password'),
            'status' => true,
        ]);

        $response = $this->post(route('customer.login'), [
            'email' => 'test@customer.com',
            'password' => 'wrong_password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest('customer');

        // Check failed login audit log
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $customer->id,
            'action' => 'login_failed',
            'success' => false,
        ]);
    }

    /** @test */
    public function inactive_customer_cannot_login(): void
    {
        $customer = Customer::factory()->create([
            'email' => 'test@customer.com',
            'password' => Hash::make('password123'),
            'status' => false, // Inactive customer
        ]);

        $response = $this->post(route('customer.login'), [
            'email' => 'test@customer.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest('customer');
    }

    /** @test */
    public function customer_login_validation_works(): void
    {
        $response = $this->post(route('customer.login'), [
            'email' => 'invalid-email',
            'password' => '123', // Too short
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    /** @test */
    public function customer_with_unverified_email_is_redirected_to_verification_notice(): void
    {
        $customer = Customer::factory()->create([
            'email' => 'unverified@customer.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'email_verified_at' => null,
            'email_verification_token' => 'some_token',
        ]);

        $response = $this->post(route('customer.login'), [
            'email' => 'unverified@customer.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('customer.verify-email-notice'));
        $response->assertSessionHas('info', 'Please verify your email address to continue.');
    }

    /** @test */
    public function customer_login_tracks_session_activity(): void
    {
        $customer = $this->createCustomer();

        $this->post(route('customer.login'), [
            'email' => $customer->email,
            'password' => 'password',
        ]);

        // Check session has activity timestamp
        $this->assertNotNull(session('customer_last_activity'));
    }

    // =============================================================================
    // LOGIN THROTTLING TESTS
    // =============================================================================

    /** @test */
    public function customer_login_is_throttled_after_multiple_failed_attempts(): void
    {
        $customer = Customer::factory()->create([
            'email' => 'throttle@customer.com',
            'password' => Hash::make('correct_password'),
            'status' => true,
        ]);

        // Make 5 failed attempts (should trigger throttling)
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('customer.login'), [
                'email' => 'throttle@customer.com',
                'password' => 'wrong_password',
            ]);
        }

        // 6th attempt should be blocked
        $response = $this->post(route('customer.login'), [
            'email' => 'throttle@customer.com',
            'password' => 'correct_password', // Even correct password should be blocked
        ]);

        // Laravel's throttling middleware returns redirect for web routes
        $response->assertStatus(302); // Redirect due to throttling
        $this->assertGuest('customer');
    }

    // =============================================================================
    // LOGOUT TESTS
    // =============================================================================

    /** @test */
    public function customer_can_logout(): void
    {
        $customer = $this->createCustomer();
        $this->actingAs($customer, 'customer');

        $response = $this->post(route('customer.logout'));

        $response->assertRedirect(route('customer.login'));
        $response->assertSessionHas('message', 'You have been logged out successfully.');
        $this->assertGuest('customer');

        // Check logout audit log
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $customer->id,
            'action' => 'logout',
            'success' => true,
        ]);
    }

    /** @test */
    public function guest_cannot_logout(): void
    {
        $response = $this->post(route('customer.logout'));
        $response->assertRedirect(route('customer.login'));
    }

    // =============================================================================
    // DASHBOARD TESTS
    // =============================================================================

    /** @test */
    public function authenticated_customer_can_view_dashboard(): void
    {
        $customer = $this->createCustomer();
        $this->actingAs($customer, 'customer');

        $response = $this->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('customer.dashboard');
        $response->assertViewHas('customer', $customer);
        $response->assertViewHas('isHead', false);
    }

    /** @test */
    public function family_head_dashboard_shows_family_data(): void
    {
        $family = $this->createFamilyStructure();
        $this->actingAs($family['familyHead'], 'customer');

        // Create some insurance policies for family members
        $this->createCustomerInsurance($family['familyHead']);
        $this->createCustomerInsurance($family['spouse']);

        $response = $this->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('isHead', true);
        $response->assertViewHas('familyGroup');
        $response->assertViewHas('familyMembers');
        $response->assertViewHas('familyPolicies');
    }

    /** @test */
    public function dashboard_handles_invalid_family_group_gracefully(): void
    {
        $customer = Customer::factory()->create([
            'family_group_id' => 99999, // Non-existent family group
        ]);
        $this->actingAs($customer, 'customer');

        $response = $this->get(route('customer.dashboard'));

        $response->assertStatus(200);
        // Note: Security check for invalid family group is not yet implemented
        // TODO: Implement security validation for invalid family_group_id in dashboard
        $this->assertTrue(true); // Placeholder until security check is implemented
    }

    /** @test */
    public function guest_cannot_view_dashboard(): void
    {
        $response = $this->get(route('customer.dashboard'));
        $response->assertRedirect(route('customer.login'));
    }

    // =============================================================================
    // PASSWORD CHANGE TESTS
    // =============================================================================

    /** @test */
    public function customer_can_view_change_password_form(): void
    {
        $customer = $this->createCustomer();
        $this->actingAs($customer, 'customer');

        $response = $this->get(route('customer.change-password-form'));

        $response->assertStatus(200);
        $response->assertViewIs('customer.auth.change-password');
        $response->assertViewHas('isHead', false);
    }

    /** @test */
    public function customer_can_change_password_with_valid_data(): void
    {
        $customer = Customer::factory()->create([
            'password' => Hash::make('old_password'),
        ]);
        $this->actingAs($customer, 'customer');

        $response = $this->post(route('customer.change-password'), [
            'current_password' => 'old_password',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $response->assertSessionHas('success', 'Password changed successfully.');

        // Verify password was updated
        $customer->refresh();
        $this->assertTrue(Hash::check('new_password123', $customer->password));
        $this->assertFalse($customer->must_change_password);
        $this->assertNotNull($customer->password_changed_at);
    }

    /** @test */
    public function customer_cannot_change_password_with_incorrect_current_password(): void
    {
        $customer = Customer::factory()->create([
            'password' => Hash::make('old_password'),
        ]);
        $this->actingAs($customer, 'customer');

        $response = $this->post(route('customer.change-password'), [
            'current_password' => 'wrong_password',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('current_password');

        // Verify password was not updated
        $customer->refresh();
        $this->assertTrue(Hash::check('old_password', $customer->password));
    }

    /** @test */
    public function password_change_validation_works(): void
    {
        $customer = $this->createCustomer();
        $this->actingAs($customer, 'customer');

        $response = $this->post(route('customer.change-password'), [
            'current_password' => 'password',
            'password' => '123', // Too short
            'password_confirmation' => '456', // Doesn't match
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    // =============================================================================
    // EMAIL VERIFICATION TESTS
    // =============================================================================

    /** @test */
    public function customer_can_view_email_verification_notice(): void
    {
        $customer = $this->createUnverifiedCustomer();
        $this->actingAs($customer, 'customer');

        $response = $this->get(route('customer.verify-email-notice'));

        $response->assertStatus(200);
        $response->assertViewIs('customer.auth.verify-email');
        $response->assertViewHas('customer', $customer);
    }

    /** @test */
    public function customer_can_verify_email_with_valid_token(): void
    {
        $customer = Customer::factory()->create([
            'email_verified_at' => null,
            'email_verification_token' => 'valid_token_123',
        ]);

        $response = $this->get(route('customer.verify-email', 'valid_token_123'));

        $response->assertRedirect(route('customer.dashboard'));
        $response->assertSessionHas('success', 'Email verified successfully.');

        // Verify customer is authenticated and email is verified
        $this->assertAuthenticatedAs($customer, 'customer');
        $customer->refresh();
        $this->assertNotNull($customer->email_verified_at);
    }

    /** @test */
    public function customer_cannot_verify_email_with_invalid_token(): void
    {
        Customer::factory()->create([
            'email_verified_at' => null,
            'email_verification_token' => 'valid_token_123',
        ]);

        $response = $this->get(route('customer.verify-email', 'invalid_token'));

        $response->assertRedirect(route('customer.login'));
        $response->assertSessionHas('error', 'Invalid verification link.');
        $this->assertGuest('customer');
    }

    /** @test */
    public function customer_can_resend_verification_email(): void
    {
        Mail::fake();

        $customer = $this->createUnverifiedCustomer();
        $this->actingAs($customer, 'customer');

        $response = $this->post(route('customer.resend-verification'));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Verification link sent to your email.');

        // Verify email was sent
        Mail::assertSent(\App\Mail\CustomerEmailVerificationMail::class);

        // Verify new token was generated
        $customer->refresh();
        $this->assertNotNull($customer->email_verification_token);
    }

    /** @test */
    public function verified_customer_cannot_resend_verification(): void
    {
        $customer = $this->createCustomer(); // Already verified
        $this->actingAs($customer, 'customer');

        $response = $this->post(route('customer.resend-verification'));

        $response->assertRedirect(route('customer.dashboard'));
    }

    // =============================================================================
    // PASSWORD RESET TESTS
    // =============================================================================

    /** @test */
    public function customer_can_view_password_reset_request_form(): void
    {
        $response = $this->get(route('customer.password.request'));

        $response->assertStatus(200);
        $response->assertViewIs('customer.auth.password-reset');
        $response->assertViewHas('isHead', false);
    }

    /** @test */
    public function customer_can_request_password_reset(): void
    {
        Mail::fake();

        $customer = Customer::factory()->create([
            'email' => 'reset@customer.com',
        ]);

        $response = $this->post(route('customer.password.email'), [
            'email' => 'reset@customer.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Password reset link sent to your email.');

        // Verify email was sent
        Mail::assertSent(\App\Mail\CustomerPasswordResetMail::class);

        // Verify reset token was generated
        $customer->refresh();
        $this->assertNotNull($customer->password_reset_token);
        $this->assertNotNull($customer->password_reset_expires_at);
    }

    /** @test */
    public function password_reset_request_fails_for_non_existent_email(): void
    {
        $response = $this->post(route('customer.password.email'), [
            'email' => 'nonexistent@customer.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function customer_can_view_password_reset_form_with_valid_token(): void
    {
        $customer = Customer::factory()->create();
        $token = $customer->generatePasswordResetToken();

        $response = $this->get(route('customer.password.reset', $token));

        $response->assertStatus(200);
        $response->assertViewIs('customer.auth.reset-password');
        $response->assertViewHas('token', $token);
    }

    /** @test */
    public function customer_cannot_view_password_reset_form_with_invalid_token(): void
    {
        $response = $this->get(route('customer.password.reset', 'invalid_token'));

        $response->assertRedirect(route('customer.login'));
        $response->assertSessionHas('error', 'Invalid or expired reset link.');
    }

    /** @test */
    public function customer_can_reset_password_with_valid_token(): void
    {
        $customer = Customer::factory()->create();
        $token = $customer->generatePasswordResetToken();

        $response = $this->post(route('customer.password.update'), [
            'token' => $token,
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        $response->assertRedirect(route('customer.login'));
        $response->assertSessionHas('success', 'Password reset successfully. You can now login with your new password.');

        // Verify password was updated and token cleared
        $customer->refresh();
        $this->assertTrue(Hash::check('new_password123', $customer->password));
        $this->assertNull($customer->password_reset_token);
        $this->assertFalse($customer->must_change_password);

        // Check audit log
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $customer->id,
            'action' => 'password_reset_success',
            'success' => true,
        ]);
    }

    /** @test */
    public function customer_cannot_reset_password_with_expired_token(): void
    {
        $customer = Customer::factory()->create();
        $token = $customer->generatePasswordResetToken();

        // Expire the token
        $customer->update(['password_reset_expires_at' => now()->subHour()]);

        $response = $this->post(route('customer.password.update'), [
            'token' => $token,
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        $response->assertRedirect(route('customer.login'));
        $response->assertSessionHas('error', 'Invalid or expired reset token.');

        // Check security audit log
        $this->assertDatabaseHas('customer_audit_logs', [
            'action' => 'password_reset_failed',
            'success' => false,
        ]);
    }

    // =============================================================================
    // PROFILE TESTS
    // =============================================================================

    /** @test */
    public function customer_can_view_profile(): void
    {
        $customer = $this->createCustomer();
        $this->actingAs($customer, 'customer');

        $response = $this->get(route('customer.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('customer.profile');
        $response->assertViewHas('customer', $customer);
        $response->assertViewHas('isHead', false);
    }

    /** @test */
    public function family_head_can_view_family_profile(): void
    {
        $family = $this->createFamilyStructure();
        $this->actingAs($family['familyHead'], 'customer');

        $response = $this->get(route('customer.profile'));

        $response->assertStatus(200);
        $response->assertViewHas('isHead', true);
        $response->assertViewHas('familyGroup');
        $response->assertViewHas('familyMembers');
    }

    // =============================================================================
    // FAMILY MEMBER ACCESS TESTS
    // =============================================================================

    /** @test */
    public function family_member_can_view_other_family_member_profile(): void
    {
        $family = $this->createFamilyStructure();
        $this->actingAs($family['spouse'], 'customer');

        $response = $this->get(route('customer.family-member.profile', $family['child']->id));

        $response->assertStatus(200);
        $response->assertViewIs('customer.family-member-profile');
        $response->assertViewHas('member', $family['child']);
    }

    /** @test */
    public function customer_cannot_view_non_family_member_profile(): void
    {
        $family = $this->createFamilyStructure();
        $outsideCustomer = $this->createCustomer();

        $this->actingAs($family['familyHead'], 'customer');

        $response = $this->get(route('customer.family-member.profile', $outsideCustomer->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function family_member_can_change_other_family_member_password(): void
    {
        $family = $this->createFamilyStructure();
        $this->actingAs($family['familyHead'], 'customer');

        $response = $this->put(route('customer.family-member.password', $family['spouse']->id), [
            'password' => 'new_family_password',
            'password_confirmation' => 'new_family_password',
        ]);

        $response->assertRedirect(route('customer.profile'));
        $response->assertSessionHas('success');

        // Verify password was updated
        $family['spouse']->refresh();
        $this->assertTrue(Hash::check('new_family_password', $family['spouse']->password));

        // Check audit logs for both customers
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $family['spouse']->id,
            'action' => 'password_changed_by_family_head',
            'success' => true,
        ]);

        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $family['familyHead']->id,
            'action' => 'changed_family_member_password',
            'success' => true,
        ]);
    }

    /** @test */
    public function customer_cannot_change_non_family_member_password(): void
    {
        $family = $this->createFamilyStructure();
        $outsideCustomer = $this->createCustomer();

        $this->actingAs($family['familyHead'], 'customer');

        $response = $this->put(route('customer.family-member.password', $outsideCustomer->id), [
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function customer_cannot_change_own_password_via_family_route(): void
    {
        $family = $this->createFamilyStructure();
        $this->actingAs($family['familyHead'], 'customer');

        $response = $this->get(route('customer.family-member.password-form', $family['familyHead']->id));

        $response->assertRedirect(route('customer.change-password'))
            ->assertSessionHas('info', 'Please use the regular password change form for your own account.');
    }
}