<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CommissionType
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property bool $status
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerInsurance> $customerInsurances
 * @property-read int|null $customer_insurances_count
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType active()
 * @method static \Database\Factories\CommissionTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType ordered()
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType query()
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionType withoutTrashed()
 * @mixin \Eloquent
 */
class CommissionType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'commission_types';

    protected $fillable = [
        'name',
        'description',
        'status',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Customer insurances that use this commission type
     */
    public function customerInsurances()
    {
        return $this->hasMany(CustomerInsurance::class, 'commission_type_id');
    }

    /**
     * Scope: Active commission types only
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Ordered by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
