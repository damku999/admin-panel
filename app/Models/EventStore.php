<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventStore extends Model
{
    use HasFactory;

    protected $table = 'event_store';

    protected $fillable = [
        'aggregate_type',
        'aggregate_id',
        'event_type',
        'event_data',
        'metadata',
        'version',
        'occurred_at'
    ];

    protected $casts = [
        'event_data' => 'array',
        'metadata' => 'array',
        'version' => 'integer',
        'occurred_at' => 'datetime'
    ];

    // Scopes
    public function scopeForAggregate($query, $aggregateType, $aggregateId)
    {
        return $query->where('aggregate_type', $aggregateType)
                    ->where('aggregate_id', $aggregateId);
    }

    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeFromVersion($query, $version)
    {
        return $query->where('version', '>=', $version);
    }

    public function scopeToVersion($query, $version)
    {
        return $query->where('version', '<=', $version);
    }

    public function scopeBetweenVersions($query, $fromVersion, $toVersion)
    {
        return $query->where('version', '>=', $fromVersion)
                    ->where('version', '<=', $toVersion);
    }

    public function scopeOrderedByVersion($query)
    {
        return $query->orderBy('version', 'asc');
    }

    public function scopeOrderedByOccurrence($query)
    {
        return $query->orderBy('occurred_at', 'asc');
    }

    // Accessors
    public function getAggregateAttribute()
    {
        return [
            'type' => $this->aggregate_type,
            'id' => $this->aggregate_id
        ];
    }

    public function getEventPayloadAttribute()
    {
        return [
            'type' => $this->event_type,
            'data' => $this->event_data,
            'metadata' => $this->metadata,
            'version' => $this->version,
            'occurred_at' => $this->occurred_at
        ];
    }

    // Methods
    public function getEventData($key = null, $default = null)
    {
        if ($key === null) {
            return $this->event_data;
        }

        return data_get($this->event_data, $key, $default);
    }

    public function getMetadata($key = null, $default = null)
    {
        if ($key === null) {
            return $this->metadata;
        }

        return data_get($this->metadata, $key, $default);
    }

    public function hasEventData($key)
    {
        return data_get($this->event_data, $key) !== null;
    }

    public function hasMetadata($key)
    {
        return data_get($this->metadata, $key) !== null;
    }

    // Static methods for event sourcing
    public static function appendEvent(
        string $aggregateType,
        string $aggregateId,
        string $eventType,
        array $eventData,
        array $metadata = []
    ) {
        $lastVersion = static::forAggregate($aggregateType, $aggregateId)
            ->max('version') ?? 0;

        return static::create([
            'aggregate_type' => $aggregateType,
            'aggregate_id' => $aggregateId,
            'event_type' => $eventType,
            'event_data' => $eventData,
            'metadata' => array_merge($metadata, [
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]),
            'version' => $lastVersion + 1,
            'occurred_at' => now()
        ]);
    }

    public static function getEventsForAggregate(string $aggregateType, string $aggregateId, int $fromVersion = 1)
    {
        return static::forAggregate($aggregateType, $aggregateId)
            ->fromVersion($fromVersion)
            ->orderedByVersion()
            ->get();
    }

    public static function getAggregateVersion(string $aggregateType, string $aggregateId)
    {
        return static::forAggregate($aggregateType, $aggregateId)
            ->max('version') ?? 0;
    }

    public static function getEventsByType(string $eventType, int $limit = null)
    {
        $query = static::byEventType($eventType)->orderedByOccurrence();

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public static function getRecentEvents(int $limit = 100)
    {
        return static::orderedByOccurrence()
            ->latest('occurred_at')
            ->limit($limit)
            ->get();
    }

    // Validation rules
    public static function validationRules()
    {
        return [
            'aggregate_type' => 'required|string|max:255',
            'aggregate_id' => 'required|string|max:255',
            'event_type' => 'required|string|max:255',
            'event_data' => 'required|array',
            'metadata' => 'nullable|array',
            'version' => 'required|integer|min:1',
            'occurred_at' => 'required|date'
        ];
    }
}