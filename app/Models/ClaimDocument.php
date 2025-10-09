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
 * @property-read Claim $claim
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
