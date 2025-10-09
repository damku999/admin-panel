<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

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
