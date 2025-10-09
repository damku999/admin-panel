<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Notification Log Model
 *
 * Tracks all notifications sent through the system
 *
 * @property int $id
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property int|null $notification_type_id
 * @property int|null $template_id
 * @property string $channel
 * @property string $recipient
 * @property string|null $subject
 * @property string $message_content
 * @property array|null $variables_used
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property string|null $error_message
 * @property array|null $api_response
 * @property int|null $sent_by
 * @property int $retry_count
 * @property \Illuminate\Support\Carbon|null $next_retry_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class NotificationLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'notification_type_id',
        'template_id',
        'channel',
        'recipient',
        'subject',
        'message_content',
        'variables_used',
        'status',
        'sent_at',
        'delivered_at',
        'read_at',
        'error_message',
        'api_response',
        'sent_by',
        'retry_count',
        'next_retry_at',
    ];

    protected $casts = [
        'variables_used' => 'array',
        'api_response' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'next_retry_at' => 'datetime',
        'retry_count' => 'integer',
    ];

    /**
     * Get the notifiable entity (polymorphic relation)
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Get the notification type
     */
    public function notificationType()
    {
        return $this->belongsTo(NotificationType::class);
    }

    /**
     * Get the template used
     */
    public function template()
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }

    /**
     * Get the user who sent this notification
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Get delivery tracking records
     */
    public function deliveryTracking()
    {
        return $this->hasMany(NotificationDeliveryTracking::class);
    }

    /**
     * Scope for pending notifications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for sent notifications
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for failed notifications
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for delivered notifications
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope for a specific channel
     */
    public function scopeChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope for notifications ready to retry
     */
    public function scopeReadyToRetry($query)
    {
        return $query->where('status', 'failed')
            ->where('retry_count', '<', 3)
            ->where(function ($q) {
                $q->whereNull('next_retry_at')
                    ->orWhere('next_retry_at', '<=', now());
            });
    }

    /**
     * Check if notification was successful
     */
    public function isSuccessful(): bool
    {
        return in_array($this->status, ['sent', 'delivered', 'read']);
    }

    /**
     * Check if notification failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if notification can be retried
     */
    public function canRetry(): bool
    {
        return $this->isFailed() && $this->retry_count < 3;
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'sent' => 'info',
            'delivered' => 'success',
            'read' => 'primary',
            'failed' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get channel icon for UI
     */
    public function getChannelIconAttribute(): string
    {
        return match ($this->channel) {
            'whatsapp' => 'fab fa-whatsapp',
            'email' => 'fas fa-envelope',
            'sms' => 'fas fa-sms',
            default => 'fas fa-bell',
        };
    }
}
