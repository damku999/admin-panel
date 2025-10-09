<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\AuditLog
 *
 * @method static \Database\Factories\AuditLogFactory factory($count = null, $state = [])
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
