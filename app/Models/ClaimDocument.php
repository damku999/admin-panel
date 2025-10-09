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
 * App\Models\ClaimDocument
 *
 * @property int $id
 * @property int $claim_id
 * @property string $document_name
 * @property string|null $description
 * @property bool $is_required
 * @property bool $is_submitted
 * @property string|null $document_path
 * @property \Illuminate\Support\Carbon|null $submitted_date
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
 * @property-read string|null $submitted_date_formatted
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\ClaimDocumentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereClaimId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereDocumentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereIsSubmitted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereSubmittedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClaimDocument withoutTrashed()
 * @mixin \Eloquent
 */
class ClaimDocument extends Model
{
    use HasApiTokens, HasFactory, HasRoles, LogsActivity, SoftDeletes, TableRecordObserver;

    protected $fillable = [
        'claim_id',
        'document_name',
        'description',
        'is_required',
        'is_submitted',
        'document_path',
        'submitted_date',
        'notes',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_submitted' => 'boolean',
        'submitted_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    /**
     * Get the claim that owns the document.
     */
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    /**
     * Mark document as submitted.
     */
    public function markAsSubmitted(?string $documentPath = null): void
    {
        $this->update([
            'is_submitted' => true,
            'submitted_date' => now(),
            'document_path' => $documentPath,
        ]);
    }

    /**
     * Mark document as not submitted.
     */
    public function markAsNotSubmitted(): void
    {
        $this->update([
            'is_submitted' => false,
            'submitted_date' => null,
            'document_path' => null,
        ]);
    }

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Get formatted submitted date.
     */
    public function getSubmittedDateFormattedAttribute(): ?string
    {
        return $this->submitted_date ? formatDateForUi($this->submitted_date) : null;
    }

    /**
     * Get document status badge class.
     */
    public function getStatusBadgeClass(): string
    {
        if ($this->is_submitted) {
            return 'badge-success';
        }

        return $this->is_required ? 'badge-danger' : 'badge-warning';
    }

    /**
     * Get document status text.
     */
    public function getStatusText(): string
    {
        if ($this->is_submitted) {
            return 'Submitted';
        }

        return $this->is_required ? 'Required' : 'Optional';
    }

    /**
     * Check if document has file uploaded.
     */
    public function hasFile(): bool
    {
        return ! empty($this->document_path) && file_exists(storage_path('app/public/'.$this->document_path));
    }

    /**
     * Get full document URL.
     */
    public function getDocumentUrl(): ?string
    {
        return $this->document_path ? asset('storage/'.$this->document_path) : null;
    }
}
