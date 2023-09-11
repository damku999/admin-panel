<?php

namespace App\Models;

use App\Models\CustomerInsurance;
use Spatie\Activitylog\LogOptions;
use App\Traits\TableRecordObserver;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
class PolicyType extends Authenticatable
{
    use  HasFactory, Notifiable, HasRoles, SoftDeletes, TableRecordObserver, LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function customerInsurances()
    {
        return $this->hasMany(CustomerInsurance::class, 'policy_type_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
