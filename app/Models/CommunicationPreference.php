<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicationPreference extends Model
{
    use HasFactory;

    protected $table = 'communication_preferences';

    protected $fillable = [
        'user_id',
        'user_type',
        'email_notifications',
        'sms_notifications',
        'whatsapp_notifications',
        'push_notifications',
        'marketing_emails',
        'marketing_sms'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'whatsapp_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'marketing_emails' => 'boolean',
        'marketing_sms' => 'boolean'
    ];

    // Relationships
    public function user()
    {
        return $this->morphTo();
    }

    // For Admin Users
    public function adminUser()
    {
        return $this->belongsTo(User::class, 'user_id')->where('user_type', 'App\\Models\\User');
    }

    // For Customers
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id')->where('user_type', 'App\\Models\\Customer');
    }

    // Scopes
    public function scopeForUser($query, $userId, $userType)
    {
        return $query->where('user_id', $userId)->where('user_type', $userType);
    }

    public function scopeEmailEnabled($query)
    {
        return $query->where('email_notifications', true);
    }

    public function scopeSmsEnabled($query)
    {
        return $query->where('sms_notifications', true);
    }

    public function scopeWhatsAppEnabled($query)
    {
        return $query->where('whatsapp_notifications', true);
    }

    public function scopePushEnabled($query)
    {
        return $query->where('push_notifications', true);
    }

    public function scopeMarketingEmailEnabled($query)
    {
        return $query->where('marketing_emails', true);
    }

    public function scopeMarketingSmsEnabled($query)
    {
        return $query->where('marketing_sms', true);
    }

    // Accessors
    public function getHasAnyNotificationEnabledAttribute()
    {
        return $this->email_notifications ||
               $this->sms_notifications ||
               $this->whatsapp_notifications ||
               $this->push_notifications;
    }

    public function getHasMarketingEnabledAttribute()
    {
        return $this->marketing_emails || $this->marketing_sms;
    }

    public function getEnabledChannelsAttribute()
    {
        $channels = [];

        if ($this->email_notifications) $channels[] = 'email';
        if ($this->sms_notifications) $channels[] = 'sms';
        if ($this->whatsapp_notifications) $channels[] = 'whatsapp';
        if ($this->push_notifications) $channels[] = 'push';

        return $channels;
    }

    public function getMarketingChannelsAttribute()
    {
        $channels = [];

        if ($this->marketing_emails) $channels[] = 'email';
        if ($this->marketing_sms) $channels[] = 'sms';

        return $channels;
    }

    // Methods
    public function enableAll()
    {
        $this->update([
            'email_notifications' => true,
            'sms_notifications' => true,
            'whatsapp_notifications' => true,
            'push_notifications' => true
        ]);
    }

    public function disableAll()
    {
        $this->update([
            'email_notifications' => false,
            'sms_notifications' => false,
            'whatsapp_notifications' => false,
            'push_notifications' => false,
            'marketing_emails' => false,
            'marketing_sms' => false
        ]);
    }

    public function enableMarketing()
    {
        $this->update([
            'marketing_emails' => true,
            'marketing_sms' => true
        ]);
    }

    public function disableMarketing()
    {
        $this->update([
            'marketing_emails' => false,
            'marketing_sms' => false
        ]);
    }

    public function canReceive($channel)
    {
        $field = $channel . '_notifications';
        return $this->{$field} ?? false;
    }

    public function canReceiveMarketing($channel)
    {
        $field = 'marketing_' . $channel;
        return $this->{$field} ?? false;
    }

    // Static methods
    public static function getOrCreateForUser($userId, $userType)
    {
        return static::firstOrCreate(
            ['user_id' => $userId, 'user_type' => $userType],
            [
                'email_notifications' => true,
                'sms_notifications' => true,
                'whatsapp_notifications' => true,
                'push_notifications' => true,
                'marketing_emails' => false,
                'marketing_sms' => false
            ]
        );
    }

    // Validation rules
    public static function validationRules()
    {
        return [
            'user_id' => 'required|integer',
            'user_type' => 'required|string',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'whatsapp_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'marketing_emails' => 'boolean',
            'marketing_sms' => 'boolean'
        ];
    }
}