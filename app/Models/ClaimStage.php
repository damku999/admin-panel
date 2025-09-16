<?php

namespace App\Models;

use App\Traits\TableRecordObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\ClaimStage
 *
 * @property int $id
 * @property int $claim_id
 * @property string $stage_name
 * @property string|null $description
 * @property bool $is_current
 * @property bool $is_completed
 * @property \Illuminate\Support\Carbon|null $stage_date
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read Claim $claim
 */
class ClaimStage extends Model
{
    use HasApiTokens, HasFactory, HasRoles, SoftDeletes, TableRecordObserver, LogsActivity;

    protected $fillable = [
        'claim_id',
        'stage_name',
        'description',
        'is_current',
        'is_completed',
        'stage_date',
        'notes',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'is_completed' => 'boolean',
        'stage_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    /**
     * Get the claim that owns the stage.
     */
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    /**
     * Set this stage as current and mark previous as not current.
     */
    public function setAsCurrent(): void
    {
        // Mark all other stages for this claim as not current
        self::where('claim_id', $this->claim_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        // Mark this stage as current
        $this->update(['is_current' => true]);
    }

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Get formatted stage date.
     */
    public function getStageDateFormattedAttribute(): ?string
    {
        return $this->stage_date ? formatDateForUi($this->stage_date) : null;
    }

    /**
     * Set stage date from UI format.
     */
    public function setStageDateAttribute($value): void
    {
        if ($value) {
            $this->attributes['stage_date'] = formatDateForDatabase($value);
        }
    }
}