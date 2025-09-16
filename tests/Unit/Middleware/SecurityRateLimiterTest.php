<?php

namespace Tests\Unit\Middleware;

use Tests\BaseTestCase;
use App\Http\Middleware\SecurityRateLimiter;
use App\Models\Customer;
use App\Models\CustomerAuditLog;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Mockery;

class SecurityRateLimiterTest extends BaseTestCase
{
    private SecurityRateLimiter $middleware;
    private $mockRateLimiter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRateLimiter = Mockery::mock(RateLimiter::class)->shouldAllowMockingProtectedMethods();
        $this->middleware = new SecurityRateLimiter($this->mockRateLimiter);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // =============================================================================
    // BASIC FUNCTIONALITY TESTS
    // =============================================================================

    /** @test */
    public function middleware_allows_request_when_under_rate_limit(): void
    {
        $request = Request::create('/test', 'GET');
        $operation = 'api_requests';

        // Mock for main rate limiting check
        $this->mockRateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->andReturn(false);

        // Mock for suspicious activity detection
        $this->mockRateLimiter
            ->shouldReceive('attempts')
            ->once()
            ->andReturn(1);

        // Mock for rapid fire detection
        $this->mockRateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->andReturn(false);

        // Mock for rate limit counter increment
        $this->mockRateLimiter
            ->shouldReceive('hit')
            ->twice(); // Once for main counter, once for rapid fire

        // Mock for debug headers (if app.debug is true)
        if (config('app.debug')) {
            $this->mockRateLimiter
                ->shouldReceive('attempts')
                ->once()
                ->andReturn(1);

            $this->mockRateLimiter
                ->shouldReceive('availableAt')
                ->once()
                ->andReturn(time() + 3600);
        }

        $response = $this->middleware->handle($request, function ($req) {
            return new Response('Success', 200);
        }, $operation);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }

    /** @test */
    public function middleware_blocks_request_when_rate_limit_exceeded(): void
    {
        $request = Request::create('/test', 'GET');
        $operation = 'login_attempts';

        $this->mockRateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->andReturn(true);

        $this->mockRateLimiter
            ->shouldReceive('availableAt')
            ->once()
            ->andReturn(time() + 900); // 15 minutes

        $response = $this->middleware->handle($request, function ($req) {
            return new Response('Should not reach here', 200);
        }, $operation);

        $this->assertEquals(302, $response->getStatusCode()); // Redirect for web requests
    }

    /** @test */
    public function middleware_returns_json_response_for_ajax_requests(): void
    {
        $request = Request::create('/api/test', 'POST');
        $request->headers->set('Accept', 'application/json');
        $operation = 'api_requests';

        $this->mockRateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->andReturn(true);

        $this->mockRateLimiter
            ->shouldReceive('availableAt')
            ->once()
            ->andReturn(time() + 300); // 5 minutes

        $response = $this->middleware->handle($request, function ($req) {
            return new Response('Should not reach here', 200);
        }, $operation);

        $this->assertEquals(429, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Too many requests. Please slow down.', $responseData['error']);
        $this->assertArrayHasKey('retry_after', $responseData);
    }

    // =============================================================================
    // RATE LIMIT CONFIGURATION TESTS
    // =============================================================================

    /** @test */
    public function get_security_limits_returns_correct_limits_for_login_attempts(): void
    {
        $reflection = new \ReflectionClass($this->middleware);
        $method = $reflection->getMethod('getSecurityLimits');
        $method->setAccessible(true);

        $limits = $method->invoke($this->middleware, 'login_attempts');

        $this->assertEquals(5, $limits['max_attempts']);
        $this->assertEquals(15, $limits['decay_minutes']);
        $this->assertEquals(60, $limits['lockout_minutes']);
    }

    /** @test */
    public function get_security_limits_returns_correct_limits_for_password_reset(): void
    {
        $reflection = new \ReflectionClass($this->middleware);
        $method = $reflection->getMethod('getSecurityLimits');
        $method->setAccessible(true);

        $limits = $method->invoke($this->middleware, 'password_reset');

        $this->assertEquals(3, $limits['max_attempts']);
        $this->assertEquals(60, $limits['decay_minutes']);
        $this->assertEquals(120, $limits['lockout_minutes']);
    }

    /** @test */
    public function get_security_limits_returns_default_for_unknown_operation(): void
    {
        $reflection = new \ReflectionClass($this->middleware);
        $method = $reflection->getMethod('getSecurityLimits');
        $method->setAccessible(true);

        $limits = $method->invoke($this->middleware, 'unknown_operation');

        // Should return api_requests default
        $this->assertEquals(200, $limits['max_attempts']);
        $this->assertEquals(60, $limits['decay_minutes']);
        $this->assertEquals(15, $limits['lockout_minutes']);
    }

    // =============================================================================
    // RATE LIMIT KEY GENERATION TESTS
    // =============================================================================

    /** @test */
    public function build_rate_limit_key_uses_customer_id_when_authenticated(): void
    {
        $customer = Customer::factory()->create();
        $this->actingAs($customer, 'customer');

        $request = Request::create('/test', 'GET');
        $operation = 'policy_access';

        $reflection = new \ReflectionClass($this->middleware);
        $method = $reflection->getMethod('buildRateLimitKey');
        $method->setAccessible(true);

        $key = $method->invoke($this->middleware, $request, $operation);

        $expectedKey = "security_rate_limit:policy_access:customer:{$customer->id}";
        $this->assertEquals($expectedKey, $key);
    }

    /** @test */
    public function build_rate_limit_key_uses_ip_when_not_authenticated(): void
    {
        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');
        $operation = 'login_attempts';

        $reflection = new \ReflectionClass($this->middleware);
        $method = $reflection->getMethod('buildRateLimitKey');
        $method->setAccessible(true);

        $key = $method->invoke($this->middleware, $request, $operation);

        $expectedKey = "security_rate_limit:login_attempts:ip:192.168.1.100";
        $this->assertEquals($expectedKey, $key);
    }

    // =============================================================================
    // AUDIT LOGGING TESTS
    // =============================================================================

    /** @test */
    public function middleware_logs_rate_limit_exceeded_for_authenticated_customer(): void
    {
        $customer = Customer::factory()->create();
        $this->actingAs($customer, 'customer');

        $request = Request::create('/test', 'POST');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');
        $request->headers->set('User-Agent', 'Test Browser');

        $operation = 'profile_changes';

        $this->mockRateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->andReturn(true);

        $this->mockRateLimiter
            ->shouldReceive('availableAt')
            ->once()
            ->andReturn(time() + 1800); // 30 minutes

        $response = $this->middleware->handle($request, function ($req) {
            return new Response('Should not reach here', 200);
        }, $operation);

        // Check that audit log was created
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $customer->id,
            'action' => 'rate_limit_exceeded',
            'success' => false,
            'failure_reason' => 'Rate limit exceeded',
        ]);

