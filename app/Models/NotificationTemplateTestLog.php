<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NotificationTemplateTestLog
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
 * @property-read \App\Models\User|null $sender
 * @property-read \App\Models\NotificationTemplate|null $template
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog whereMessageContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog whereRecipient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog whereResponseData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog whereSentBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTemplateTestLog whereUpdatedAt($value)
 * @mixin \Eloquent
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
