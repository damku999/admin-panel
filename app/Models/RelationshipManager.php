<?php

namespace App\Models;

use App\Models\CustomerInsurance;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use App\Traits\TableRecordObserver;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
class RelationshipManager extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, TableRecordObserver, LogsActivity;
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
        return $this->hasMany(CustomerInsurance::class, 'relationship_manager_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
