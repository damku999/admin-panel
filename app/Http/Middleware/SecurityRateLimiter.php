<?php

namespace App\Http\Middleware;

use App\Models\CustomerAuditLog;
use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SecurityRateLimiter
{
    protected RateLimiter $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request with enhanced security rate limiting.
     */
    public function handle(Request $request, Closure $next, string $operation = 'general'): Response
    {
        $limits = $this->getSecurityLimits($operation);
        $key = $this->buildRateLimitKey($request, $operation);
        
        // Check if request is rate limited
        if ($this->limiter->tooManyAttempts($key, $limits['max_attempts'])) {
            $this->logRateLimitExceeded($request, $operation, $key);
            return $this->buildRateLimitResponse($request, $operation, $key);
        }

        // Log suspicious activity patterns
        $this->detectSuspiciousActivity($request, $operation, $key);

        // Increment rate limit counter
        $this->limiter->hit($key, $limits['decay_minutes'] * 60);

        $response = $next($request);

        // Add rate limit headers for transparency (in debug mode only)
        if (config('app.debug')) {
            $this->addRateLimitHeaders($response, $key, $limits);
        }

        return $response;
    }

    /**
     * Get security limits for different operations.
     */
    protected function getSecurityLimits(string $operation): array
    {
        $limits = [
            'login_attempts' => [
                'max_attempts' => 5,
                'decay_minutes' => 15,
                'lockout_minutes' => 60
            ],
            'password_reset' => [
                'max_attempts' => 3,
                'decay_minutes' => 60,
                'lockout_minutes' => 120
            ],
            'policy_downloads' => [
                'max_attempts' => 20,
                'decay_minutes' => 60,
                'lockout_minutes' => 30
            ],
            'policy_access' => [
                'max_attempts' => 100,
                'decay_minutes' => 60,
                'lockout_minutes' => 15
            ],
            'profile_changes' => [
                'max_attempts' => 5,
                'decay_minutes' => 60,
                'lockout_minutes' => 30
            ],
            'api_requests' => [
                'max_attempts' => 200,
                'decay_minutes' => 60,
                'lockout_minutes' => 15
            ],
            'suspicious_activity' => [
                'max_attempts' => 3,
                'decay_minutes' => 60,
                'lockout_minutes' => 240
            ]
        ];

        return $limits[$operation] ?? $limits['api_requests'];
    }

    /**
     * Build a comprehensive rate limit key.
     */
    protected function buildRateLimitKey(Request $request, string $operation): string
    {
        $customer = Auth::guard('customer')->user();
        $ip = $request->ip();
        
        // Use customer ID if authenticated, otherwise use IP
        $identifier = $customer ? "customer:{$customer->id}" : "ip:{$ip}";
        
        return "security_rate_limit:{$operation}:{$identifier}";
    }

    /**
     * Log when rate limits are exceeded.
     */
    protected function logRateLimitExceeded(Request $request, string $operation, string $key): void
    {
        $customer = Auth::guard('customer')->user();
        
        // Log to audit system
        if ($customer) {
            CustomerAuditLog::create([
                'customer_id' => $customer->id,
                'action' => 'rate_limit_exceeded',
                'description' => "Rate limit exceeded for {$operation}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
                'success' => false,
                'failure_reason' => 'Rate limit exceeded',
                'metadata' => [
                    'operation' => $operation,
                    'rate_limit_key' => $key,
                    'request_url' => $request->fullUrl(),
                    'request_method' => $request->method(),
                    'security_violation' => 'rate_limit_exceeded',
                    'attempts_remaining' => 0
                ]
            ]);
        }

        // Log to application logs
        \Log::warning('Security rate limit exceeded', [
            'operation' => $operation,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'customer_id' => $customer?->id,
            'rate_limit_key' => $key
        ]);
    }

    /**
     * Detect suspicious activity patterns.
     */
    protected function detectSuspiciousActivity(Request $request, string $operation, string $key): void
    {
        $attempts = $this->limiter->attempts($key);
        $limits = $this->getSecurityLimits($operation);
        $threshold = (int) ($limits['max_attempts'] * 0.8); // 80% of limit

        // Log warning when approaching rate limit
        if ($attempts >= $threshold) {
            $customer = Auth::guard('customer')->user();
            
            \Log::notice('Approaching rate limit threshold', [
                'operation' => $operation,
                'attempts' => $attempts,
                'threshold' => $threshold,
                'max_attempts' => $limits['max_attempts'],
                'customer_id' => $customer?->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        // Detect rapid-fire requests (potential bot activity)
        $rapidFireKey = $key . ':rapid_fire';
        if ($this->limiter->tooManyAttempts($rapidFireKey, 10)) {
            // Log potential bot activity
            \Log::warning('Potential bot activity detected', [
                'operation' => $operation,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'rate_limit_key' => $key
            ]);
        }
        
        $this->limiter->hit($rapidFireKey, 60); // 1-minute window for rapid fire detection
    }

    /**
     * Build appropriate response for rate-limited requests.
     */
    protected function buildRateLimitResponse(Request $request, string $operation, string $key): Response
    {
        $availableAt = $this->limiter->availableAt($key);
        $retryAfter = $availableAt - time();
        
        $isAjax = $request->expectsJson() || $request->ajax();
        
        if ($isAjax) {
            return response()->json([
                'error' => 'Too many requests. Please slow down.',
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => $retryAfter
            ], 429)->header('Retry-After', $retryAfter);
        }

        // For web requests, redirect with appropriate message
        $redirectRoute = $this->getAppropriateRedirectRoute($request, $operation);
        $message = $this->getUserFriendlyRateLimitMessage($operation, $retryAfter);
        
        return redirect()->route($redirectRoute)
            ->with('warning', $message);
    }

    /**
     * Get appropriate redirect route based on context.
     */
    protected function getAppropriateRedirectRoute(Request $request, string $operation): string
    {
        $customer = Auth::guard('customer')->user();
        
        if (!$customer) {
            return 'customer.login';
        }
        
        // Operation-specific redirects
        switch ($operation) {
            case 'login_attempts':
                return 'customer.login';
            case 'password_reset':
                return 'customer.login';
            case 'policy_downloads':
            case 'policy_access':
                return 'customer.policies';
            default:
                return 'customer.dashboard';
        }
    }

    /**
     * Get user-friendly rate limit messages.
     */
    protected function getUserFriendlyRateLimitMessage(string $operation, int $retryAfter): string
    {
        $minutes = ceil($retryAfter / 60);
        
        $messages = [
            'login_attempts' => "Too many login attempts. Please wait {$minutes} minutes before trying again.",
            'password_reset' => "Too many password reset requests. Please wait {$minutes} minutes.",
            'policy_downloads' => "Too many download requests. Please wait {$minutes} minutes before downloading again.",
            'policy_access' => "Too many policy access attempts. Please slow down.",
            'profile_changes' => "Too many profile changes. Please wait {$minutes} minutes.",
            'suspicious_activity' => "Suspicious activity detected. Access temporarily restricted."
        ];

        return $messages[$operation] ?? "Too many requests. Please wait {$minutes} minutes and try again.";
    }

    /**
     * Add rate limit headers to response for debugging.
     */
    protected function addRateLimitHeaders(Response $response, string $key, array $limits): void
    {
        $attempts = $this->limiter->attempts($key);
        $remaining = max(0, $limits['max_attempts'] - $attempts);
        $availableAt = $this->limiter->availableAt($key);
        
        $response->headers->add([
            'X-RateLimit-Limit' => $limits['max_attempts'],
            'X-RateLimit-Remaining' => $remaining,
            'X-RateLimit-Reset' => $availableAt
        ]);
    }
}