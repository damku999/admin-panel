<?php

namespace App\Models;

use App\Traits\TableRecordObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Claim extends Model
{
    use HasFactory, SoftDeletes, TableRecordObserver, LogsActivity;

    protected $fillable = [
        'customer_id',
        'customer_insurance_id', 
        'policy_no',
        'insurance_claim_number',
        'vehicle_number',
        'insurance_type',
        'liability_type',
        'current_stage',
        'claim_status',
        'incident_date',
        'claim_amount',
        'description',
        // Health Insurance fields
        'patient_name',
        'contact_number',
        'admission_date',
        'treating_doctor_name',
        'hospital_name',
        'hospital_address',
        'illness',
        'approx_hospitalization_days',
        'approx_cost',
        // Truck Insurance fields
        'driver_contact_number',
        'spot_location_address',
        'fir_required',
        'third_party_injury',
        'accident_description',
        // Common fields
        'remarks',
        'document_request_sent',
        'document_request_sent_at',
        'intimation_date',
        'closed_at',
        'closure_reason'
    ];

    protected $casts = [
        'admission_date' => 'date',
        'incident_date' => 'date',
        'fir_required' => 'boolean',
        'third_party_injury' => 'boolean',
        'document_request_sent' => 'boolean',
        'approx_cost' => 'decimal:2',
        'claim_amount' => 'decimal:2',
        'intimation_date' => 'datetime',
        'document_request_sent_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerInsurance(): BelongsTo
    {
        return $this->belongsTo(CustomerInsurance::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClaimDocument::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(ClaimStage::class)->orderBy('stage_order');
    }

    public function currentStageRecord(): HasOne
    {
        return $this->hasOne(ClaimStage::class)->where('is_current', true);
    }

    public function liability(): HasOne
    {
        return $this->hasOne(ClaimLiability::class);
    }

    // Scopes
    public function scopeHealthInsurance($query)
    {
        return $query->where('insurance_type', 'Health');
    }

    public function scopeTruckInsurance($query)
    {
        return $query->where('insurance_type', 'Truck');
    }

    public function scopeOpen($query)
    {
        return $query->where('claim_status', 'Open');
    }

    public function scopeClosed($query)
    {
        return $query->where('claim_status', 'Closed');
    }

    // Helper methods
    public function isHealthInsurance(): bool
    {
        return $this->insurance_type === 'Health';
    }

    public function isTruckInsurance(): bool
    {
        return $this->insurance_type === 'Truck';
    }

    public function isClosed(): bool
    {
        return $this->claim_status === 'Closed';
    }

    public function isOpen(): bool
    {
        return $this->claim_status === 'Open';
    }

    public function hasInsuranceClaimNumber(): bool
    {
        return !empty($this->insurance_claim_number);
    }

    public function getRequiredDocuments()
    {
        return $this->documents()->where('document_status', 'Required')->get();
    }

    public function getPendingDocuments()
    {
        return $this->documents()->whereIn('document_status', ['Required', 'Pending'])->get();
    }

    public function getReceivedDocuments()
    {
        return $this->documents()->where('document_status', 'Received')->get();
    }

    public function getDocumentCompletionPercentage(): int
    {
        $totalDocs = $this->documents()->where('is_mandatory', true)->count();
        if ($totalDocs === 0) return 100;
        
        $receivedDocs = $this->documents()->where('is_mandatory', true)->where('document_status', 'Received')->count();
        return (int) round(($receivedDocs / $totalDocs) * 100);
    }

    public function canBeClosed(): bool
    {
        return $this->claim_status !== 'Closed' && 
               $this->hasInsuranceClaimNumber() && 
               $this->getDocumentCompletionPercentage() >= 80; // At least 80% documents received
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
