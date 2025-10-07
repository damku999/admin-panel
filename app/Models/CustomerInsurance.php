<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Broker;
use App\Models\Customer;
use App\Models\Claim;
use App\Models\FuelType;
use App\Models\PolicyType;
use App\Models\PremiumType;
use App\Models\InsuranceCompany;
use Spatie\Activitylog\LogOptions;
use App\Models\RelationshipManager;
use App\Traits\TableRecordObserver;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CustomerInsurance
 *
 * @property int $id
 * @property string|null $issue_date
 * @property int|null $branch_id
 * @property int|null $broker_id
 * @property int|null $relationship_manager_id
 * @property int|null $customer_id
 * @property int|null $insurance_company_id
 * @property int|null $premium_type_id
 * @property int|null $policy_type_id
 * @property int|null $fuel_type_id
 * @property string|null $policy_no
 * @property string|null $registration_no
 * @property string|null $rto
 * @property string|null $make_model
 * @property string|null $commission_on
 * @property string|null $start_date
 * @property string|null $expired_date
 * @property string|null $tp_expiry_date
 * @property string|null $maturity_date
 * @property float|null $od_premium
 * @property float|null $tp_premium
 * @property float|null $net_premium
 * @property float|null $premium_amount
 * @property float|null $gst
 * @property float|null $final_premium_with_gst
 * @property float|null $sgst1
 * @property float|null $cgst1
 * @property float|null $cgst2
 * @property float|null $sgst2
 * @property float|null $my_commission_percentage
 * @property float|null $my_commission_amount
 * @property float|null $transfer_commission_percentage
 * @property float|null $transfer_commission_amount
 * @property float|null $reference_commission_percentage
 * @property float|null $reference_commission_amount
 * @property float|null $actual_earnings
 * @property float|null $ncb_percentage
 * @property string|null $mode_of_payment
 * @property string|null $cheque_no
 * @property string|null $insurance_status
 * @property string|null $policy_document_path
 * @property string|null $gross_vehicle_weight
 * @property string|null $mfg_year
 * @property int|null $reference_by
 * @property string|null $plan_name
 * @property string|null $premium_paying_term
 * @property string|null $policy_term
 * @property string|null $sum_insured
 * @property string|null $pension_amount_yearly
 * @property string|null $approx_maturity_amount
 * @property string|null $life_insurance_payment_mode
 * @property string|null $remarks
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read Branch|null $branch
 * @property-read Broker|null $broker
 * @property-read Customer|null $customer
 * @property-read FuelType|null $fuelType
 * @property-read InsuranceCompany|null $insuranceCompany
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read PolicyType|null $policyType
 * @property-read PremiumType|null $premiumType
 * @property-read RelationshipManager|null $relationshipManager
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereActualEarnings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereApproxMaturityAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereBrokerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereCgst1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereCgst2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereChequeNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereCommissionOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereExpiredDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereFinalPremiumWithGst($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereFuelTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereGrossVehicleWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereGst($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereInsuranceCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereInsuranceStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereLifeInsurancePaymentMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereMakeModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereMaturityDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereMfgYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereModeOfPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereMyCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereMyCommissionPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereNcbPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereNetPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereOdPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance wherePensionAmountYearly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance wherePlanName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance wherePolicyDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance wherePolicyNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance wherePolicyTerm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance wherePolicyTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance wherePremiumAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance wherePremiumPayingTerm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance wherePremiumTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereReferenceBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereReferenceCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereReferenceCommissionPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereRegistrationNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereRelationshipManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereRto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereSgst1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereSgst2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereSumInsured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereTpExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereTpPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereTransferCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereTransferCommissionPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance withoutTrashed()
 * @mixin \Eloquent
 */
class CustomerInsurance extends Model
{
    use HasFactory, SoftDeletes, TableRecordObserver, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'issue_date',
        'branch_id',
        'broker_id',
        'relationship_manager_id',
        'customer_id',
        'insurance_company_id',
        'premium_type_id',
        'policy_type_id',
        'fuel_type_id',
        'policy_no',
        'registration_no',
        'rto',
        'make_model',
        'commission_on',
        'start_date',
        'expired_date',
        'tp_expiry_date',
        'maturity_date',
        'od_premium',
        'tp_premium',
        'net_premium',
        'premium_amount',
        'gst',
        'final_premium_with_gst',
        'sgst1',
        'cgst1',
        'cgst2',
        'sgst2',
        'my_commission_percentage',
        'my_commission_amount',
        'transfer_commission_percentage',
        'transfer_commission_amount',
        'reference_commission_percentage',
        'reference_commission_amount',
        'actual_earnings',
        'ncb_percentage',
        'mode_of_payment',
        'cheque_no',
        'insurance_status',
        'policy_document_path',
        'gross_vehicle_weight',
        'mfg_year',
        'reference_by',
        'plan_name',
        'premium_paying_term',
        'policy_term',
        'sum_insured',
        'pension_amount_yearly',
        'approx_maturity_amount',
        'life_insurance_payment_mode',
        'remarks',
        'status',
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    // Define the relationships here
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class, 'broker_id');
    }

    public function relationshipManager()
    {
        return $this->belongsTo(RelationshipManager::class, 'relationship_manager_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function insuranceCompany()
    {
        return $this->belongsTo(InsuranceCompany::class, 'insurance_company_id');
    }

    public function premiumType()
    {
        return $this->belongsTo(PremiumType::class, 'premium_type_id');
    }

    public function policyType()
    {
        return $this->belongsTo(PolicyType::class, 'policy_type_id');
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_id');
    }

    public function commissionType()
    {
        return $this->belongsTo(CommissionType::class, 'commission_type_id');
    }

    /**
     * Get all claims for this insurance policy.
     */
    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    // =======================================================
    // DATE FORMATTING ACCESSORS & MUTATORS
    // =======================================================

    /**
     * Get issue date in UI format (d/m/Y)
     */
    public function getIssueDateFormattedAttribute()
    {
        return formatDateForUi($this->issue_date);
    }

    /**
     * Set issue date from UI format (d/m/Y) to database format (Y-m-d)
     */
    public function setIssueDateAttribute($value)
    {
        $this->attributes['issue_date'] = formatDateForDatabase($value);
    }

    /**
     * Get start date in UI format (d/m/Y)
     */
    public function getStartDateFormattedAttribute()
    {
        return formatDateForUi($this->start_date);
    }

    /**
     * Set start date from UI format (d/m/Y) to database format (Y-m-d)
     */
    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = formatDateForDatabase($value);
    }

    /**
     * Get expired date in UI format (d/m/Y)
     */
    public function getExpiredDateFormattedAttribute()
    {
        return formatDateForUi($this->expired_date);
    }

    /**
     * Set expired date from UI format (d/m/Y) to database format (Y-m-d)
     */
    public function setExpiredDateAttribute($value)
    {
        $this->attributes['expired_date'] = formatDateForDatabase($value);
    }

    /**
     * Get TP expiry date in UI format (d/m/Y)
     */
    public function getTpExpiryDateFormattedAttribute()
    {
        return formatDateForUi($this->tp_expiry_date);
    }

    /**
     * Set TP expiry date from UI format (d/m/Y) to database format (Y-m-d)
     */
    public function setTpExpiryDateAttribute($value)
    {
        $this->attributes['tp_expiry_date'] = formatDateForDatabase($value);
    }

    /**
     * Get maturity date in UI format (d/m/Y)
     */
    public function getMaturityDateFormattedAttribute()
    {
        return formatDateForUi($this->maturity_date);
    }

    /**
     * Set maturity date from UI format (d/m/Y) to database format (Y-m-d)
     */
    public function setMaturityDateAttribute($value)
    {
        $this->attributes['maturity_date'] = formatDateForDatabase($value);
    }
}
