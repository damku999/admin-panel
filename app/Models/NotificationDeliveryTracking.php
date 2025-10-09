<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Notification Delivery Tracking Model
 *
 * Tracks status updates for notification deliveries
 *
 * @property int $id
 * @property int $notification_log_id
 * @property string $status
 * @property \Illuminate\Support\Carbon $tracked_at
 * @property array|null $provider_status
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class NotificationDeliveryTracking extends Model
{
    use HasFactory;

    protected $table = 'notification_delivery_tracking';

    protected $fillable = [
        'notification_log_id',
        'status',
        'tracked_at',
        'provider_status',
        'metadata',
    ];

    protected $casts = [
        'tracked_at' => 'datetime',
        'provider_status' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the notification log this tracking belongs to
     */
    public function notificationLog()
    {
        return $this->belongsTo(NotificationLog::class);
    }
}
