<?php

namespace App\Models;

use App\Traits\TableRecordObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ClaimStage extends Model
{
    use HasFactory, SoftDeletes, TableRecordObserver, LogsActivity;

    protected $fillable = [
        'claim_id',
        'stage_name',
        'stage_description',
        'notes',
        'stage_date',
        'is_current',
        'stage_order',
        'stage_status',
        'whatsapp_sent',
        'whatsapp_sent_at',
        'status'
    ];

    protected $casts = [
        'stage_date' => 'datetime',
        'is_current' => 'boolean',
        'whatsapp_sent' => 'boolean',
        'whatsapp_sent_at' => 'datetime',
        'status' => 'boolean',
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    // Relationships
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    // Scopes
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('stage_status', 'Completed');
    }

    public function scopePending($query)
    {
        return $query->where('stage_status', 'Pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('stage_status', 'In Progress');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('stage_order');
    }

    public function scopeChronological($query)
    {
        return $query->orderBy('stage_date');
    }

    public function scopeWhatsAppSent($query)
    {
        return $query->where('whatsapp_sent', true);
    }

    public function scopeWhatsAppPending($query)
    {
        return $query->where('whatsapp_sent', false);
    }

    // Helper methods
    public function isCurrent(): bool
    {
        return $this->is_current === true;
    }

    public function isCompleted(): bool
    {
        return $this->stage_status === 'Completed';
    }

    public function isPending(): bool
    {
        return $this->stage_status === 'Pending';
    }

    public function isInProgress(): bool
    {
        return $this->stage_status === 'In Progress';
    }

    public function isOnHold(): bool
    {
        return $this->stage_status === 'On Hold';
    }

    public function isCancelled(): bool
    {
        return $this->stage_status === 'Cancelled';
    }

    public function hasWhatsAppSent(): bool
    {
        return $this->whatsapp_sent === true;
    }

    public function markAsCurrent(): bool
    {
        // First, unset any existing current stage for this claim
        static::where('claim_id', $this->claim_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        // Set this stage as current
        $this->is_current = true;
        $this->stage_status = 'In Progress';
        
        // Update the claim's current stage
        $this->claim->update(['current_stage' => $this->stage_name]);
        
        return $this->save();
    }

    public function markAsCompleted(): bool
    {
        $this->stage_status = 'Completed';
        $this->is_current = false;
        
        return $this->save();
    }

    public function markAsOnHold(): bool
    {
        $this->stage_status = 'On Hold';
        
        return $this->save();
    }

    public function markAsCancelled(): bool
    {
        $this->stage_status = 'Cancelled';
        $this->is_current = false;
        
        return $this->save();
    }

    public function markWhatsAppSent(): bool
    {
        $this->whatsapp_sent = true;
        $this->whatsapp_sent_at = now();
        
        return $this->save();
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->stage_status) {
            'Completed' => 'badge-success',
            'In Progress' => 'badge-primary',
            'Pending' => 'badge-warning',
            'On Hold' => 'badge-secondary',
            'Cancelled' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    public function getStatusIcon(): string
    {
        return match($this->stage_status) {
            'Completed' => 'fa-check-circle',
            'In Progress' => 'fa-spinner',
            'Pending' => 'fa-clock',
            'On Hold' => 'fa-pause-circle',
            'Cancelled' => 'fa-times-circle',
            default => 'fa-circle'
        };
    }

    // Static methods for creating standard stages
    public static function getStandardStages(): array
    {
        return [
            ['stage_name' => 'Claim Initiated', 'stage_description' => 'Initial claim intimation received', 'stage_order' => 1],
            ['stage_name' => 'Document List Sent', 'stage_description' => 'Required document list sent to customer', 'stage_order' => 2],
            ['stage_name' => 'Documents Collection', 'stage_description' => 'Collecting required documents from customer', 'stage_order' => 3],
            ['stage_name' => 'Claim Number Assigned', 'stage_description' => 'Insurance company claim number assigned', 'stage_order' => 4],
            ['stage_name' => 'Under Review', 'stage_description' => 'Claim under insurance company review', 'stage_order' => 5],
            ['stage_name' => 'Survey Scheduled', 'stage_description' => 'Survey scheduled by insurance company', 'stage_order' => 6],
            ['stage_name' => 'Approved', 'stage_description' => 'Claim approved by insurance company', 'stage_order' => 7],
            ['stage_name' => 'Settlement', 'stage_description' => 'Claim settlement in progress', 'stage_order' => 8],
            ['stage_name' => 'Completed', 'stage_description' => 'Claim process completed', 'stage_order' => 9],
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
