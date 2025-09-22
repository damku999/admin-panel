<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class TrustedDevice extends Model
{
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
     * Get the authenticatable entity (User or Customer)
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Generate device fingerprint from request
     */
    public static function generateDeviceId(string $userAgent, string $ipAddress, ?string $additionalData = null): string
    {
        $fingerprint = $userAgent . $ipAddress . ($additionalData ?? '');
        return hash('sha256', $fingerprint);
    }

    /**
     * Create trusted device from request
     */
    public static function createFromRequest(
        $authenticatable,
        \Illuminate\Http\Request $request,
        string $deviceName = null
    ): self {
        $userAgent = $request->userAgent() ?? '';
        $ipAddress = $request->ip();
        $deviceId = self::generateDeviceId($userAgent, $ipAddress);

        // Check if device already exists for this user
        $existingDevice = self::where('authenticatable_type', get_class($authenticatable))
            ->where('authenticatable_id', $authenticatable->id)
            ->where('device_id', $deviceId)
            ->first();

        if ($existingDevice) {
            // If device exists but is inactive, reactivate it
            if (!$existingDevice->is_active) {
                $existingDevice->update([
                    'device_name' => $deviceName ?? $existingDevice->device_name,
                    'last_used_at' => now(),
                    'trusted_at' => now(),
                    'expires_at' => now()->addDays(config('security.device_trust_duration', 30)),
                    'is_active' => true,
                ]);
                return $existingDevice;
            }

            // If device is already active, update last used and return existing
            $existingDevice->update([
                'device_name' => $deviceName ?? $existingDevice->device_name,
                'last_used_at' => now(),
            ]);
            return $existingDevice;
        }

        // Parse user agent for device info
        $deviceInfo = self::parseUserAgent($userAgent);

        // Create new device
        return self::create([
            'authenticatable_type' => get_class($authenticatable),
            'authenticatable_id' => $authenticatable->id,
            'device_id' => $deviceId,
            'device_name' => $deviceName ?? $deviceInfo['device_name'],
            'device_type' => $deviceInfo['device_type'],
            'browser' => $deviceInfo['browser'],
            'platform' => $deviceInfo['platform'],
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'last_used_at' => now(),
            'trusted_at' => now(),
            'expires_at' => now()->addDays(config('security.device_trust_duration', 30)),
            'is_active' => true,
        ]);
    }

    /**
     * Parse user agent string for device information
     */
    protected static function parseUserAgent(string $userAgent): array
    {
        $deviceType = 'desktop';
        $browser = 'Unknown';
        $platform = 'Unknown';
        $deviceName = 'Unknown Device';

        // Detect mobile devices
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            $deviceType = 'mobile';
            if (strpos($userAgent, 'iPad') !== false) {
                $deviceType = 'tablet';
                $deviceName = 'iPad';
                $platform = 'iOS';
            } elseif (strpos($userAgent, 'iPhone') !== false) {
                $deviceName = 'iPhone';
                $platform = 'iOS';
            } elseif (strpos($userAgent, 'Android') !== false) {
                $platform = 'Android';
                $deviceName = 'Android Device';
            }
        }

        // Detect browser
        if (strpos($userAgent, 'Chrome') !== false && strpos($userAgent, 'Edg') === false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Edg') !== false) {
            $browser = 'Edge';
        }

        // Detect platform if not mobile
        if ($deviceType === 'desktop') {
            if (strpos($userAgent, 'Windows') !== false) {
                $platform = 'Windows';
                $deviceName = 'Windows PC';
            } elseif (strpos($userAgent, 'Mac') !== false) {
                $platform = 'macOS';
                $deviceName = 'Mac';
            } elseif (strpos($userAgent, 'Linux') !== false) {
                $platform = 'Linux';
                $deviceName = 'Linux PC';
            }
        }

        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'platform' => $platform,
            'device_name' => $deviceName,
        ];
    }

    /**
     * Check if device is still valid and trusted
     */
    public function isValid(): bool
    {
        return $this->is_active &&
               (!$this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Update last used timestamp
     */
    public function updateLastUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Revoke trust for this device
     */
    public function revoke(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Extend trust period
     */
    public function extendTrust(int $days = null): void
    {
        $days = $days ?? config('security.device_trust_duration', 30);
        $this->update(['expires_at' => now()->addDays($days)]);
    }

    /**
     * Get device display name with platform info
     */
    public function getDisplayName(): string
    {
        return $this->device_name .
               ($this->browser ? " ({$this->browser})" : '') .
               ($this->platform ? " - {$this->platform}" : '');
    }

    /**
     * Scope for active devices only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for valid (active and not expired) devices
     */
    public function scopeValid($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }
}