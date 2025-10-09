<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Notification Template Version Model
 *
 * Tracks version history of notification templates
 *
 * @property int $id
 * @property int $template_id
 * @property int $version_number
 * @property string $channel
 * @property string|null $subject
 * @property string $template_content
 * @property array|null $available_variables
 * @property bool $is_active
 * @property int|null $changed_by
 * @property string $change_type
 * @property string|null $change_notes
 * @property \Illuminate\Support\Carbon $changed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class NotificationTemplateVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'version_number',
        'channel',
        'subject',
        'template_content',
        'available_variables',
        'is_active',
        'changed_by',
        'change_type',
        'change_notes',
        'changed_at',
    ];

    protected $casts = [
        'available_variables' => 'array',
        'is_active' => 'boolean',
        'changed_at' => 'datetime',
    ];

    /**
     * Get the template this version belongs to
     */
    public function template()
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }

    /**
     * Get the user who made this change
     */
    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
