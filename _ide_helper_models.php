<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Branch
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CustomerInsurance> $customerInsurances
 * @property-read int|null $customer_insurances_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Branch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch query()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch withoutTrashed()
 * @mixin \Eloquent
 */
	class Branch extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Broker
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $mobile_number
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CustomerInsurance> $customerInsurances
 * @property-read int|null $customer_insurances_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|Broker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Broker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Broker onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Broker permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Broker query()
 * @method static \Illuminate\Database\Eloquent\Builder|Broker role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Broker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Broker whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Broker whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Broker whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Broker whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Broker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Broker whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Broker whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Broker whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Broker whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Broker whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Broker withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Broker withoutTrashed()
 * @mixin \Eloquent
 */
	class Broker extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Customer
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $mobile_number
 * @property string|null $date_of_birth
 * @property string|null $wedding_anniversary_date
 * @property string|null $engagement_anniversary_date
 * @property string|null $type
 * @property int|null $status
 * @property string|null $pan_card_number
 * @property string|null $aadhar_card_number
 * @property string|null $gst_number
 * @property string|null $pan_card_path
 * @property string|null $aadhar_card_path
 * @property string|null $gst_path
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CustomerInsurance> $insurance
 * @property-read int|null $insurance_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAadharCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAadharCardPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereEngagementAnniversaryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereGstNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereGstPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePanCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePanCardPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereWeddingAnniversaryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer withoutTrashed()
 * @mixin \Eloquent
 */
	class Customer extends \Eloquent {}
}

namespace App\Models{
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
 * @property int $is_renewed
 * @property string|null $renewed_date
 * @property int|null $new_insurance_id
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereIsRenewed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereNewInsuranceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInsurance whereRenewedDate($value)
 */
	class CustomerInsurance extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FuelType
 *
 * @property int $id
 * @property string|null $name
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CustomerInsurance> $customerInsurances
 * @property-read int|null $customer_insurances_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType query()
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FuelType withoutTrashed()
 * @mixin \Eloquent
 */
	class FuelType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\InsuranceCompany
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $mobile_number
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CustomerInsurance> $customerInsurances
 * @property-read int|null $customer_insurances_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany query()
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InsuranceCompany withoutTrashed()
 * @mixin \Eloquent
 */
	class InsuranceCompany extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PolicyType
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CustomerInsurance> $customerInsurances
 * @property-read int|null $customer_insurances_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType query()
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PolicyType withoutTrashed()
 * @mixin \Eloquent
 */
	class PolicyType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PremiumType
 *
 * @property int $id
 * @property string|null $name
 * @property int $is_vehicle
 * @property int $is_life_insurance_policies
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CustomerInsurance> $customerInsurances
 * @property-read int|null $customer_insurances_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType query()
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType whereIsLifeInsurancePolicies($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType whereIsVehicle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PremiumType withoutTrashed()
 * @mixin \Eloquent
 */
	class PremiumType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ReferenceUser
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $mobile_number
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CustomerInsurance> $customerInsurances
 * @property-read int|null $customer_insurances_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceUser withoutTrashed()
 * @mixin \Eloquent
 */
	class ReferenceUser extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\RelationshipManager
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $mobile_number
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CustomerInsurance> $customerInsurances
 * @property-read int|null $customer_insurances_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RelationshipManager withoutTrashed()
 * @mixin \Eloquent
 */
	class RelationshipManager extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Report
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property array|null $selected_columns
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Report permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder|Report role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereSelectedColumns($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Report withoutTrashed()
 * @mixin \Eloquent
 */
	class Report extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $first_name
 * @property string|null $last_name
 * @property string $email
 * @property string|null $mobile_number
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property int $role_id 1=Admin, 2=TA/TP
 * @property int $status
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read string $full_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Report> $reports
 * @property-read int|null $reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

