<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use App\Models\CustomerInsurance;
use Spatie\Activitylog\LogOptions;
use App\Traits\TableRecordObserver;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
class Report extends Authenticatable
{
    use  HasRoles, SoftDeletes, TableRecordObserver, LogsActivity;

    protected $guarded = [];

    protected static $logName = 'User Reports';

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $casts = ['selected_columns' => 'array'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Support\Collection
     */

    public static function getInsuranceReport($filters)
    {
        $report = Report::where(['user_id' => auth()->user()->id, 'name' => $filters['report_name']])->first();
        $report->selected_columns = collect($report->selected_columns)->map(function ($item) {
            $item['select'] = $item['table_column_name'] . ' as ' . $item['display_name'];
            return $item;
        })->pluck('select');

        $customerInsurances = CustomerInsurance::with(
            'branch',
            'broker',
            'relationshipManager',
            'customer',
            'insuranceCompany',
            'premiumType',
            'policyType',
            'fuelType'
        )
            ->when(!empty($filters['record_creation_start_date']), function ($query) use ($filters) {
                return $query->where('created_at', '>=', Carbon::parse($filters['record_creation_start_date'])->startOfDay()->format('Y-m-d H:i:s'));
            })
            ->when(!empty($filters['record_creation_end_date']), function ($query) use ($filters) {
                return $query->where('created_at', '<=', Carbon::parse($filters['record_creation_end_date'])->endOfDay()->format('Y-m-d H:i:s'));
            })
            ->when(!empty($filters['issue_start_date']), function ($query) use ($filters) {
                return $query->where('issue_date', '>=', Carbon::parse($filters['issue_start_date'])->format('Y-m-d'));
            })
            ->when(!empty($filters['issue_end_date']), function ($query) use ($filters) {
                return $query->where('issue_date', '<=', Carbon::parse($filters['issue_end_date'])->format('Y-m-d'));
            })
            ->when(!empty($filters['branch_id']), function ($query) use ($filters) {
                return $query->whereHas('branch', function ($query) use ($filters) {
                    $query->where('id', $filters['branch_id']);
                });
            })
            ->when(!empty($filters['broker_id']), function ($query) use ($filters) {
                return $query->whereHas('broker', function ($query) use ($filters) {
                    $query->where('id', $filters['broker_id']);
                });
            })
            ->when(!empty($filters['relationship_manager_id']), function ($query) use ($filters) {
                return $query->whereHas('relationshipManager', function ($query) use ($filters) {
                    $query->where('id', $filters['relationship_manager_id']);
                });
            })
            ->when(!empty($filters['insurance_company_id']), function ($query) use ($filters) {
                return $query->whereHas('insuranceCompany', function ($query) use ($filters) {
                    $query->where('id', $filters['insurance_company_id']);
                });
            })
            ->when(!empty($filters['policy_type_id']), function ($query) use ($filters) {
                return $query->whereHas('policyType', function ($query) use ($filters) {
                    $query->where('id', $filters['policy_type_id']);
                });
            })
            ->when(!empty($filters['fuel_type_id']), function ($query) use ($filters) {
                return $query->whereHas('fuelType', function ($query) use ($filters) {
                    $query->where('id', $filters['fuel_type_id']);
                });
            })
            ->when(!empty($filters['premium_type_id']), function ($query) use ($filters) {
                return $query->whereHas('premiumType', function ($query) use ($filters) {
                    $query->where('id', $filters['premium_type_id']);
                });
            })
            ->when(!empty($filters['customer_id']), function ($query) use ($filters) {
                return $query->whereHas('customer', function ($query) use ($filters) {
                    $query->where('id', $filters['customer_id']);
                });
            })
            ->when(!empty($filters['due_start_date']), function ($query) use ($filters) {
                return $query->where('expired_date', '>=', Carbon::parse($filters['due_start_date'])->format('Y-m-01'));
            })
            ->when(!empty($filters['due_end_date']), function ($query) use ($filters) {
                return $query->where('expired_date', '<=', Carbon::parse($filters['due_end_date'])->format('Y-m-31'));
            })
            ->get();
        return $customerInsurances;
    }
}
