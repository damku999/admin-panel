<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\SecuritySetting
 *
 * @method static \Database\Factories\SecuritySettingFactory factory($count = null, $state = [])
 */
class SecuritySetting extends Model
{
    use HasFactory;
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
     * Get the settingable entity (User or Customer)
     */
    public function settingable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get default security settings
     */
    public static function getDefaults(): array
    {
        return [
            'two_factor_enabled' => false,
            'device_tracking_enabled' => true,
            'login_notifications' => true,
            'security_alerts' => true,
            'session_timeout' => 7200, // 2 hours in seconds
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
     * Get notification preference
     */
    public function getNotificationPreference(string $key, bool $default = false): bool
    {
        return $this->notification_preferences[$key] ?? $default;
    }

    /**
     * Update notification preference
     */
    public function updateNotificationPreference(string $key, bool $value): void
    {
        $preferences = $this->notification_preferences ?? [];
        $preferences[$key] = $value;
        $this->update(['notification_preferences' => $preferences]);
    }

    /**
     * Get session timeout in minutes
     */
    public function getSessionTimeoutMinutes(): int
    {
        return intval($this->session_timeout / 60);
    }

    /**
     * Set session timeout from minutes
     */
    public function setSessionTimeoutMinutes(int $minutes): void
    {
        $this->update(['session_timeout' => $minutes * 60]);
    }
}