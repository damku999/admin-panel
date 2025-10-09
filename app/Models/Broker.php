<?php

namespace App\Models;

use App\Traits\TableRecordObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

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
 *
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
 *
 * @mixin \Eloquent
 */
class Broker extends Authenticatable
{
    use HasFactory, HasRoles, LogsActivity, Notifiable, SoftDeletes, TableRecordObserver;

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
        return $this->hasMany(CustomerInsurance::class, 'broker_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
