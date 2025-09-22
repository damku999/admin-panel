<?php

namespace App\Traits;

use App\Models\ApiKey;
use App\Services\ApiKeyService;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasApiKeys
{
    /**
     * Get all API keys for this model
     */
    public function apiKeys(): MorphMany
    {
        return $this->morphMany(ApiKey::class, 'keyable');
    }

    /**
     * Get active API keys for this model
     */
    public function activeApiKeys()
    {
        return $this->apiKeys()->active();
    }

    /**
     * Create a new API key
     */
    public function createApiKey(
        string $name,
        array $abilities = null,
        array $restrictions = null,
        int $rateLimit = 1000,
        int $rateLimitWindow = 3600,
        \DateTimeInterface $expiresAt = null
    ): ApiKey {
        $service = app(ApiKeyService::class);

        return $service->createApiKey(
            keyable: $this,
            name: $name,
            abilities: $abilities,
            restrictions: $restrictions,
            rateLimit: $rateLimit,
            rateLimitWindow: $rateLimitWindow,
            expiresAt: $expiresAt
        );
    }

    /**
     * Create a standard API key with common permissions
     */
    public function createStandardApiKey(string $name): ApiKey
    {
        $service = app(ApiKeyService::class);
        return $service->createStandardApiKey($this, $name);
    }

    /**
     * Create a read-only API key
     */
    public function createReadOnlyApiKey(string $name): ApiKey
    {
        $service = app(ApiKeyService::class);
        return $service->createReadOnlyApiKey($this, $name);
    }

    /**
     * Create an admin API key (only for admin users)
     */
    public function createAdminApiKey(string $name): ApiKey
    {
        // Check if this is an admin user (customize based on your role system)
        if (method_exists($this, 'hasRole') && !$this->hasRole('admin')) {
            throw new \UnauthorizedException('Only admin users can create admin API keys');
        }

        $service = app(ApiKeyService::class);
        return $service->createAdminApiKey($this, $name);
    }

    /**
     * Revoke an API key
     */
    public function revokeApiKey(ApiKey $apiKey): bool
    {
        // Ensure the API key belongs to this model
        if ($apiKey->keyable_type !== get_class($this) || $apiKey->keyable_id !== $this->getKey()) {
            throw new \InvalidArgumentException('API key does not belong to this entity');
        }

        $service = app(ApiKeyService::class);
        return $service->revokeApiKey($apiKey);
    }

    /**
     * Get API key usage analytics
     */
    public function getApiKeyAnalytics(int $days = 30): array
    {
        $service = app(ApiKeyService::class);
        $analytics = [];

        foreach ($this->activeApiKeys as $apiKey) {
            $analytics[$apiKey->id] = $service->getUsageAnalytics($apiKey, $days);
        }

        return $analytics;
    }

    /**
     * Check if this entity has any active API keys
     */
    public function hasActiveApiKeys(): bool
    {
        return $this->activeApiKeys()->exists();
    }

    /**
     * Get total API usage across all keys
     */
    public function getTotalApiUsage(int $days = 30): array
    {
        $usage = \DB::table('api_key_usage')
                   ->join('api_keys', 'api_key_usage.api_key_id', '=', 'api_keys.id')
                   ->where('api_keys.keyable_type', get_class($this))
                   ->where('api_keys.keyable_id', $this->getKey())
                   ->where('api_key_usage.requested_at', '>=', now()->subDays($days))
                   ->selectRaw('
                       COUNT(*) as total_requests,
                       COUNT(CASE WHEN response_code < 400 THEN 1 END) as successful_requests,
                       COUNT(CASE WHEN response_code >= 400 THEN 1 END) as failed_requests,
                       AVG(response_time) as avg_response_time,
                       COUNT(DISTINCT ip_address) as unique_ips
                   ')
                   ->first();

        return [
            'total_requests' => $usage->total_requests ?? 0,
            'successful_requests' => $usage->successful_requests ?? 0,
            'failed_requests' => $usage->failed_requests ?? 0,
            'success_rate' => $usage->total_requests > 0
                ? round(($usage->successful_requests / $usage->total_requests) * 100, 2)
                : 0,
            'avg_response_time' => round($usage->avg_response_time ?? 0, 3),
            'unique_ips' => $usage->unique_ips ?? 0,
        ];
    }

    /**
     * Get API keys that are close to their rate limit
     */
    public function getKeysNearRateLimit(float $threshold = 0.8)
    {
        return $this->activeApiKeys->filter(function ($apiKey) use ($threshold) {
            $usage = $apiKey->getRateLimitUsage();
            $limit = $apiKey->rate_limit;

            return $limit > 0 && ($usage / $limit) >= $threshold;
        });
    }

    /**
     * Disable all API keys for this entity
     */
    public function disableAllApiKeys(): int
    {
        return $this->apiKeys()->update(['is_active' => false]);
    }

    /**
     * Enable all API keys for this entity
     */
    public function enableAllApiKeys(): int
    {
        return $this->apiKeys()
                   ->where('is_active', false)
                   ->where(function ($query) {
                       $query->whereNull('expires_at')
                             ->orWhere('expires_at', '>', now());
                   })
                   ->update(['is_active' => true]);
    }
}