<?php

namespace App\Models;

use App\Traits\TableRecordObserver;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AddonCover extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, TableRecordObserver, LogsActivity;
    
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