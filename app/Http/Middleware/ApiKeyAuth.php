<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseInterface;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $ability = null): ResponseInterface
    {
        $startTime = microtime(true);

        // Extract API key from request
        $apiKey = $this->extractApiKey($request);

        if (!$apiKey) {
            return $this->unauthorizedResponse('API key is required');
        }

        // Find and validate API key
        $keyModel = ApiKey::active()->where('key', $apiKey)->first();

        if (!$keyModel) {
            return $this->unauthorizedResponse('Invalid API key');
        }

        // Check if key is expired or inactive
        if (!$keyModel->isActive()) {
            return $this->unauthorizedResponse('API key is expired or inactive');
        }

        // Check IP restrictions
        if (!$keyModel->isIpAllowed($request->ip())) {
            return $this->forbiddenResponse('IP address not allowed for this API key');
        }

        // Check endpoint access
        $endpoint = $request->route()?->uri() ?? $request->path();
        if (!$keyModel->hasEndpointAccess($endpoint, $request->method())) {
            return $this->forbiddenResponse('Insufficient permissions for this endpoint');
        }

        // Check specific ability if provided
        if ($ability && !$keyModel->hasAbility($ability)) {
            return $this->forbiddenResponse("Missing required ability: {$ability}");
        }

        // Check rate limiting
        if ($keyModel->isRateLimited()) {
            return $this->rateLimitResponse($keyModel);
        }

        // Update last used timestamp
        $keyModel->updateLastUsed();

        // Set the authenticated API key in the request
        $request->attributes->set('api_key', $keyModel);
        $request->attributes->set('api_key_owner', $keyModel->keyable);

        // Process the request
        $response = $next($request);

        // Log API usage
        $this->logApiUsage($request, $response, $keyModel, $startTime);

        // Add rate limit headers
        $this->addRateLimitHeaders($response, $keyModel);

        return $response;
    }

    /**
     * Extract API key from request headers or query parameters
     */
    protected function extractApiKey(Request $request): ?string
    {
        // Check Authorization header (Bearer token)
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }

        // Check X-API-Key header
        $apiKeyHeader = $request->header('X-API-Key');
        if ($apiKeyHeader) {
            return $apiKeyHeader;
        }

        // Check query parameter (less secure, but sometimes necessary)
        return $request->query('api_key');
    }

    /**
     * Log API key usage for analytics and monitoring
     */
    protected function logApiUsage(Request $request, $response, ApiKey $apiKey, float $startTime): void
    {
        $responseTime = microtime(true) - $startTime;
        $endpoint = $request->route()?->uri() ?? $request->path();

        // Get response data (limit size for storage)
        $responseData = null;
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $responseContent = $response->getData(true);
            // Limit response data size to prevent database bloat
            if (json_encode($responseContent) && strlen(json_encode($responseContent)) < 10000) {
                $responseData = $responseContent;
            }
        }

        // Get request data (excluding sensitive information)
        $requestData = $this->sanitizeRequestData($request->all());

        $apiKey->logUsage(
            endpoint: $endpoint,
            method: $request->method(),
            ip: $request->ip(),
            responseCode: $response->getStatusCode(),
            requestData: $requestData,
            responseData: $responseData,
            responseTime: $responseTime
        );
    }

    /**
     * Add rate limit headers to response
     */
    protected function addRateLimitHeaders($response, ApiKey $apiKey): void
    {
        $used = $apiKey->getRateLimitUsage();
        $limit = $apiKey->rate_limit;
        $remaining = max(0, $limit - $used);
        $resetTime = now()->addSeconds($apiKey->rate_limit_window)->timestamp;

        $response->headers->set('X-RateLimit-Limit', $limit);
        $response->headers->set('X-RateLimit-Remaining', $remaining);
        $response->headers->set('X-RateLimit-Reset', $resetTime);
        $response->headers->set('X-RateLimit-Window', $apiKey->rate_limit_window);
    }

    /**
     * Remove sensitive data from request for logging
     */
    protected function sanitizeRequestData(array $data): array
    {
        $sensitiveFields = ['password', 'token', 'secret', 'api_key', 'credit_card', 'ssn'];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }

        return $data;
    }

    /**
     * Return unauthorized response
     */
    protected function unauthorizedResponse(string $message): Response
    {
        return response()->json([
            'error' => 'Unauthorized',
            'message' => $message,
            'status_code' => 401
        ], 401);
    }

    /**
     * Return forbidden response
     */
    protected function forbiddenResponse(string $message): Response
    {
        return response()->json([
            'error' => 'Forbidden',
            'message' => $message,
            'status_code' => 403
        ], 403);
    }

    /**
     * Return rate limit exceeded response
     */
    protected function rateLimitResponse(ApiKey $apiKey): Response
    {
        $used = $apiKey->getRateLimitUsage();
        $limit = $apiKey->rate_limit;
        $resetTime = now()->addSeconds($apiKey->rate_limit_window)->timestamp;

        return response()->json([
            'error' => 'Rate Limit Exceeded',
            'message' => "API rate limit of {$limit} requests per {$apiKey->rate_limit_window} seconds exceeded",
            'status_code' => 429,
            'rate_limit' => [
                'limit' => $limit,
                'used' => $used,
                'reset_at' => $resetTime
            ]
        ], 429);
    }
}