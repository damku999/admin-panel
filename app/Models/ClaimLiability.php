<?php

namespace App\Models;

use App\Traits\TableRecordObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ClaimLiability extends Model
{
    use HasFactory, SoftDeletes, TableRecordObserver, LogsActivity;

    protected $fillable = [
        'claim_id',
        'liability_type',
        'claim_amount',
        'salvage_amount',
        'claim_charge',
        'amount_to_be_paid_by_customer',
        'deductions',
        'claim_amount_received',
        'payment_method',
        'payment_reference_number',
        'payment_date',
        'payment_notes',
        'payment_status',
        'remarks',
        'is_final',
        'status'
    ];

    protected $casts = [
        'claim_amount' => 'decimal:2',
        'salvage_amount' => 'decimal:2',
        'claim_charge' => 'decimal:2',
        'amount_to_be_paid_by_customer' => 'decimal:2',
        'deductions' => 'decimal:2',
        'claim_amount_received' => 'decimal:2',
        'payment_date' => 'date',
        'is_final' => 'boolean',
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
    public function scopeCashless($query)
    {
        return $query->where('liability_type', 'Cashless');
    }

    public function scopeReimbursement($query)
    {
        return $query->where('liability_type', 'Reimbursement');
    }

    public function scopeFinal($query)
    {
        return $query->where('is_final', true);
    }

    public function scopeDraft($query)
    {
        return $query->where('is_final', false);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'Pending');
    }

    public function scopeProcessed($query)
    {
        return $query->where('payment_status', 'Processed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'Completed');
    }

    // Helper methods
    public function isCashless(): bool
    {
        return $this->liability_type === 'Cashless';
    }

    public function isReimbursement(): bool
    {
        return $this->liability_type === 'Reimbursement';
    }

    public function isFinal(): bool
    {
        return $this->is_final === true;
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'Pending';
    }

    public function isProcessed(): bool
    {
        return $this->payment_status === 'Processed';
    }

    public function isCompleted(): bool
    {
        return $this->payment_status === 'Completed';
    }

    public function isFailed(): bool
    {
        return $this->payment_status === 'Failed';
    }

    // Calculation methods
    public function calculateAmountToBePaidByCustomer(): float
    {
        if (!$this->isCashless()) {
            return 0;
        }

        $claimAmount = (float) ($this->claim_amount ?? 0);
        $salvageAmount = (float) ($this->salvage_amount ?? 0);
        $claimCharge = (float) ($this->claim_charge ?? 0);

        return max(0, $claimAmount - $salvageAmount - $claimCharge);
    }

    public function calculateClaimAmountReceived(): float
    {
        if (!$this->isReimbursement()) {
            return 0;
        }

        $claimAmount = (float) ($this->claim_amount ?? 0);
        $salvageAmount = (float) ($this->salvage_amount ?? 0);
        $deductions = (float) ($this->deductions ?? 0);

        return max(0, $claimAmount - $salvageAmount - $deductions);
    }

    public function updateCalculatedFields(): bool
    {
        if ($this->isCashless()) {
            $this->amount_to_be_paid_by_customer = $this->calculateAmountToBePaidByCustomer();
        } elseif ($this->isReimbursement()) {
            $this->claim_amount_received = $this->calculateClaimAmountReceived();
        }

        return $this->save();
    }

    public function markAsProcessed(): bool
    {
        $this->payment_status = 'Processed';
        
        return $this->save();
    }

    public function markAsCompleted(): bool
    {
        $this->payment_status = 'Completed';
        $this->is_final = true;
        
        return $this->save();
    }

    public function markAsFailed(): bool
    {
        $this->payment_status = 'Failed';
        
        return $this->save();
    }

    public function getPaymentStatusBadgeClass(): string
    {
        return match($this->payment_status) {
            'Completed' => 'badge-success',
            'Processed' => 'badge-primary',
            'Pending' => 'badge-warning',
            'Failed' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    public function getPaymentStatusIcon(): string
    {
        return match($this->payment_status) {
            'Completed' => 'fa-check-circle',
            'Processed' => 'fa-spinner',
            'Pending' => 'fa-clock',
            'Failed' => 'fa-times-circle',
            default => 'fa-circle'
        };
    }

    public function getNetAmount(): float
    {
        if ($this->isCashless()) {
            return $this->calculateAmountToBePaidByCustomer();
        } elseif ($this->isReimbursement()) {
            return $this->calculateClaimAmountReceived();
        }

        return 0;
    }

    public function getFormattedNetAmount(): string
    {
        return number_format($this->getNetAmount(), 2);
    }

    public function getSummary(): array
    {
        $summary = [
            'liability_type' => $this->liability_type,
            'claim_amount' => $this->claim_amount,
            'salvage_amount' => $this->salvage_amount,
            'net_amount' => $this->getNetAmount(),
            'payment_status' => $this->payment_status,
            'is_final' => $this->is_final,
        ];

        if ($this->isCashless()) {
            $summary['claim_charge'] = $this->claim_charge;
            $summary['amount_to_be_paid_by_customer'] = $this->amount_to_be_paid_by_customer;
        } elseif ($this->isReimbursement()) {
            $summary['deductions'] = $this->deductions;
            $summary['claim_amount_received'] = $this->claim_amount_received;
        }

        return $summary;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
