<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ApiThrottleMiddleware
{
    protected array $limits = [
        'auth' => ['attempts' => 5, 'decay' => 15], // 5 attempts per 15 minutes for auth endpoints
        'read' => ['attempts' => 100, 'decay' => 1], // 100 attempts per minute for read operations
        'write' => ['attempts' => 30, 'decay' => 1], // 30 attempts per minute for write operations
        'report' => ['attempts' => 10, 'decay' => 1], // 10 attempts per minute for reports
    ];
    
    public function handle(Request $request, Closure $next, string $type = 'read'): Response
    {
        if (!isset($this->limits[$type])) {
            $type = 'read'; // Default to read limits
        }
        
        $limit = $this->limits[$type];
        $key = $this->generateKey($request, $type);
        
        $attempts = Cache::get($key, 0);
        
        if ($attempts >= $limit['attempts']) {
            $retryAfter = $limit['decay'] * 60;
            
            return response()->json([
                'success' => false,
                'message' => 'Request limit exceeded for ' . $type . ' operations.',
                'error_code' => 'THROTTLE_EXCEEDED',
                'limit_type' => $type,
                'retry_after' => $retryAfter,
            ], 429)->withHeaders([
                'X-RateLimit-Limit' => $limit['attempts'],
                'X-RateLimit-Remaining' => 0,
                'X-RateLimit-Type' => $type,
                'Retry-After' => $retryAfter,
            ]);
        }
        
        // Increment attempt counter
        Cache::put($key, $attempts + 1, now()->addMinutes($limit['decay']));
        
        $response = $next($request);
        
        // Add throttle headers
        $remaining = max(0, $limit['attempts'] - ($attempts + 1));
        
        return $response->withHeaders([
            'X-RateLimit-Limit' => $limit['attempts'],
            'X-RateLimit-Remaining' => $remaining,
            'X-RateLimit-Type' => $type,
            'X-RateLimit-Reset' => now()->addMinutes($limit['decay'])->timestamp,
        ]);
    }
    
    protected function generateKey(Request $request, string $type): string
    {
        $user = $request->user();
        $identifier = $user ? 'user:' . $user->id : 'ip:' . $request->ip();
        
        return "throttle:{$type}:{$identifier}";
    }
}