<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CustomerDevice
 *
 * @property int $id
 * @property int $customer_id
 * @property string $device_type
 * @property string $device_token
 * @property string|null $device_name
 * @property string|null $device_model
 * @property string|null $os_version
 * @property string|null $app_version
 * @property \Illuminate\Support\Carbon|null $last_active_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Customer|null $customer
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice active()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice whereAppVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice whereDeviceModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice whereDeviceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice whereDeviceToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice whereLastActiveAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice whereOsVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerDevice whereUpdatedAt($value)
 * @mixin \Eloquent
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
