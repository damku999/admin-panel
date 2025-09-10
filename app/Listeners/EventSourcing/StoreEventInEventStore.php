<?php

namespace App\Listeners\EventSourcing;

use App\Services\EventSourcingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreEventInEventStore implements ShouldQueue
{
    use InteractsWithQueue;

    protected EventSourcingService $eventSourcingService;

    public function __construct(EventSourcingService $eventSourcingService)
    {
        $this->eventSourcingService = $eventSourcingService;
    }

    public function handle($event): void
    {
        $eventName = get_class($event);
        $eventData = $this->extractEventData($event);
        
        [$aggregateType, $aggregateId] = $this->extractAggregateInfo($event);
        
        $this->eventSourcingService->store(
            eventName: $eventName,
            eventData: $eventData,
            aggregateType: $aggregateType,
            aggregateId: $aggregateId
        );
    }

    protected function extractEventData($event): array
    {
        // Try to get structured event data
        if (method_exists($event, 'getEventData')) {
            return $event->getEventData();
        }

        // Fallback to reflection-based extraction
        $reflection = new \ReflectionClass($event);
        $data = [];

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            if ($propertyName !== 'connection' && $propertyName !== 'queue') {
                $data[$propertyName] = $event->{$propertyName};
            }
        }

        return $data;
    }

    protected function extractAggregateInfo($event): array
    {
        // Determine aggregate type and ID based on event properties
        if (property_exists($event, 'customer')) {
            return ['Customer', (string) $event->customer->id];
        }
        
        if (property_exists($event, 'quotation')) {
            return ['Quotation', (string) $event->quotation->id];
        }
        
        if (property_exists($event, 'policy')) {
            return ['CustomerInsurance', (string) $event->policy->id];
        }

        if (property_exists($event, 'originalPolicy') && property_exists($event, 'renewedPolicy')) {
            return ['CustomerInsurance', (string) $event->renewedPolicy->id];
        }

        // For communication events, use customer as aggregate if available
        if (property_exists($event, 'customerId') && $event->customerId) {
            return ['Customer', (string) $event->customerId];
        }

        return [null, null];
    }

    public function failed($event, \Throwable $exception): void
    {
        \Log::error('Failed to store event in event store', [
            'event_class' => get_class($event),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}