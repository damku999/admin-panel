<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Notification Type Model
 *
 * Defines types of notifications that can be sent
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $category
 * @property string|null $description
 * @property bool $default_whatsapp_enabled
 * @property bool $default_email_enabled
 * @property bool $is_active
 * @property int $order_no
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Database\Factories\NotificationTypeFactory factory($count = null, $state = [])
 */
class NotificationType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'category',
        'description',
        'default_whatsapp_enabled',
        'default_email_enabled',
        'is_active',
        'order_no',
    ];

    protected $casts = [
        'default_whatsapp_enabled' => 'boolean',
        'default_email_enabled' => 'boolean',
        'is_active' => 'boolean',
        'order_no' => 'integer',
    ];

    /**
     * Get templates for this notification type
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function templates()
    {
        return $this->hasMany(NotificationTemplate::class);
    }
}
