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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Claim|null $claim
 * @property-read string|null $stage_date_formatted
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\ClaimStageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage whereClaimId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage whereIsCurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage whereStageDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage whereStageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimStage withoutTrashed()
 * @mixin \Eloquent
 */
class ClaimStage extends Model
{
    use HasApiTokens, HasFactory, HasRoles, LogsActivity, SoftDeletes, TableRecordObserver;

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
