<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventSourcingService
{
    protected string $eventsTable = 'event_store';

    public function store(string $eventName, array $eventData, ?string $aggregateType = null, ?string $aggregateId = null): bool
    {
        try {
            $eventId = $this->generateEventId();
            
            DB::table($this->eventsTable)->insert([
                'event_id' => $eventId,
                'event_name' => $eventName,
                'aggregate_type' => $aggregateType,
                'aggregate_id' => $aggregateId,
                'event_data' => json_encode($eventData),
                'metadata' => json_encode($this->gatherMetadata()),
                'occurred_at' => now(),
                'created_at' => now(),
            ]);

            Log::info('Event stored in event store', [
                'event_id' => $eventId,
                'event_name' => $eventName,
                'aggregate_type' => $aggregateType,
                'aggregate_id' => $aggregateId,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to store event', [
                'event_name' => $eventName,
                'error' => $e->getMessage(),
                'aggregate_type' => $aggregateType,
                'aggregate_id' => $aggregateId,
            ]);

            return false;
        }
    }

    public function getEventsForAggregate(string $aggregateType, string $aggregateId, ?int $fromVersion = null): array
    {
        $query = DB::table($this->eventsTable)
            ->where('aggregate_type', $aggregateType)
            ->where('aggregate_id', $aggregateId)
            ->orderBy('occurred_at');

        if ($fromVersion) {
            $query->where('id', '>', $fromVersion);
        }

        return $query->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_id' => $event->event_id,
                    'event_name' => $event->event_name,
                    'event_data' => json_decode($event->event_data, true),
                    'metadata' => json_decode($event->metadata, true),
                    'occurred_at' => $event->occurred_at,
                ];
            })
            ->toArray();
    }

    public function getEventsByType(string $eventName, ?int $limit = null, ?\DateTime $since = null): array
    {
        $query = DB::table($this->eventsTable)
            ->where('event_name', $eventName)
            ->orderBy('occurred_at', 'desc');

        if ($since) {
            $query->where('occurred_at', '>=', $since);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_id' => $event->event_id,
                    'event_name' => $event->event_name,
                    'aggregate_type' => $event->aggregate_type,
                    'aggregate_id' => $event->aggregate_id,
                    'event_data' => json_decode($event->event_data, true),
                    'metadata' => json_decode($event->metadata, true),
                    'occurred_at' => $event->occurred_at,
                ];
            })
            ->toArray();
    }

    public function getEventStream(?\DateTime $since = null, ?int $limit = 1000): array
    {
        $query = DB::table($this->eventsTable)
            ->orderBy('occurred_at', 'desc');

        if ($since) {
            $query->where('occurred_at', '>=', $since);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_id' => $event->event_id,
                    'event_name' => $event->event_name,
                    'aggregate_type' => $event->aggregate_type,
                    'aggregate_id' => $event->aggregate_id,
                    'event_data' => json_decode($event->event_data, true),
                    'metadata' => json_decode($event->metadata, true),
                    'occurred_at' => $event->occurred_at,
                ];
            })
            ->toArray();
    }

    public function rebuildProjection(string $aggregateType, string $aggregateId, callable $projectionHandler): void
    {
        $events = $this->getEventsForAggregate($aggregateType, $aggregateId);
        
        foreach ($events as $event) {
            $projectionHandler($event);
        }
    }

    public function getAggregateStatistics(string $aggregateType): array
    {
        $stats = DB::table($this->eventsTable)
            ->select([
                'event_name',
                DB::raw('COUNT(*) as event_count'),
                DB::raw('MIN(occurred_at) as first_event'),
                DB::raw('MAX(occurred_at) as last_event'),
            ])
            ->where('aggregate_type', $aggregateType)
            ->groupBy('event_name')
            ->get()
            ->toArray();

        return [
            'aggregate_type' => $aggregateType,
            'total_events' => array_sum(array_column($stats, 'event_count')),
            'event_types' => $stats,
            'unique_aggregates' => DB::table($this->eventsTable)
                ->where('aggregate_type', $aggregateType)
                ->distinct('aggregate_id')
                ->count(),
        ];
    }

    protected function generateEventId(): string
    {
        return (string) \Illuminate\Support\Str::uuid();
    }

    protected function gatherMetadata(): array
    {
        return [
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
        ];
    }
}