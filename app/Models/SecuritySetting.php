<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\SecuritySetting
 *
 * @property int $id
 * @property string $settingable_type
 * @property int $settingable_id
 * @property bool $two_factor_enabled
 * @property bool $device_tracking_enabled
 * @property bool $login_notifications
 * @property bool $security_alerts
 * @property int $session_timeout
 * @property int $device_trust_duration
 * @property array|null $notification_preferences
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $settingable
 * @method static \Database\Factories\SecuritySettingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting whereDeviceTrackingEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting whereDeviceTrustDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting whereLoginNotifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting whereNotificationPreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting whereSecurityAlerts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting whereSessionTimeout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting whereSettingableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting whereSettingableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting whereTwoFactorEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecuritySetting whereUpdatedAt($value)
 * @mixin \Eloquent
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
