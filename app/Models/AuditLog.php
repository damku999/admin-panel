<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\AuditLog
 *
 * @property int $id
 * @property string $auditable_type
 * @property int $auditable_id
 * @property string|null $actor_type
 * @property int|null $actor_id
 * @property string|null $action
 * @property string $event
 * @property string $event_category
 * @property string|null $target_type
 * @property int|null $target_id
 * @property string|null $properties
 * @property array|null $old_values
 * @property array|null $new_values
 * @property array|null $metadata
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $session_id
 * @property string|null $request_id
 * @property \Illuminate\Support\Carbon $occurred_at
 * @property string $severity
 * @property int|null $risk_score
 * @property string|null $risk_level
 * @property array|null $risk_factors
 * @property bool $is_suspicious
 * @property string|null $category
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $actor
 * @property-read Model|\Eloquent $auditable
 * @property-read string|null $formatted_location
 * @property-read string $risk_badge_class
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog byEventCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog byRiskScore(int $minScore)
 * @method static \Database\Factories\AuditLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog highRisk()
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog recentActivity(int $hours = 24)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog suspicious()
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereActorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereActorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereEventCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereIsSuspicious($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereOccurredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereRiskFactors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereRiskLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereRiskScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereSeverity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereTargetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereUserAgent($value)
 * @mixin \Eloquent
 */
class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'actor_type',
        'actor_id',
        'event',
        'event_category',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent',
        'session_id',
        'request_id',
        'risk_score',
        'risk_level',
        'risk_factors',
        'is_suspicious',
        'location_country',
        'location_city',
        'location_lat',
        'location_lng',
        'occurred_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'risk_factors' => 'array',
        'is_suspicious' => 'boolean',
        'occurred_at' => 'datetime',
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
        'risk_score' => 'integer',
    ];

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeHighRisk($query)
    {
        return $query->where('risk_level', 'high')
            ->orWhere('risk_level', 'critical');
    }

    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }

    public function scopeByEventCategory($query, string $category)
    {
        return $query->where('event_category', $category);
    }

    public function scopeByRiskScore($query, int $minScore)
    {
        return $query->where('risk_score', '>=', $minScore);
    }

    public function scopeRecentActivity($query, int $hours = 24)
    {
        return $query->where('occurred_at', '>=', now()->subHours($hours));
    }

    public function getFormattedLocationAttribute(): ?string
    {
        if ($this->location_city && $this->location_country) {
            return "{$this->location_city}, {$this->location_country}";
        }

        return $this->location_country;
    }

    public function getRiskBadgeClassAttribute(): string
    {
        return match ($this->risk_level) {
            'critical' => 'badge-danger',
            'high' => 'badge-warning',
            'medium' => 'badge-info',
            'low' => 'badge-success',
            default => 'badge-secondary',
        };
    }

    public function hasRiskFactor(string $factor): bool
    {
        return in_array($factor, $this->risk_factors ?? []);
    }
}
