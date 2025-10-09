<?php

namespace App\Models\Customer;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Customer-specific Security Settings Model
 * Separate from admin security settings to prevent conflicts
 */
class CustomerSecuritySettings extends Model
{
    protected $table = 'security_settings';

    protected $fillable = [
        'settingable_type',
        'settingable_id',
        'two_factor_enabled',
        'device_tracking_enabled',
        'login_notifications',
        'security_alerts',
        'session_timeout',
        'device_trust_duration',
        'notification_preferences',
    ];

    protected $casts = [
        'two_factor_enabled' => 'boolean',
        'device_tracking_enabled' => 'boolean',
        'login_notifications' => 'boolean',
        'security_alerts' => 'boolean',
        'session_timeout' => 'integer',
        'device_trust_duration' => 'integer',
        'notification_preferences' => 'array',
    ];

    /**
     * Get the settingable model (Customer only)
     */
    public function settingable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to only customer records
     */
    public function scopeCustomersOnly($query)
    {
        return $query->where('settingable_type', Customer::class);
    }

    /**
     * Get default settings for customers
     */
    public static function getDefaults(): array
    {
        return [
            'two_factor_enabled' => false,
            'device_tracking_enabled' => true,
            'login_notifications' => true,
            'security_alerts' => true,
            'session_timeout' => 3600, // 1 hour
            'device_trust_duration' => 30, // 30 days
            'notification_preferences' => [
                'email_login_alerts' => true,
                'email_security_alerts' => true,
                'email_2fa_changes' => true,
                'sms_security_alerts' => false,
                'sms_login_alerts' => false,
            ],
        ];
    }

    /**
     * Update notification preference
     */
    public function updateNotificationPreference(string $key, bool $value): void
    {
        $preferences = $this->notification_preferences ?? [];
        $preferences[$key] = $value;
        $this->notification_preferences = $preferences;
        $this->save();
    }

    /**
     * Enable 2FA in settings
     */
    public function enableTwoFactor(): void
    {
        $this->two_factor_enabled = true;
        $this->save();
    }

    /**
     * Disable 2FA in settings
     */
    public function disableTwoFactor(): void
    {
        $this->two_factor_enabled = false;
        $this->save();
    }
}
