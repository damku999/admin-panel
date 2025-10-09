<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CustomerType
 *
 * @method static \Database\Factories\CustomerTypeFactory factory($count = null, $state = [])
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
