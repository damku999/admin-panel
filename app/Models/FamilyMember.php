<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\FamilyMember
 *
 * @property int $id
 * @property int $family_group_id Family group this member belongs to
 * @property int $customer_id Customer who is the family member
 * @property string|null $relationship Relationship to family head (father/mother/child/spouse/etc)
 * @property bool $is_head Is this member the family head
 * @property bool $status Active/inactive status
 * @property int|null $created_by Admin user who created this
 * @property int|null $updated_by Admin user who last updated this
 * @property int|null $deleted_by Admin user who deleted this
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\FamilyGroup|null $familyGroup
 * @method static \Database\Factories\FamilyMemberFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember whereFamilyGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember whereIsHead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember whereRelationship($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyMember whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class FamilyMember extends Model
{
    use HasFactory, LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $fillable = [
        'family_group_id',
        'customer_id',
        'relationship',
        'is_head',
        'status',
    ];

    protected $casts = [
        'is_head' => 'boolean',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the family group this member belongs to.
     */
    public function familyGroup(): BelongsTo
    {
        return $this->belongsTo(FamilyGroup::class);
    }

    /**
     * Get the customer for this family member.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Check if this member is the family head.
     */
    public function isFamilyHead(): bool
    {
        return $this->is_head === true;
    }

    /**
     * Check if this member is active.
     */
    public function isActive(): bool
    {
        return $this->status === true;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
