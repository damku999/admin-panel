<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryStatus extends Model
{
    use HasFactory;

    protected $table = 'delivery_status';

    protected $fillable = [
        'message_id',
        'external_id',
        'status',
        'timestamp',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'timestamp' => 'datetime',
        'message_id' => 'integer'
    ];

    // Relationships
    public function messageQueue()
    {
        return $this->belongsTo(MessageQueue::class, 'message_id');
    }

    // Scopes
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeBounced($query)
    {
        return $query->where('status', 'bounced');
    }

    public function scopeComplained($query)
    {
        return $query->where('status', 'complained');
    }

    public function scopeOpened($query)
    {
        return $query->where('status', 'opened');
    }

    public function scopeClicked($query)
    {
        return $query->where('status', 'clicked');
    }

    // Accessors
    public function getIsDeliveredAttribute()
    {
        return $this->status === 'delivered';
    }

    public function getIsBouncedAttribute()
    {
        return $this->status === 'bounced';
    }

    public function getIsComplainedAttribute()
    {
        return $this->status === 'complained';
    }

    public function getIsOpenedAttribute()
    {
        return $this->status === 'opened';
    }

    public function getIsClickedAttribute()
    {
        return $this->status === 'clicked';
    }

    public function getIsEngagedAttribute()
    {
        return in_array($this->status, ['opened', 'clicked']);
    }

    public function getIsNegativeAttribute()
    {
        return in_array($this->status, ['bounced', 'complained']);
    }
}