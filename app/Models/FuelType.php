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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerInsurance> $customerInsurances
 * @property-read int|null $customer_insurances_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\FuelTypeFactory factory($count = null, $state = [])
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
class FuelType extends Authenticatable
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
    ];

    public function customerInsurances()
    {
        return $this->hasMany(CustomerInsurance::class, 'fuel_type_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
