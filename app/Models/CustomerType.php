<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CustomerType
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Customer> $customers
 * @property-read int|null $customers_count
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType active()
 * @method static \Database\Factories\CustomerTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType ordered()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerType withoutTrashed()
 * @mixin \Eloquent
 */
class CustomerType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_types';

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
     * Customers that belong to this type
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'customer_type_id');
    }

    /**
     * Scope: Active customer types only
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
