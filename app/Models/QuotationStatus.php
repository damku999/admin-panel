<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\QuotationStatus
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string|null $color
 * @property bool $is_active
 * @property bool $is_final
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Quotation> $quotations
 * @property-read int|null $quotations_count
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus active()
 * @method static \Database\Factories\QuotationStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus final()
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus nonFinal()
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus ordered()
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus whereIsFinal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|QuotationStatus withoutTrashed()
 * @mixin \Eloquent
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
