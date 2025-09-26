<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Customer;
use Carbon\Carbon;

/**
 * Customer-specific Trusted Device Model
 * Separate from admin trusted devices to prevent conflicts
 */
class CustomerTrustedDevice extends Model
{
    protected $table = 'trusted_devices';

    protected $fillable = [
        'authenticatable_type',
        'authenticatable_id',
        'device_id',
        'device_name',
        'device_type',
        'browser',
        'platform',
        'ip_address',
        'user_agent',
        'last_used_at',
        'trusted_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'trusted_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the authenticatable model (Customer only)
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to only customer records
     */
    public function scopeCustomersOnly($query)
    {
        return $query->where('authenticatable_type', Customer::class);
    }

    /**
     * Scope to only active devices
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('expires_at', '>', now());
    }

    /**
     * Check if device is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if device is active
     */
    public function isActive(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Mark device as used
     */
    public function markAsUsed(): void
    {
        $this->last_used_at = now();
        $this->save();
    }

    /**
     * Extend device trust duration
     */
    public function extendTrust(int $days = 30): void
    {
        $this->expires_at = now()->addDays($days);
        $this->markAsUsed();
    }

    /**
     * Revoke device trust
     */
    public function revoke(): void
    {
        $this->is_active = false;
        $this->save();
    }

    /**
     * Generate unique device ID from request
     */
    public static function generateDeviceId(string $userAgent, string $ipAddress): string
    {
        return hash('sha256', $userAgent . '|' . $ipAddress);
    }

    /**
     * Parse user agent for device information
     */
    public static function parseUserAgent(string $userAgent): array
    {
        // Simple user agent parsing - you can use a more sophisticated library
        $browser = 'Unknown';
        $platform = 'Unknown';
        $deviceType = 'Desktop';

        // Browser detection
        if (stripos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (stripos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (stripos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (stripos($userAgent, 'Edge') !== false) {
            $browser = 'Edge';
        }

        // Platform detection
        if (stripos($userAgent, 'Windows') !== false) {
            $platform = 'Windows';
        } elseif (stripos($userAgent, 'Mac') !== false) {
            $platform = 'macOS';
        } elseif (stripos($userAgent, 'Linux') !== false) {
            $platform = 'Linux';
        } elseif (stripos($userAgent, 'Android') !== false) {
            $platform = 'Android';
            $deviceType = 'Mobile';
        } elseif (stripos($userAgent, 'iOS') !== false || stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false) {
            $platform = 'iOS';
            $deviceType = stripos($userAgent, 'iPad') !== false ? 'Tablet' : 'Mobile';
        }

        // Mobile detection
        if (stripos($userAgent, 'Mobile') !== false && $deviceType === 'Desktop') {
            $deviceType = 'Mobile';
        }

        return [
            'browser' => $browser,
            'platform' => $platform,
            'device_type' => $deviceType,
        ];
    }

    /**
     * Create a friendly device name
     */
    public static function createDeviceName(array $deviceInfo): string
    {
        return $deviceInfo['browser'] . ' on ' . $deviceInfo['platform'];
    }
}