<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NotificationTemplate
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
 * @property-read string $category
 * @property-read string $name
 * @property-read string $type
 * @property-read \App\Models\NotificationType|null $notificationType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NotificationTemplateTestLog> $testLogs
 * @property-read int|null $test_logs_count
 * @property-read \App\Models\User|null $updater
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NotificationTemplateVersion> $versions
 * @property-read int|null $versions_count
 * @method static \Database\Factories\NotificationTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplate whereAvailableVariables($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplate whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplate whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplate whereNotificationTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplate whereSampleOutput($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplate whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplate whereTemplateContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplate whereUpdatedBy($value)
 * @mixin \Eloquent
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
     */
    public function render(array $data): string
    {
        $content = $this->template_content;

        foreach ($data as $key => $value) {
            $content = str_replace('{'.$key.'}', $value, $content);
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
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get version history for this template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function versions()
    {
        return $this->hasMany(NotificationTemplateVersion::class, 'template_id');
    }

    /**
     * Get test logs for this template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testLogs()
    {
        return $this->hasMany(NotificationTemplateTestLog::class, 'template_id');
    }

    /**
     * Get display name from notification type
     */
    public function getNameAttribute(): string
    {
        return $this->notificationType->name ?? 'Unknown';
    }

    /**
     * Get type code from notification type
     */
    public function getTypeAttribute(): string
    {
        return $this->notificationType->code ?? 'unknown';
    }

    /**
     * Get category from notification type
     */
    public function getCategoryAttribute(): string
    {
        return $this->notificationType->category ?? 'unknown';
    }
}
