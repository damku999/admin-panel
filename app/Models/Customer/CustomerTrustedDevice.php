<?php

namespace App\Models\Customer;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Customer\CustomerTrustedDevice
 *
 * @property int $id
 * @property string $authenticatable_type
 * @property int $authenticatable_id
 * @property string $device_id
 * @property string $device_name
 * @property string|null $device_type
 * @property string|null $browser
 * @property string|null $platform
 * @property string $ip_address
 * @property string $user_agent
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon $trusted_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $authenticatable
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice active()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice customersOnly()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereAuthenticatableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereAuthenticatableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereDeviceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereTrustedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerTrustedDevice whereUserAgent($value)
 * @mixin \Eloquent
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
        return $this->is_active && ! $this->isExpired();
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
        return hash('sha256', $userAgent.'|'.$ipAddress);
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
        return $deviceInfo['browser'].' on '.$deviceInfo['platform'];
    }
}
