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
 *
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
 *
 * @mixin \Eloquent
 */
class ReferenceUser extends Authenticatable
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
        return $this->hasMany(CustomerInsurance::class, 'reference_user_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
