<?php

namespace Tests\Security;

use App\Models\Customer;
use App\Models\CustomerAuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SessionTimeoutSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'timeout.test@example.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'email_verified_at' => now()
        ]);
    }

    public function test_session_timeout_middleware_allows_active_sessions(): void
    {
        // Set recent activity timestamp
        session(['customer_last_activity' => now()->timestamp]);
        
        $this->actingAs($this->customer, 'customer');
        
        // Should be able to access protected route
        $response = $this->get(route('customer.dashboard'));
        $response->assertStatus(200);
        
        // Session should still be active
        $this->assertAuthenticatedAs($this->customer, 'customer');
    }

    public function test_session_timeout_middleware_expires_inactive_sessions(): void
    {
        // Set old activity timestamp (more than 60 minutes ago)
        $oldTimestamp = now()->subMinutes(61)->timestamp;
        session(['customer_last_activity' => $oldTimestamp]);
        
        $this->actingAs($this->customer, 'customer');
        
        // Try to access protected route
        $response = $this->get(route('customer.dashboard'));
        
        // Should be redirected to login
        $response->assertRedirect(route('customer.login'));
        $response->assertSessionHas('warning', 'Your session has expired due to inactivity. Please log in again.');
        
        // Should be logged out
        $this->assertGuest('customer');
    }

    public function test_session_timeout_logs_forced_logout(): void
    {
        // Set old activity timestamp
        $oldTimestamp = now()->subMinutes(65)->timestamp;
        session(['customer_last_activity' => $oldTimestamp]);
        
        $this->actingAs($this->customer, 'customer');
        
        // Access protected route to trigger timeout
        $response = $this->get(route('customer.dashboard'));
        
        // Should log the session timeout
        $auditLog = CustomerAuditLog::where([
            'customer_id' => $this->customer->id,
            'action' => 'session_timeout',
            'success' => false
        ])->first();
        
        $this->assertNotNull($auditLog);
        $this->assertEquals('Session expired due to inactivity', $auditLog->description);
        $this->assertEquals('Session timeout due to inactivity', $auditLog->failure_reason);
        
        $metadata = $auditLog->metadata;
        $this->assertArrayHasKey('timeout_minutes', $metadata);
        $this->assertArrayHasKey('inactive_duration_minutes', $metadata);
        $this->assertArrayHasKey('security_action', $metadata);
        $this->assertEquals('forced_logout', $metadata['security_action']);
        $this->assertEquals(60, $metadata['timeout_minutes']);
    }

    public function test_session_timeout_updates_activity_timestamp(): void
    {
        $initialTimestamp = now()->subMinutes(30)->timestamp;
        session(['customer_last_activity' => $initialTimestamp]);
        
        $this->actingAs($this->customer, 'customer');
        
        // Access protected route
        $response = $this->get(route('customer.dashboard'));
        $response->assertStatus(200);
        
        // Activity timestamp should be updated
        $updatedTimestamp = session('customer_last_activity');
        $this->assertGreaterThan($initialTimestamp, $updatedTimestamp);
        $this->assertEqualsWithDelta(now()->timestamp, $updatedTimestamp, 5); // Within 5 seconds
    }

    public function test_session_timeout_middleware_sets_initial_activity_on_login(): void
    {
        // Login customer
        $response = $this->post(route('customer.login'), [
            'email' => 'timeout.test@example.com',
            'password' => 'password123'
        ]);
        
        $response->assertRedirect(route('customer.dashboard'));
        
        // Should set initial last activity timestamp
        $lastActivity = session('customer_last_activity');
        $this->assertNotNull($lastActivity);
        $this->assertEqualsWithDelta(now()->timestamp, $lastActivity, 5); // Within 5 seconds
    }

    public function test_session_timeout_does_not_affect_unauthenticated_users(): void
    {
        // Set old activity timestamp but don't authenticate
        session(['customer_last_activity' => now()->subMinutes(70)->timestamp]);
        
        // Should be able to access login page
        $response = $this->get(route('customer.login'));
        $response->assertStatus(200);
        
        // No audit log should be created
        $auditLogCount = CustomerAuditLog::where('action', 'session_timeout')->count();
        $this->assertEquals(0, $auditLogCount);
    }

    public function test_session_timeout_handles_missing_activity_timestamp(): void
    {
        // Don't set any activity timestamp
        $this->actingAs($this->customer, 'customer');
        
        // Access protected route
        $response = $this->get(route('customer.dashboard'));
        $response->assertStatus(200);
        
        // Should set activity timestamp
        $lastActivity = session('customer_last_activity');
        $this->assertNotNull($lastActivity);
        $this->assertEqualsWithDelta(now()->timestamp, $lastActivity, 5);
    }

    public function test_session_timeout_respects_configured_timeout_value(): void
    {
        // Temporarily change config
        config(['session.customer_timeout' => 30]); // 30 minutes
        
        // Set activity timestamp to 31 minutes ago
        $oldTimestamp = now()->subMinutes(31)->timestamp;
        session(['customer_last_activity' => $oldTimestamp]);
        
        $this->actingAs($this->customer, 'customer');
        
        // Should timeout with 30-minute threshold
        $response = $this->get(route('customer.dashboard'));
        $response->assertRedirect(route('customer.login'));
        $this->assertGuest('customer');
        
        // Reset config
        config(['session.customer_timeout' => 60]);
    }

    public function test_session_timeout_adds_debug_headers_in_development(): void
    {
        // Enable debug mode
        config(['app.debug' => true]);
        
        session(['customer_last_activity' => now()->subMinutes(10)->timestamp]);
        $this->actingAs($this->customer, 'customer');
        
        $response = $this->get(route('customer.dashboard'));
        
        if (config('app.debug')) {
            $response->assertHeader('X-Session-Timeout');
            $response->assertHeader('X-Last-Activity');
            $response->assertHeader('X-Time-Remaining');
        }
        
        // Reset debug mode
        config(['app.debug' => false]);
    }

    public function test_multiple_requests_update_activity_correctly(): void
    {
        $this->actingAs($this->customer, 'customer');
        
        // Make first request
        $response1 = $this->get(route('customer.dashboard'));
        $response1->assertStatus(200);
        $firstActivity = session('customer_last_activity');
        
        // Wait a moment
        sleep(1);
        
        // Make second request
        $response2 = $this->get(route('customer.profile'));
        $response2->assertStatus(200);
        $secondActivity = session('customer_last_activity');
        
        // Second activity should be more recent than first
        $this->assertGreaterThan($firstActivity, $secondActivity);
    }

    public function test_session_timeout_works_across_different_routes(): void
    {
        // Set old activity timestamp
        $oldTimestamp = now()->subMinutes(61)->timestamp;
        session(['customer_last_activity' => $oldTimestamp]);
        
        $this->actingAs($this->customer, 'customer');
        
        $protectedRoutes = [
            'customer.dashboard',
            'customer.profile',
            'customer.change-password'
        ];
        
        foreach ($protectedRoutes as $route) {
            $response = $this->get(route($route));
            $response->assertRedirect(route('customer.login'));
            $this->assertGuest('customer');
            
            // Re-authenticate for next route test
            $this->actingAs($this->customer, 'customer');
            session(['customer_last_activity' => $oldTimestamp]);
        }
    }

    public function test_session_timeout_preserves_csrf_token_regeneration(): void
    {
        // Set old activity timestamp
        $oldTimestamp = now()->subMinutes(61)->timestamp;
        session(['customer_last_activity' => $oldTimestamp]);
        
        $oldCsrfToken = csrf_token();
        $this->actingAs($this->customer, 'customer');
        
        // Access route to trigger timeout
        $response = $this->get(route('customer.dashboard'));
        
        // CSRF token should be regenerated
        $newCsrfToken = csrf_token();
        $this->assertNotEquals($oldCsrfToken, $newCsrfToken);
    }

    public function test_session_timeout_middleware_order_is_correct(): void
    {
        // The timeout middleware should run after auth but before other customer middleware
        // This is tested by ensuring proper functionality when timeout occurs
        
        $oldTimestamp = now()->subMinutes(65)->timestamp;
        session(['customer_last_activity' => $oldTimestamp]);
        
        $this->actingAs($this->customer, 'customer');
        
        // Try to access a route with multiple middleware
        $response = $this->get(route('customer.dashboard'));
        
        // Should timeout before other middleware can run
        $response->assertRedirect(route('customer.login'));
        $response->assertSessionHas('warning');
        $this->assertGuest('customer');
    }
}