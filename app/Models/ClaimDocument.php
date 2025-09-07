<?php

namespace App\Models;

use App\Traits\TableRecordObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ClaimDocument extends Model
{
    use HasFactory, SoftDeletes, TableRecordObserver, LogsActivity;

    protected $fillable = [
        'claim_id',
        'document_name',
        'document_description',
        'document_status',
        'document_path',
        'received_at',
        'notes',
        'is_mandatory',
        'insurance_type',
        'order_no',
        'status'
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'is_mandatory' => 'boolean',
        'status' => 'boolean',
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    // Relationships
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    // Accessors
    protected function documentPath(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? asset('storage/' . $value) : null,
        );
    }

    // Scopes
    public function scopeRequired($query)
    {
        return $query->where('document_status', 'Required');
    }

    public function scopeReceived($query)
    {
        return $query->where('document_status', 'Received');
    }

    public function scopePending($query)
    {
        return $query->whereIn('document_status', ['Required', 'Pending']);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeOptional($query)
    {
        return $query->where('is_mandatory', false);
    }

    public function scopeHealthInsurance($query)
    {
        return $query->where('insurance_type', 'Health');
    }

    public function scopeTruckInsurance($query)
    {
        return $query->where('insurance_type', 'Truck');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_no');
    }

    // Helper methods
    public function isRequired(): bool
    {
        return $this->document_status === 'Required';
    }

    public function isReceived(): bool
    {
        return $this->document_status === 'Received';
    }

    public function isPending(): bool
    {
        return in_array($this->document_status, ['Required', 'Pending']);
    }

    public function isMandatory(): bool
    {
        return $this->is_mandatory === true;
    }

    public function hasDocument(): bool
    {
        return !empty($this->document_path);
    }

    public function markAsReceived(): bool
    {
        $this->document_status = 'Received';
        $this->received_at = now();
        
        return $this->save();
    }

    public function markAsPending(): bool
    {
        $this->document_status = 'Pending';
        
        return $this->save();
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->document_status) {
            'Received' => 'badge-success',
            'Pending' => 'badge-warning',
            'Required' => 'badge-danger',
            'Not Applicable' => 'badge-secondary',
            default => 'badge-secondary'
        };
    }

    // Static methods for creating standard document templates
    public static function getHealthInsuranceDocuments(): array
    {
        return [
            ['document_name' => 'Patient Name', 'is_mandatory' => true, 'order_no' => 1],
            ['document_name' => 'Policy No', 'is_mandatory' => true, 'order_no' => 2],
            ['document_name' => 'Contact No', 'is_mandatory' => true, 'order_no' => 3],
            ['document_name' => 'Date of Admission', 'is_mandatory' => true, 'order_no' => 4],
            ['document_name' => 'Treating Doctor Name', 'is_mandatory' => true, 'order_no' => 5],
            ['document_name' => 'Hospital Name', 'is_mandatory' => true, 'order_no' => 6],
            ['document_name' => 'Address of Hospital', 'is_mandatory' => true, 'order_no' => 7],
            ['document_name' => 'Illness', 'is_mandatory' => true, 'order_no' => 8],
            ['document_name' => 'Approx Hospitalisation Days', 'is_mandatory' => true, 'order_no' => 9],
            ['document_name' => 'Approx Cost', 'is_mandatory' => true, 'order_no' => 10],
        ];
    }

    public static function getTruckInsuranceDocuments(): array
    {
        return [
            ['document_name' => 'Claim form duly Signed', 'is_mandatory' => true, 'order_no' => 1],
            ['document_name' => 'Policy Copy', 'is_mandatory' => true, 'order_no' => 2],
            ['document_name' => 'RC Copy', 'is_mandatory' => true, 'order_no' => 3],
            ['document_name' => 'Driving License', 'is_mandatory' => true, 'order_no' => 4],
            ['document_name' => 'Driver Contact Number', 'is_mandatory' => true, 'order_no' => 5],
            ['document_name' => 'Spot Location Address', 'is_mandatory' => true, 'order_no' => 6],
            ['document_name' => 'Fitness Certificate', 'is_mandatory' => true, 'order_no' => 7],
            ['document_name' => 'Permit', 'is_mandatory' => true, 'order_no' => 8],
            ['document_name' => 'Road tax', 'is_mandatory' => true, 'order_no' => 9],
            ['document_name' => 'Cancel Cheque', 'is_mandatory' => true, 'order_no' => 10],
            ['document_name' => 'Fast tag statement', 'is_mandatory' => true, 'order_no' => 11],
            ['document_name' => 'CKYC Form', 'is_mandatory' => true, 'order_no' => 12],
            ['document_name' => 'Insured Pan and Address Proof', 'is_mandatory' => true, 'order_no' => 13],
            ['document_name' => 'Load Challan', 'is_mandatory' => true, 'order_no' => 14],
            ['document_name' => 'All side spot Photos with driver selfie', 'is_mandatory' => true, 'order_no' => 15],
            ['document_name' => 'Towing Bill', 'is_mandatory' => true, 'order_no' => 16],
            ['document_name' => 'Workshop Estimate', 'is_mandatory' => true, 'order_no' => 17],
            ['document_name' => 'FIR - Yes or No', 'is_mandatory' => true, 'order_no' => 18],
            ['document_name' => 'Third Party Injury - Yes or No', 'is_mandatory' => true, 'order_no' => 19],
            ['document_name' => 'How accident Happened?', 'is_mandatory' => true, 'order_no' => 20],
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
