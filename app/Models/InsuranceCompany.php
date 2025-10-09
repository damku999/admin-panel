<?php

namespace App\Models;

use App\Traits\TableRecordObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerInsurance> $customerInsurances
 * @property-read int|null $customer_insurances_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\InsuranceCompanyFactory factory($count = null, $state = [])
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
class InsuranceCompany extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, LogsActivity, Notifiable, SoftDeletes, TableRecordObserver;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile_number',
        'status',
    ];

    public function customerInsurances()
    {
        return $this->hasMany(CustomerInsurance::class, 'insurance_company_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
