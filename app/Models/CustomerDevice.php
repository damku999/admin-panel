<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Customer Device Model
 *
 * Manages customer device tokens for push notifications
 */
class CustomerDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'device_type',
        'device_token',
        'device_name',
        'device_model',
        'os_version',
        'app_version',
        'last_active_at',
        'is_active',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the customer that owns this device
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope for active devices only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific device type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('device_type', $type);
    }

    /**
     * Update last active timestamp
     */
    public function markActive()
    {
        $this->last_active_at = now();
        $this->save();
    }

    /**
     * Deactivate this device
     */
    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
    }
}
