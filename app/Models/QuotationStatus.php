<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\QuotationStatus
 *
 * @method static \Database\Factories\QuotationStatusFactory factory($count = null, $state = [])
 */
class QuotationStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'quotation_statuses';

    protected $fillable = [
        'name',
        'description',
        'color',
        'is_active',
        'is_final',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_final' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Quotations that have this status
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'quotation_status_id');
    }

    /**
     * Scope: Active statuses only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Final statuses only (accepted/rejected)
     */
    public function scopeFinal($query)
    {
        return $query->where('is_final', true);
    }

    /**
     * Scope: Non-final statuses (draft/generated/sent)
     */
    public function scopeNonFinal($query)
    {
        return $query->where('is_final', false);
    }

    /**
     * Scope: Ordered by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the status color for UI display
     */
    public function getColorAttribute($value)
    {
        return $value ?: '#6c757d'; // Default gray color
    }
}