        $auditLog = CustomerAuditLog::where([
            'customer_id' => $customer->id,
            'action' => 'rate_limit_exceeded'
        ])->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals('192.168.1.100', $auditLog->ip_address);
        $this->assertEquals('Test Browser', $auditLog->user_agent);

        $metadata = is_array($auditLog->metadata) ? $auditLog->metadata : json_decode($auditLog->metadata, true);
        $this->assertEquals($operation, $metadata['operation']);
        $this->assertEquals('rate_limit_exceeded', $metadata['security_violation']);
        $this->assertEquals(0, $metadata['attempts_remaining']);
    }

    // =============================================================================
    // SUSPICIOUS ACTIVITY DETECTION TESTS
    // =============================================================================

    /** @test */
    public function middleware_detects_suspicious_activity_when_approaching_limit(): void
    {
        $request = Request::create('/test', 'GET');
        $operation = 'policy_downloads';

        // Mock 80% of max attempts (16 out of 20 for policy_downloads)
        $this->mockRateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->andReturn(false);

        $this->mockRateLimiter
            ->shouldReceive('hit')
            ->twice(); // Once for main counter, once for rapid fire

        $this->mockRateLimiter
            ->shouldReceive('attempts')
            ->once()
            ->andReturn(16); // 80% of 20

        $this->mockRateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->with(Mockery::pattern('/rapid_fire$/'), 10)
            ->andReturn(false);

        // Mock for debug headers (if app.debug is true)
        if (config('app.debug')) {
            $this->mockRateLimiter
                ->shouldReceive('attempts')
                ->once()
                ->andReturn(16);

            $this->mockRateLimiter
                ->shouldReceive('availableAt')
                ->once()
                ->andReturn(time() + 3600);
        }

        // This should log a notice about approaching threshold
        $response = $this->middleware->handle($request, function ($req) {
            return new Response('Success', 200);
        }, $operation);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function middleware_detects_rapid_fire_bot_activity(): void
    {
        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');
        $operation = 'api_requests';

        $this->mockRateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->with(Mockery::pattern('/^security_rate_limit:api_requests/'), 200)
            ->andReturn(false);

        $this->mockRateLimiter
            ->shouldReceive('hit')
            ->twice();

        $this->mockRateLimiter
            ->shouldReceive('attempts')
            ->once()
            ->andReturn(50); // Below warning threshold

        // Simulate rapid fire detection
        $this->mockRateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->with(Mockery::pattern('/rapid_fire$/'), 10)
            ->andReturn(true); // Rapid fire detected

        // Mock for debug headers (if app.debug is true)
        if (config('app.debug')) {
            $this->mockRateLimiter
                ->shouldReceive('attempts')
                ->once()
                ->andReturn(50);

            $this->mockRateLimiter
                ->shouldReceive('availableAt')
                ->once()
                ->andReturn(time() + 3600);
        }

        $response = $this->middleware->handle($request, function ($req) {
            return new Response('Success', 200);
        }, $operation);

        $this->assertEquals(200, $response->getStatusCode());
    }

    // =============================================================================
    // REDIRECT ROUTE TESTS
    // =============================================================================

    /** @test */
    public function get_appropriate_redirect_route_returns_login_for_unauthenticated_user(): void
    {
        $request = Request::create('/test', 'GET');

        $reflection = new \ReflectionClass($this->middleware);
        $method = $reflection->getMethod('getAppropriateRedirectRoute');
        $method->setAccessible(true);

        $route = $method->invoke($this->middleware, $request, 'login_attempts');
        $this->assertEquals('customer.login', $route);
    }

    /** @test */
    public function get_appropriate_redirect_route_returns_operation_specific_routes(): void
    {
        $customer = $this->createCustomer();
        $this->actingAs($customer, 'customer');

        $request = Request::create('/test', 'GET');

        $reflection = new \ReflectionClass($this->middleware);
        $method = $reflection->getMethod('getAppropriateRedirectRoute');
        $method->setAccessible(true);

        // Test policy operations redirect to policies
        $route = $method->invoke($this->middleware, $request, 'policy_downloads');
        $this->assertEquals('customer.policies', $route);

        $route = $method->invoke($this->middleware, $request, 'policy_access');
        $this->assertEquals('customer.policies', $route);

        // Test default redirect to dashboard
        $route = $method->invoke($this->middleware, $request, 'general');
        $this->assertEquals('customer.dashboard', $route);
    }

    // =============================================================================
    // USER FRIENDLY MESSAGE TESTS
    // =============================================================================

    /** @test */
    public function get_user_friendly_rate_limit_message_returns_operation_specific_messages(): void
    {
        $reflection = new \ReflectionClass($this->middleware);
        $method = $reflection->getMethod('getUserFriendlyRateLimitMessage');
        $method->setAccessible(true);

        $message = $method->invoke($this->middleware, 'login_attempts', 900); // 15 minutes
        $this->assertStringContainsString('Too many login attempts', $message);
        $this->assertStringContainsString('15 minutes', $message);

        $message = $method->invoke($this->middleware, 'password_reset', 1800); // 30 minutes
        $this->assertStringContainsString('password reset requests', $message);
        $this->assertStringContainsString('30 minutes', $message);

        $message = $method->invoke($this->middleware, 'policy_downloads', 600); // 10 minutes
        $this->assertStringContainsString('download requests', $message);
        $this->assertStringContainsString('10 minutes', $message);

        $message = $method->invoke($this->middleware, 'suspicious_activity', 3600); // 1 hour
        $this->assertStringContainsString('Suspicious activity detected', $message);
    }

    /** @test */
    public function get_user_friendly_rate_limit_message_handles_unknown_operations(): void
    {
        $reflection = new \ReflectionClass($this->middleware);
        $method = $reflection->getMethod('getUserFriendlyRateLimitMessage');
        $method->setAccessible(true);

        $message = $method->invoke($this->middleware, 'unknown_operation', 1200); // 20 minutes
        $this->assertStringContainsString('Too many requests', $message);
        $this->assertStringContainsString('20 minutes', $message);
    }

    // =============================================================================
    // DEBUG HEADER TESTS
    // =============================================================================

    /** @test */
    public function middleware_adds_rate_limit_headers_in_debug_mode(): void
    {
        // Enable debug mode
        config(['app.debug' => true]);

        $request = Request::create('/test', 'GET');
        $operation = 'api_requests';

        $this->mockRateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->andReturn(false);

        $this->mockRateLimiter
            ->shouldReceive('hit')
            ->twice();

        $this->mockRateLimiter
            ->shouldReceive('attempts')
            ->twice() // Once for suspicious activity, once for headers
            ->andReturn(50);

        $this->mockRateLimiter
            ->shouldReceive('availableAt')
            ->once()
            ->andReturn(time() + 3600);

        $this->mockRateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->with(Mockery::pattern('/rapid_fire$/'), 10)
            ->andReturn(false);

        $response = $this->middleware->handle($request, function ($req) {
            return new Response('Success', 200);
        }, $operation);

        $this->assertTrue($response->headers->has('X-RateLimit-Limit'));
        $this->assertTrue($response->headers->has('X-RateLimit-Remaining'));
        $this->assertTrue($response->headers->has('X-RateLimit-Reset'));

        $this->assertEquals('200', $response->headers->get('X-RateLimit-Limit'));
        $this->assertEquals('150', $response->headers->get('X-RateLimit-Remaining')); // 200 - 50
    }

    /** @test */
    public function middleware_does_not_add_headers_when_debug_disabled(): void
    {
        // Disable debug mode
        config(['app.debug' => false]);

        $request = Request::create('/test', 'GET');
        $operation = 'api_requests';

        $this->mockRateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->andReturn(false);

        $this->mockRateLimiter
            ->shouldReceive('hit')
            ->twice();

        $this->mockRateLimiter
            ->shouldReceive('attempts')
            ->once()
            ->andReturn(50);

        $this->mockRateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->with(Mockery::pattern('/rapid_fire$/'), 10)
            ->andReturn(false);

        $response = $this->middleware->handle($request, function ($req) {
            return new Response('Success', 200);
        }, $operation);

        $this->assertFalse($response->headers->has('X-RateLimit-Limit'));
        $this->assertFalse($response->headers->has('X-RateLimit-Remaining'));
        $this->assertFalse($response->headers->has('X-RateLimit-Reset'));
    }
}