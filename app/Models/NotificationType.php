<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\NotificationType
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NotificationTemplate> $templates
 * @property-read int|null $templates_count
 * @method static \Database\Factories\NotificationTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType query()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereDefaultEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereDefaultWhatsappEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereOrderNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType withoutTrashed()
 * @mixin \Eloquent
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
