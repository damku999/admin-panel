<?php

namespace App\Http\Middleware;

use App\Services\LoggingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApplicationMonitoringMiddleware
{
    private LoggingService $logger;
    
    public function __construct(LoggingService $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * Handle an incoming request with comprehensive monitoring
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip monitoring for assets and health checks
        if ($this->shouldSkipMonitoring($request)) {
            return $next($request);
        }
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        $queryCountBefore = $this->getQueryCount();
        
        // Add request context to logger
        $this->logger->addContext('request_id', uniqid('req_', true))
                    ->addContext('url', $request->fullUrl())
                    ->addContext('method', $request->method())
                    ->addContext('user_id', auth()->id())
                    ->addContext('ip_address', $request->ip());
        
        $response = null;
        $exception = null;
        
        try {
            $response = $next($request);
            
            // Log successful request
            $this->logRequestMetrics($request, $response, $startTime, $startMemory, $queryCountBefore);
            
        } catch (Throwable $e) {
            $exception = $e;
            
            // Log failed request
            $this->logger->logError($e, [
                'request_method' => $request->method(),
                'request_url' => $request->fullUrl(),
                'request_input' => $request->except(['password', 'password_confirmation', '_token']),
                'execution_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ]);
            
            throw $e;
        }
        
        return $response;
    }
    
    /**
     * Log comprehensive request metrics
     */
    private function logRequestMetrics(Request $request, Response $response, float $startTime, int $startMemory, int $queryCountBefore): void
    {
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // milliseconds
        $memoryUsage = (memory_get_usage(true) - $startMemory) / 1024 / 1024; // MB
        $queryCount = $this->getQueryCount() - $queryCountBefore;
        $statusCode = $response->getStatusCode();
        
        // Determine log level based on performance and status
        $logLevel = $this->determineLogLevel($executionTime, $statusCode, $queryCount);
        
        $metrics = [
            'performance' => [
                'execution_time_ms' => round($executionTime, 2),
                'memory_usage_mb' => round($memoryUsage, 2),
                'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
                'query_count' => $queryCount,
            ],
            'request' => [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'path' => $request->path(),
                'query_params' => $request->query(),
                'content_type' => $request->header('Content-Type'),
                'user_agent' => $request->header('User-Agent'),
                'referer' => $request->header('Referer'),
            ],
            'response' => [
                'status_code' => $statusCode,
                'content_length' => $response->headers->get('Content-Length'),
                'content_type' => $response->headers->get('Content-Type'),
            ],
            'user_context' => [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()?->email,
                'ip_address' => $request->ip(),
                'session_id' => session()->getId(),
            ],
        ];
        
        // Log performance metrics
        $this->logger->logPerformanceMetric(
            "HTTP {$request->method()} {$request->path()}", 
            $executionTime, 
            $metrics
        );
        
        // Log slow requests with additional detail
        if ($executionTime > 2000) {
            $this->logger->logEvent('slow_request', [
                'threshold_ms' => 2000,
                'actual_time_ms' => round($executionTime, 2),
                'query_count' => $queryCount,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ], 'warning');
        }
        
        // Log high query count requests
        if ($queryCount > 20) {
            $this->logger->logEvent('high_query_count', [
                'query_count' => $queryCount,
                'threshold' => 20,
                'url' => $request->fullUrl(),
                'execution_time_ms' => round($executionTime, 2),
            ], 'warning');
        }
        
        // Log high memory usage requests
        if ($memoryUsage > 50) {
            $this->logger->logEvent('high_memory_usage', [
                'memory_usage_mb' => round($memoryUsage, 2),
                'threshold_mb' => 50,
                'url' => $request->fullUrl(),
                'execution_time_ms' => round($executionTime, 2),
            ], 'warning');
        }
        
        // Log HTTP errors
        if ($statusCode >= 400) {
            $errorLevel = $statusCode >= 500 ? 'error' : 'warning';
            $this->logger->logEvent('http_error', [
                'status_code' => $statusCode,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time_ms' => round($executionTime, 2),
            ], $errorLevel);
        }
        
        // Add performance headers in development
        if (app()->environment('local', 'development')) {
            $response->headers->add([
                'X-Debug-Time' => round($executionTime, 2) . 'ms',
                'X-Debug-Memory' => round($memoryUsage, 2) . 'MB',
                'X-Debug-Queries' => $queryCount,
            ]);
        }
    }
    
    /**
     * Determine appropriate log level based on metrics
     */
    private function determineLogLevel(float $executionTime, int $statusCode, int $queryCount): string
    {
        if ($statusCode >= 500) {
            return 'error';
        }
        
        if ($statusCode >= 400 || $executionTime > 2000 || $queryCount > 20) {
            return 'warning';
        }
        
        if ($executionTime > 1000 || $queryCount > 10) {
            return 'notice';
        }
        
        return 'info';
    }
    
    /**
     * Get current database query count
     */
    private function getQueryCount(): int
    {
        return count(DB::getQueryLog());
    }
    
    /**
     * Check if monitoring should be skipped for this request
     */
    private function shouldSkipMonitoring(Request $request): bool
    {
        $skipPaths = [
            'health',
            'api/health',
            '_debugbar',
            'assets/',
            'css/',
            'js/',
            'images/',
            'fonts/',
            'favicon.ico',
            'robots.txt',
            'sitemap.xml',
        ];
        
        $path = $request->path();
        
        foreach ($skipPaths as $skipPath) {
            if (str_starts_with($path, $skipPath)) {
                return true;
            }
        }
        
        // Skip monitoring for certain content types
        $contentType = $request->header('Content-Type', '');
        $skipContentTypes = [
            'image/',
            'font/',
            'text/css',
            'application/javascript',
        ];
        
        foreach ($skipContentTypes as $skipType) {
            if (str_starts_with($contentType, $skipType)) {
                return true;
            }
        }
        
        return false;
    }
}