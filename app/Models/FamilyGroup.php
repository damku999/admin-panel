<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\FamilyGroup
 *
 * @property int $id
 * @property string $name Family name or identifier
 * @property int|null $family_head_id Customer ID who is the family head
 * @property bool $status Active/inactive status
 * @property int|null $created_by Admin user who created this
 * @property int|null $updated_by Admin user who last updated this
 * @property int|null $deleted_by Admin user who deleted this
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Customer> $customers
 * @property-read int|null $customers_count
 * @property-read \App\Models\Customer|null $familyHead
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FamilyMember> $familyMembers
 * @property-read int|null $family_members_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FamilyMember> $members
 * @property-read int|null $members_count
 * @method static \Database\Factories\FamilyGroupFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyGroup whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyGroup whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyGroup whereFamilyHeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyGroup whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FamilyGroup whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class FamilyGroup extends Model
{
    use HasFactory, LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $fillable = [
        'name',
        'family_head_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the family head customer.
     */
    public function familyHead(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'family_head_id');
    }

    /**
     * Get all family members.
     */
    public function familyMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    /**
     * Alias for familyMembers relationship (for convenience).
     */
    public function members(): HasMany
    {
        return $this->familyMembers();
    }

    /**
     * Get all customers in this family group.
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'family_group_id');
    }

    /**
     * Check if the family group is active.
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
