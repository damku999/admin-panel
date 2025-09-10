<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to monitor cache performance and request metrics
 */
class CachePerformanceMiddleware
{
    /**
     * Handle an incoming request and measure performance
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip performance monitoring for static assets and internal routes
        if ($this->shouldSkipMonitoring($request)) {
            return $next($request);
        }

        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        // Execute request
        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        // Calculate metrics
        $executionTime = round(($endTime - $startTime) * 1000, 2); // ms
        $memoryUsage = round(($endMemory - $startMemory) / 1024 / 1024, 2); // MB
        $peakMemory = round(memory_get_peak_usage(true) / 1024 / 1024, 2); // MB

        // Add performance headers for development
        if (app()->environment('local')) {
            $response->headers->add([
                'X-Execution-Time' => $executionTime . 'ms',
                'X-Memory-Usage' => $memoryUsage . 'MB',
                'X-Peak-Memory' => $peakMemory . 'MB',
            ]);
        }

        // Log slow requests (>1 second)
        if ($executionTime > 1000) {
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time_ms' => $executionTime,
                'memory_usage_mb' => $memoryUsage,
                'peak_memory_mb' => $peakMemory,
                'user_id' => auth()->id(),
            ]);
        }

        // Log high memory usage requests (>100MB)
        if ($peakMemory > 100) {
            Log::warning('High memory usage request', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time_ms' => $executionTime,
                'memory_usage_mb' => $memoryUsage,
                'peak_memory_mb' => $peakMemory,
                'user_id' => auth()->id(),
            ]);
        }

        return $response;
    }

    /**
     * Check if request should skip performance monitoring
     */
    private function shouldSkipMonitoring(Request $request): bool
    {
        $skipPaths = [
            'api/health',
            '_debugbar',
            'assets/',
            'css/',
            'js/',
            'images/',
            'fonts/',
            'favicon.ico',
        ];

        $path = $request->path();

        foreach ($skipPaths as $skipPath) {
            if (str_starts_with($path, $skipPath)) {
                return true;
            }
        }

        return false;
    }
}