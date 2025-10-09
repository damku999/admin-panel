<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CommissionType
 *
 * @method static \Database\Factories\CommissionTypeFactory factory($count = null, $state = [])
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
