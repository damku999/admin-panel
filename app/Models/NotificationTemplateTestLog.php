<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Notification Template Test Log Model
 *
 * Tracks test sends of notification templates
 *
 * @property int $id
 * @property int|null $template_id
 * @property string $channel
 * @property string $recipient
 * @property string|null $subject
 * @property string $message_content
 * @property string $status
 * @property string|null $error_message
 * @property array|null $response_data
 * @property int|null $sent_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class NotificationTemplateTestLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'channel',
        'recipient',
        'subject',
        'message_content',
        'status',
        'error_message',
        'response_data',
        'sent_by',
    ];

    protected $casts = [
        'response_data' => 'array',
    ];

    /**
     * Get the template this test log belongs to
     */
    public function template()
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }

    /**
     * Get the user who sent this test
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
