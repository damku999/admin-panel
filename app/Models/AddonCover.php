<?php

namespace App\Models;

use App\Traits\TableRecordObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\AddonCover
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $order_no
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\AddonCoverFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover query()
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover whereOrderNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AddonCover withoutTrashed()
 * @mixin \Eloquent
 */
class AddonCover extends Authenticatable
{
    use HasFactory, HasRoles, LogsActivity, Notifiable, SoftDeletes, TableRecordObserver;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'order_no',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'order_no' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Handle smart ordering when saving
     */
    protected static function booted()
    {
        static::saving(function ($addonCover) {
            static::handleSmartOrdering($addonCover);
        });
    }

    /**
     * Smart ordering system with auto-assignment and conflict resolution
     */
    private static function handleSmartOrdering($addonCover)
    {
        // If order_no is 0, auto-assign next available number
        if ($addonCover->order_no == 0) {
            $addonCover->order_no = static::getNextAvailableOrder();

            return;
        }

        // For updates, get the original order number
        $originalOrderNo = null;
        if ($addonCover->exists) {
            $originalOrderNo = $addonCover->getOriginal('order_no');
        }

        // If this is an update and order didn't change, no need to process
        if ($originalOrderNo !== null && $originalOrderNo == $addonCover->order_no) {
            return;
        }

        // Check for duplicate order numbers (excluding this record)
        $existingCover = static::where('order_no', $addonCover->order_no)
            ->where('id', '!=', $addonCover->id ?? 0)
            ->first();

        if ($existingCover) {
            // If updating: shift others and move this record to desired position
            if ($originalOrderNo !== null) {
                // First, close the gap from the original position
                static::where('order_no', '>', $originalOrderNo)
                    ->where('id', '!=', $addonCover->id)
                    ->decrement('order_no');
            }

            // Then shift all covers at or after the new position
            static::where('order_no', '>=', $addonCover->order_no)
                ->where('id', '!=', $addonCover->id ?? 0)
                ->increment('order_no');
        } elseif ($originalOrderNo !== null && $originalOrderNo < $addonCover->order_no) {
            // Moving to a higher position: close the gap from original position
            static::where('order_no', '>', $originalOrderNo)
                ->where('order_no', '<=', $addonCover->order_no)
                ->where('id', '!=', $addonCover->id)
                ->decrement('order_no');
        } elseif ($originalOrderNo !== null && $originalOrderNo > $addonCover->order_no) {
            // Moving to a lower position: shift others up
            static::where('order_no', '>=', $addonCover->order_no)
                ->where('order_no', '<', $originalOrderNo)
                ->where('id', '!=', $addonCover->id)
                ->increment('order_no');
        }
    }

    /**
     * Get next available order number after the last order
     */
    private static function getNextAvailableOrder()
    {
        $lastOrder = static::max('order_no') ?? 0;

        return $lastOrder + 1;
    }

    /**
     * Get ordered addon covers for display
     */
    public static function getOrdered($status = 1)
    {
        return static::where('status', $status)
            ->orderBy('order_no', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
