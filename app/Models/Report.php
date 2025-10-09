<?php

namespace App\Models;

use App\Traits\TableRecordObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

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
 *
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
 * @method static \Database\Factories\ReportFactory factory($count = null, $state = [])
 *
 * @mixin \Eloquent
 */
class Report extends Authenticatable
{
    use HasFactory, HasRoles, LogsActivity, SoftDeletes, TableRecordObserver;

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

        // Note: Column selection will be handled by frontend/views,
        // this method just retrieves the data with all necessary relationships

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
            ->when(! empty($filters['record_creation_start_date']), function ($query) use ($filters) {
                $startDate = \App\Helpers\DateHelper::isValidDatabaseFormat($filters['record_creation_start_date'])
                    ? $filters['record_creation_start_date']
                    : formatDateForDatabase($filters['record_creation_start_date']);

                return $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay()->format('Y-m-d H:i:s'));
            })
            ->when(! empty($filters['record_creation_end_date']), function ($query) use ($filters) {
                $endDate = \App\Helpers\DateHelper::isValidDatabaseFormat($filters['record_creation_end_date'])
                    ? $filters['record_creation_end_date']
                    : formatDateForDatabase($filters['record_creation_end_date']);

                return $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay()->format('Y-m-d H:i:s'));
            })
            ->when(! empty($filters['issue_start_date']), function ($query) use ($filters) {
                try {
                    $startDate = Carbon::createFromFormat('d/m/Y', $filters['issue_start_date'])->format('Y-m-d');

                    return $query->where('issue_date', '>=', $startDate);
                } catch (\Exception $e) {
                    return $query->where('issue_date', '>=', $filters['issue_start_date']);
                }
            })
            ->when(! empty($filters['issue_end_date']), function ($query) use ($filters) {
                try {
                    $endDate = Carbon::createFromFormat('d/m/Y', $filters['issue_end_date'])->format('Y-m-d');

                    return $query->where('issue_date', '<=', $endDate);
                } catch (\Exception $e) {
                    return $query->where('issue_date', '<=', $filters['issue_end_date']);
                }
            })
            ->when(! empty($filters['broker_id']), function ($query) use ($filters) {
                return $query->whereHas('broker', function ($query) use ($filters) {
                    $query->where('id', $filters['broker_id']);
                });
            })
            ->when(! empty($filters['relationship_manager_id']), function ($query) use ($filters) {
                return $query->whereHas('relationshipManager', function ($query) use ($filters) {
                    $query->where('id', $filters['relationship_manager_id']);
                });
            })
            ->when(! empty($filters['insurance_company_id']), function ($query) use ($filters) {
                return $query->whereHas('insuranceCompany', function ($query) use ($filters) {
                    $query->where('id', $filters['insurance_company_id']);
                });
            })
            ->when(! empty($filters['policy_type_id']), function ($query) use ($filters) {
                return $query->whereHas('policyType', function ($query) use ($filters) {
                    $query->where('id', $filters['policy_type_id']);
                });
            })
            ->when(! empty($filters['fuel_type_id']), function ($query) use ($filters) {
                return $query->whereHas('fuelType', function ($query) use ($filters) {
                    $query->where('id', $filters['fuel_type_id']);
                });
            })
            ->when(! empty($filters['premium_type_id']), function ($query) use ($filters) {
                return $query->whereHas('premiumType', function ($query) use ($filters) {
                    if (is_array($filters['premium_type_id'])) {
                        $query->whereIn('id', $filters['premium_type_id']);
                    } else {
                        $query->where('id', $filters['premium_type_id']);
                    }
                });
            })
            ->when(! empty($filters['customer_id']), function ($query) use ($filters) {
                return $query->whereHas('customer', function ($query) use ($filters) {
                    $query->where('id', $filters['customer_id']);
                });
            })
            ->when(! empty($filters['due_start_date']), function ($query) use ($filters) {
                // Due dates can be in m/Y or mm/Y format, handle both
                try {
                    $dateStr = trim($filters['due_start_date']);
                    $startDate = null;
                    $usedFormat = null;

                    // Try parsing with different formats
                    foreach (['m/Y', 'n/Y', 'Y-m', 'd/m/Y', 'M Y', 'F Y'] as $format) {
                        try {
                            $parsed = Carbon::createFromFormat($format, $dateStr);
                            if ($parsed && $parsed->year > 2000 && $parsed->year < 2100) {
                                $startDate = $parsed;
                                $usedFormat = $format;
                                break;
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }

                    if (! $startDate) {
                        throw new \Exception("Unable to parse date: {$dateStr}");
                    }

                    $formattedDate = $startDate->startOfMonth()->format('Y-m-d');

                    \Log::info('✅ Due start date filter applied', [
                        'input' => $dateStr,
                        'format_used' => $usedFormat,
                        'parsed' => $formattedDate,
                        'query' => "expired_date >= {$formattedDate}",
                    ]);

                    return $query->where('expired_date', '>=', $formattedDate);
                } catch (\Exception $e) {
                    \Log::error('❌ Due start date parsing failed', [
                        'input' => $filters['due_start_date'] ?? 'null',
                        'error' => $e->getMessage(),
                    ]);

                    return $query;
                }
            })
            ->when(! empty($filters['due_end_date']), function ($query) use ($filters) {
                // Due dates can be in m/Y or mm/Y format, handle both
                try {
                    $dateStr = trim($filters['due_end_date']);
                    $endDate = null;
                    $usedFormat = null;

                    // Try parsing with different formats
                    foreach (['m/Y', 'n/Y', 'Y-m', 'd/m/Y', 'M Y', 'F Y'] as $format) {
                        try {
                            $parsed = Carbon::createFromFormat($format, $dateStr);
                            if ($parsed && $parsed->year > 2000 && $parsed->year < 2100) {
                                $endDate = $parsed;
                                $usedFormat = $format;
                                break;
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }

                    if (! $endDate) {
                        throw new \Exception("Unable to parse date: {$dateStr}");
                    }

                    $formattedDate = $endDate->endOfMonth()->format('Y-m-d');

                    \Log::info('✅ Due end date filter applied', [
                        'input' => $dateStr,
                        'format_used' => $usedFormat,
                        'parsed' => $formattedDate,
                        'query' => "expired_date <= {$formattedDate}",
                    ]);

                    return $query->where('expired_date', '<=', $formattedDate);
                } catch (\Exception $e) {
                    \Log::error('❌ Due end date parsing failed', [
                        'input' => $filters['due_end_date'] ?? 'null',
                        'error' => $e->getMessage(),
                    ]);

                    return $query;
                }
            })
            ->when(! empty($filters['status']), function ($query) use ($filters) {
                if ($filters['status'] === 'active') {
                    return $query->where('status', 1);
                } elseif ($filters['status'] === 'inactive') {
                    return $query->where('status', 0);
                }

                return $query;
            })
            ->when(! empty($filters['premium_amount_min']), function ($query) use ($filters) {
                return $query->where('final_premium_with_gst', '>=', $filters['premium_amount_min']);
            })
            ->when(! empty($filters['premium_amount_max']), function ($query) use ($filters) {
                return $query->where('final_premium_with_gst', '<=', $filters['premium_amount_max']);
            })
            ->get();

        return $customerInsurances;
    }
}
