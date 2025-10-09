<?php

namespace App\Models;

use App\Traits\TableRecordObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\ClaimLiabilityDetail
 *
 * @property int $id
 * @property int $claim_id
 * @property string $claim_type
 * @property string|null $claim_amount
 * @property string|null $salvage_amount
 * @property string|null $less_claim_charge
 * @property string|null $amount_to_be_paid
 * @property string|null $less_salvage_amount
 * @property string|null $less_deductions
 * @property string|null $claim_amount_received
 * @property string|null $hospital_name
 * @property string|null $hospital_address
 * @property string|null $garage_name
 * @property string|null $garage_address
 * @property string|null $estimated_amount
 * @property string|null $approved_amount
 * @property string|null $final_amount
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Claim|null $claim
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\ClaimLiabilityDetailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereAmountToBePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereApprovedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereClaimAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereClaimAmountReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereClaimId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereClaimType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereEstimatedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereFinalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereGarageAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereGarageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereHospitalAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereHospitalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereLessClaimCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereLessDeductions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereLessSalvageAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereSalvageAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimLiabilityDetail withoutTrashed()
 * @mixin \Eloquent
 */
class ClaimLiabilityDetail extends Model
{
    use HasApiTokens, HasFactory, HasRoles, LogsActivity, SoftDeletes, TableRecordObserver;

    protected $fillable = [
        'claim_id',
        'claim_type',
        'hospital_name',
        'hospital_address',
        'garage_name',
        'garage_address',
        'estimated_amount',
        'approved_amount',
        'final_amount',
        'claim_amount',
        'salvage_amount',
        'less_claim_charge',
        'amount_to_be_paid',
        'less_salvage_amount',
        'less_deductions',
        'claim_amount_received',
        'notes',
    ];

    protected $casts = [
        'estimated_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    /**
     * Get the claim that owns the liability detail.
     */
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    /**
     * Check if this is a cashless claim.
     */
    public function isCashless(): bool
    {
        return $this->claim_type === 'Cashless';
    }

    /**
     * Check if this is a reimbursement claim.
     */
    public function isReimbursement(): bool
    {
        return $this->claim_type === 'Reimbursement';
    }

    /**
     * Get the facility name (hospital or garage).
     */
    public function getFacilityName(): ?string
    {
        return $this->hospital_name ?? $this->garage_name;
    }

    /**
     * Get the facility address.
     */
    public function getFacilityAddress(): ?string
    {
        return $this->hospital_address ?? $this->garage_address;
    }

    /**
     * Get formatted estimated amount.
     */
    public function getFormattedEstimatedAmount(): string
    {
        return $this->estimated_amount ? '₹'.number_format($this->estimated_amount, 2) : '-';
    }

    /**
     * Get formatted approved amount.
     */
    public function getFormattedApprovedAmount(): string
    {
        return $this->approved_amount ? '₹'.number_format($this->approved_amount, 2) : '-';
    }

    /**
     * Get formatted final amount.
     */
    public function getFormattedFinalAmount(): string
    {
        return $this->final_amount ? '₹'.number_format($this->final_amount, 2) : '-';
    }

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
