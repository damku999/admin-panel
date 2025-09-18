<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageQueue extends Model
{
    use HasFactory;

    protected $table = 'message_queue';

    protected $fillable = [
        'recipient_type',
        'recipient',
        'subject',
        'message',
        'status',
        'priority',
        'scheduled_at',
        'sent_at',
        'attempts',
        'max_attempts',
        'error_message',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'attempts' => 'integer',
        'max_attempts' => 'integer',
        'priority' => 'integer'
    ];

    // Relationships
    public function deliveryStatuses()
    {
        return $this->hasMany(DeliveryStatus::class, 'message_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', '<=', 3);
    }

    // Mutators & Accessors
    public function getIsFailedAttribute()
    {
        return $this->status === 'failed';
    }

    public function getIsSentAttribute()
    {
        return $this->status === 'sent';
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getCanRetryAttribute()
    {
        return $this->status === 'failed' && $this->attempts < $this->max_attempts;
    }

    // Methods
    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'attempts' => $this->attempts + 1,
            'error_message' => $errorMessage
        ]);
    }

    public function incrementAttempts()
    {
        $this->increment('attempts');
    }
}