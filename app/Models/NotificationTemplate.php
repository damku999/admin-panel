<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Notification Template Model
 *
 * Manages WhatsApp and Email message templates for automated notifications
 *
 * @property int $id
 * @property int $notification_type_id
 * @property string $channel
 * @property string|null $subject
 * @property string $template_content
 * @property array|null $available_variables
 * @property string|null $sample_output
 * @property bool $is_active
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\NotificationType $notificationType
 * @property-read \App\Models\Admin|null $updater
 * @property-read string $name
 * @property-read string $type
 * @method static \Database\Factories\NotificationTemplateFactory factory($count = null, $state = [])
 */
class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_type_id',
        'channel',
        'subject',
        'template_content',
        'available_variables',
        'sample_output',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'available_variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the notification type for this template
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notificationType()
    {
        return $this->belongsTo(NotificationType::class);
    }

    /**
     * Render template with provided data
     *
     * @param array $data
     * @return string
     */
    public function render(array $data): string
    {
        $content = $this->template_content;

        foreach ($data as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }

        return $content;
    }

    /**
     * Get updater user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    /**
     * Get display name from notification type
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->notificationType->name ?? 'Unknown';
    }

    /**
     * Get type code from notification type
     *
     * @return string
     */
    public function getTypeAttribute(): string
    {
        return $this->notificationType->code ?? 'unknown';
    }

    /**
     * Get category from notification type
     *
     * @return string
     */
    public function getCategoryAttribute(): string
    {
        return $this->notificationType->category ?? 'unknown';
    }
}
