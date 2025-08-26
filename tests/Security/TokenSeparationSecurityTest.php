<?php

namespace Tests\Security;

use App\Models\Customer;
use App\Models\CustomerAuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TokenSeparationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_and_password_reset_use_separate_tokens(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'email_verified_at' => null
        ]);

        // Generate email verification token
        $emailToken = $customer->generateEmailVerificationToken();
        
        // Generate password reset token
        $passwordToken = $customer->generatePasswordResetToken();
        
        // Refresh the customer to get latest data
        $customer->refresh();
        
        // Tokens should be different
        $this->assertNotEquals($emailToken, $passwordToken);
        $this->assertNotEquals($customer->email_verification_token, $customer->password_reset_token);
        
        // Both tokens should exist
        $this->assertNotNull($customer->email_verification_token);
        $this->assertNotNull($customer->password_reset_token);
    }

    public function test_password_reset_token_has_expiration(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => true
        ]);

        $token = $customer->generatePasswordResetToken();
        $customer->refresh();

        // Should have expiration timestamp
        $this->assertNotNull($customer->password_reset_expires_at);
        
        // Expiration should be in the future (within 1 hour)
        $this->assertTrue($customer->password_reset_expires_at->isFuture());
        $this->assertTrue($customer->password_reset_expires_at->isAfter(now()->addMinutes(55)));
        $this->assertTrue($customer->password_reset_expires_at->isBefore(now()->addMinutes(65)));
    }

    public function test_password_reset_token_validation_with_valid_token(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => true
        ]);

        $token = $customer->generatePasswordResetToken();
        
        // Valid token should pass verification
        $this->assertTrue($customer->verifyPasswordResetToken($token));
    }

    public function test_password_reset_token_validation_with_invalid_token(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => true
        ]);

        $customer->generatePasswordResetToken();
        
        // Invalid token should fail verification
        $this->assertFalse($customer->verifyPasswordResetToken('invalid-token'));
    }

    public function test_expired_password_reset_token_is_rejected(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => true
        ]);

        $token = $customer->generatePasswordResetToken();
        
        // Manually expire the token
        $customer->update(['password_reset_expires_at' => now()->subMinutes(10)]);
        
        // Expired token should fail verification and be cleared
        $this->assertFalse($customer->verifyPasswordResetToken($token));
        
        // Token should be cleared after expiration check
        $customer->refresh();
        $this->assertNull($customer->password_reset_token);
        $this->assertNull($customer->password_reset_expires_at);
    }

    public function test_email_verification_token_cannot_be_used_for_password_reset(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'email_verified_at' => null
        ]);

        // Generate email verification token
        $emailToken = $customer->generateEmailVerificationToken();
        
        // Try to use email verification token for password reset
        $response = $this->post(route('customer.password.reset'), [
            'token' => $emailToken,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertRedirect(route('customer.login'));
        $response->assertSessionHas('error', 'Invalid or expired reset token.');
        
        // Password should not be changed
        $customer->refresh();
        $this->assertTrue(Hash::check('password123', $customer->password));
        $this->assertFalse(Hash::check('newpassword123', $customer->password));
    }

    public function test_password_reset_token_cannot_be_used_for_email_verification(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'email_verified_at' => null
        ]);

        // Generate password reset token
        $passwordToken = $customer->generatePasswordResetToken();
        
        // Try to use password reset token for email verification
        $response = $this->get(route('customer.verify-email', $passwordToken));
        
        $response->assertRedirect(route('customer.login'));
        $response->assertSessionHas('error', 'Invalid verification link.');
        
        // Email should not be verified
        $customer->refresh();
        $this->assertNull($customer->email_verified_at);
    }

    public function test_password_reset_token_is_cryptographically_secure(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => true
        ]);

        // Generate multiple tokens
        $tokens = [];
        for ($i = 0; $i < 10; $i++) {
            $tokens[] = $customer->generatePasswordResetToken();
        }

        // All tokens should be unique
        $this->assertEquals(count($tokens), count(array_unique($tokens)));
        
        // Each token should be 64 characters (32 bytes hex encoded)
        foreach ($tokens as $token) {
            $this->assertEquals(64, strlen($token));
            $this->assertTrue(ctype_xdigit($token)); // Should be valid hexadecimal
        }
    }

    public function test_password_reset_clears_token_after_successful_reset(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => true
        ]);

        $token = $customer->generatePasswordResetToken();
        
        // Reset password
        $response = $this->post(route('customer.password.reset'), [
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertRedirect(route('customer.login'));
        $response->assertSessionHas('success', 'Password reset successfully. You can now login with your new password.');
        
        // Token should be cleared
        $customer->refresh();
        $this->assertNull($customer->password_reset_token);
        $this->assertNull($customer->password_reset_expires_at);
        
        // Password should be changed
        $this->assertTrue(Hash::check('newpassword123', $customer->password));
    }

    public function test_failed_password_reset_attempts_are_logged(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => true
        ]);

        // Try to reset with invalid token
        $response = $this->post(route('customer.password.reset'), [
            'token' => 'invalid-token-12345',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        // Should log the failure
        $auditLog = CustomerAuditLog::where([
            'customer_id' => $customer->id,
            'action' => 'password_reset_failed',
            'success' => false
        ])->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals('Failed password reset attempt with invalid or expired token', $auditLog->description);
        $this->assertEquals('Invalid or expired reset token', $auditLog->failure_reason);
        
        $metadata = $auditLog->metadata;
        $this->assertTrue($metadata['token_provided']);
        $this->assertTrue($metadata['customer_found']);
        $this->assertEquals('invalid_password_reset_token', $metadata['security_violation']);
    }

    public function test_successful_password_reset_attempts_are_logged(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => true
        ]);

        $token = $customer->generatePasswordResetToken();
        
        // Reset password successfully
        $response = $this->post(route('customer.password.reset'), [
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        // Should log the success
        $auditLog = CustomerAuditLog::where([
            'customer_id' => $customer->id,
            'action' => 'password_reset_success',
            'success' => true
        ])->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals('Password reset successfully using valid token', $auditLog->description);
        
        $metadata = $auditLog->metadata;
        $this->assertEquals('reset_token', $metadata['password_change_method']);
        $this->assertTrue($metadata['security_checks_passed']);
    }

    public function test_token_reuse_prevention(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => true
        ]);

        $token = $customer->generatePasswordResetToken();
        
        // Use token once successfully
        $response = $this->post(route('customer.password.reset'), [
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertRedirect(route('customer.login'));
        
        // Try to use the same token again
        $response = $this->post(route('customer.password.reset'), [
            'token' => $token,
            'password' => 'anothernewpassword123',
            'password_confirmation' => 'anothernewpassword123'
        ]);

        $response->assertRedirect(route('customer.login'));
        $response->assertSessionHas('error', 'Invalid or expired reset token.');
        
        // Password should not be changed again
        $customer->refresh();
        $this->assertTrue(Hash::check('newpassword123', $customer->password));
        $this->assertFalse(Hash::check('anothernewpassword123', $customer->password));
    }
}