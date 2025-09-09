<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitoringMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = $endMemory - $startMemory;
        
        // Log performance metrics
        $performanceData = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'execution_time_ms' => round($executionTime, 2),
            'memory_usage_mb' => round($memoryUsage / 1024 / 1024, 2),
            'status_code' => $response->getStatusCode(),
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];
        
        // Log slow requests (> 1 second)
        if ($executionTime > 1000) {
            Log::channel('performance')->warning('Slow request detected', $performanceData);
        }
        
        // Log high memory usage (> 50MB)
        if ($memoryUsage > 50 * 1024 * 1024) {
            Log::channel('performance')->warning('High memory usage detected', $performanceData);
        }
        
        // Add performance headers for development
        if (config('app.debug')) {
            $response->headers->set('X-Execution-Time', $executionTime . 'ms');
            $response->headers->set('X-Memory-Usage', round($memoryUsage / 1024 / 1024, 2) . 'MB');
        }
        
        return $response;
    }
}