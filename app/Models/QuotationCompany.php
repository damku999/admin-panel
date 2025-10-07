<?php

namespace App\Models;

use App\Traits\TableRecordObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class QuotationCompany extends Model
{
    use HasFactory, TableRecordObserver, LogsActivity;

    protected $fillable = [
        'quotation_id',
        'insurance_company_id',
        'quote_number',
        'policy_type',
        'policy_tenure_years',
        'idv_vehicle',
        'idv_trailer',
        'idv_cng_lpg_kit',
        'idv_electrical_accessories',
        'idv_non_electrical_accessories',
        'total_idv',
        'plan_name',
        'basic_od_premium',
        'tp_premium',
        'ncb_percentage',
        'cng_lpg_premium',
        'total_od_premium',
        'addon_covers_breakdown',
        'total_addon_premium',
        'net_premium',
        'sgst_amount',
        'cgst_amount',
        'total_premium',
        'roadside_assistance',
        'final_premium',
        'is_recommended',
        'recommendation_note',
        'ranking',
        'benefits',
        'exclusions',
    ];

    protected $casts = [
        'policy_tenure_years' => 'integer',
        'idv_vehicle' => 'decimal:2',
        'idv_trailer' => 'decimal:2',
        'idv_cng_lpg_kit' => 'decimal:2',
        'idv_electrical_accessories' => 'decimal:2',
        'idv_non_electrical_accessories' => 'decimal:2',
        'total_idv' => 'decimal:2',
        'basic_od_premium' => 'decimal:2',
        'tp_premium' => 'decimal:2',
        'ncb_percentage' => 'decimal:2',
        'cng_lpg_premium' => 'decimal:2',
        'total_od_premium' => 'decimal:2',
        'addon_covers_breakdown' => 'array',
        'total_addon_premium' => 'decimal:2',
        'net_premium' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'total_premium' => 'decimal:2',
        'roadside_assistance' => 'decimal:2',
        'final_premium' => 'decimal:2',
        'is_recommended' => 'boolean',
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function insuranceCompany(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function calculateSavings(?QuotationCompany $compareWith = null): float
    {
        if (!$compareWith) {
            return 0;
        }
        
        return $compareWith->final_premium - $this->final_premium;
    }

    public function getFormattedPremium(): string
    {
        return '₹ ' . number_format($this->final_premium, 0);
    }
}
