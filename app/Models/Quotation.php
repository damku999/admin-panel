<?php

namespace App\Models;

use App\Traits\TableRecordObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Quotation extends Model
{
    use HasFactory, TableRecordObserver, LogsActivity, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'vehicle_number',
        'make_model_variant',
        'rto_location',
        'manufacturing_year',
        'date_of_registration',
        'cubic_capacity_kw',
        'seating_capacity',
        'fuel_type',
        'ncb_percentage',
        'idv_vehicle',
        'idv_trailer',
        'idv_cng_lpg_kit',
        'idv_electrical_accessories',
        'idv_non_electrical_accessories',
        'total_idv',
        'addon_covers',
        'policy_type',
        'policy_tenure_years',
        'status',
        'sent_at',
        'whatsapp_number',
        'notes',
    ];

    protected $casts = [
        'date_of_registration' => 'date',
        'ncb_percentage' => 'decimal:2',
        'idv_vehicle' => 'decimal:2',
        'idv_trailer' => 'decimal:2',
        'idv_cng_lpg_kit' => 'decimal:2',
        'idv_electrical_accessories' => 'decimal:2',
        'idv_non_electrical_accessories' => 'decimal:2',
        'total_idv' => 'decimal:2',
        'addon_covers' => 'array',
        'sent_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (Quotation $quotation) {
            // Delete all related quotation companies
            $quotation->quotationCompanies()->delete();
            
            // Clean up activity logs for this quotation and its companies
            \Spatie\Activitylog\Models\Activity::where('subject_type', Quotation::class)
                ->where('subject_id', $quotation->id)
                ->delete();
                
            \Spatie\Activitylog\Models\Activity::where('subject_type', \App\Models\QuotationCompany::class)
                ->whereIn('subject_id', $quotation->quotationCompanies()->pluck('id'))
                ->delete();
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function quotationCompanies(): HasMany
    {
        return $this->hasMany(QuotationCompany::class)->orderBy('ranking');
    }

    public function quotationStatus(): BelongsTo
    {
        return $this->belongsTo(QuotationStatus::class);
    }

    public function recommendedQuote(): ?QuotationCompany
    {
        return $this->quotationCompanies()->where('is_recommended', true)->first();
    }

    public function bestQuote(): ?QuotationCompany
    {
        return $this->quotationCompanies()->orderBy('final_premium')->first();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function isVehicleInsurance(): bool
    {
        return !empty($this->vehicle_number);
    }

    public function getQuoteReference(): string
    {
        return 'QT/' . date('y') . '/' . str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }
}
