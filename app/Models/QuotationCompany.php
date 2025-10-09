<?php

namespace App\Models;

use App\Traits\TableRecordObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\QuotationCompany
 *
 * @property int $id
 * @property int $quotation_id
 * @property int $insurance_company_id
 * @property string $quote_number
 * @property string|null $policy_type
 * @property int|null $policy_tenure_years
 * @property string|null $idv_vehicle
 * @property string|null $idv_trailer
 * @property string|null $idv_cng_lpg_kit
 * @property string|null $idv_electrical_accessories
 * @property string|null $idv_non_electrical_accessories
 * @property string|null $total_idv
 * @property string|null $plan_name
 * @property string|null $basic_od_premium
 * @property string|null $tp_premium
 * @property string|null $ncb_percentage
 * @property string|null $cng_lpg_premium
 * @property string|null $total_od_premium
 * @property array|null $addon_covers_breakdown
 * @property string|null $total_addon_premium
 * @property string|null $net_premium
 * @property string|null $sgst_amount
 * @property string|null $cgst_amount
 * @property string|null $total_premium
 * @property string|null $roadside_assistance
 * @property string|null $final_premium
 * @property bool $is_recommended
 * @property string|null $recommendation_note
 * @property int|null $ranking
 * @property string|null $benefits
 * @property string|null $exclusions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\InsuranceCompany|null $insuranceCompany
 * @property-read \App\Models\Quotation|null $quotation
 * @method static \Database\Factories\QuotationCompanyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereAddonCoversBreakdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereBasicOdPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereBenefits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereCgstAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereCngLpgPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereExclusions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereFinalPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereIdvCngLpgKit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereIdvElectricalAccessories($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereIdvNonElectricalAccessories($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereIdvTrailer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereIdvVehicle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereInsuranceCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereIsRecommended($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereNcbPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereNetPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany wherePlanName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany wherePolicyTenureYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany wherePolicyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereQuotationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereQuoteNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereRanking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereRecommendationNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereRoadsideAssistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereSgstAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereTotalAddonPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereTotalIdv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereTotalOdPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereTotalPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereTpPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationCompany whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class QuotationCompany extends Model
{
    use HasFactory, LogsActivity, TableRecordObserver;

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
        if (! $compareWith) {
            return 0;
        }

        return $compareWith->final_premium - $this->final_premium;
    }

    public function getFormattedPremium(): string
    {
        return '₹ '.number_format($this->final_premium, 0);
    }
}
