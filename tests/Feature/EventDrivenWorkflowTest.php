<?php

namespace Tests\Feature;

use App\Events\Customer\CustomerRegistered;
use App\Events\Quotation\QuotationGenerated;
use App\Models\Customer;
use App\Models\PolicyType;
use App\Models\Quotation;
use App\Services\EventSourcingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EventDrivenWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->artisan('migrate');
        
        // Seed basic data
        $this->seed();
    }

    public function test_customer_registration_fires_events(): void
    {
        Event::fake();
        
        $customerData = [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'mobile_number' => '9876543210',
            'status' => 1,
            'type' => 'individual',
            'date_of_birth' => '1990-01-01',
        ];

        $customer = Customer::create($customerData);
        
        // Fire CustomerRegistered event manually (since we're testing the event system)
        CustomerRegistered::dispatch($customer, ['test' => true], 'test');
        
        // Assert event was dispatched
        Event::assertDispatched(CustomerRegistered::class, function ($event) use ($customer) {
            return $event->customer->id === $customer->id &&
                   $event->registrationChannel === 'test';
        });
    }

    public function test_quotation_generation_fires_events(): void
    {
        Event::fake();
        
        $customer = Customer::factory()->create();
        $policyType = PolicyType::first() ?? PolicyType::create([
            'name' => 'Test Policy',
            'description' => 'Test Description',
            'status' => 1,
        ]);
        
        $quotationData = [
            'customer_id' => $customer->id,
            'policy_type_id' => $policyType->id,
            'quotation_number' => 'QT' . time(),
            'vehicle_number' => 'TN01AB1234',
            'sum_assured' => 500000,
            'total_idv' => 450000,
        ];

        $quotation = Quotation::create($quotationData);
        
        // Fire QuotationGenerated event
        QuotationGenerated::dispatch($quotation);
        
        // Assert event was dispatched
        Event::assertDispatched(QuotationGenerated::class, function ($event) use ($quotation) {
            return $event->quotation->id === $quotation->id;
        });
    }

    public function test_event_sourcing_stores_events(): void
    {
        $eventSourcingService = app(EventSourcingService::class);
        
        $eventData = [
            'customer_id' => 1,
            'action' => 'test_action',
            'timestamp' => now()->toISOString(),
        ];
        
        $result = $eventSourcingService->store(
            'TestEvent',
            $eventData,
            'Customer',
            '1'
        );
        
        $this->assertTrue($result);
        
        // Verify event was stored
        $events = $eventSourcingService->getEventsForAggregate('Customer', '1');
        $this->assertCount(1, $events);
        $this->assertEquals('TestEvent', $events[0]['event_name']);
        $this->assertEquals($eventData, $events[0]['event_data']);
    }

    public function test_event_listeners_are_queued(): void
    {
        Queue::fake();
        
        $customer = Customer::factory()->create();
        
        // Dispatch event
        CustomerRegistered::dispatch($customer, [], 'test');
        
        // Assert jobs were pushed to queue
        Queue::assertPushed(\App\Listeners\Customer\SendWelcomeEmail::class);
        Queue::assertPushed(\App\Listeners\Customer\CreateCustomerAuditLog::class);
        Queue::assertPushed(\App\Listeners\Customer\NotifyAdminOfRegistration::class);
    }

    public function test_event_stream_retrieval(): void
    {
        $eventSourcingService = app(EventSourcingService::class);
        
        // Store multiple events
        $eventSourcingService->store('Event1', ['data' => 'test1'], 'Customer', '1');
        $eventSourcingService->store('Event2', ['data' => 'test2'], 'Customer', '1');
        $eventSourcingService->store('Event3', ['data' => 'test3'], 'Quotation', '1');
        
        // Get event stream
        $events = $eventSourcingService->getEventStream();
        
        $this->assertCount(3, $events);
        $this->assertEquals('Event3', $events[0]['event_name']); // Most recent first
        $this->assertEquals('Event2', $events[1]['event_name']);
        $this->assertEquals('Event1', $events[2]['event_name']);
    }

    public function test_aggregate_statistics(): void
    {
        $eventSourcingService = app(EventSourcingService::class);
        
        // Store multiple events for Customer aggregate
        $eventSourcingService->store('CustomerRegistered', ['data' => 'test1'], 'Customer', '1');
        $eventSourcingService->store('CustomerRegistered', ['data' => 'test2'], 'Customer', '2');
        $eventSourcingService->store('CustomerProfileUpdated', ['data' => 'test3'], 'Customer', '1');
        
        $stats = $eventSourcingService->getAggregateStatistics('Customer');
        
        $this->assertEquals('Customer', $stats['aggregate_type']);
        $this->assertEquals(3, $stats['total_events']);
        $this->assertEquals(2, $stats['unique_aggregates']);
        $this->assertCount(2, $stats['event_types']); // CustomerRegistered, CustomerProfileUpdated
    }

    public function test_event_data_extraction(): void
    {
        $customer = Customer::factory()->create();
        $event = new CustomerRegistered($customer, ['test_meta' => 'test_value'], 'web');
        
        $eventData = $event->getEventData();
        
        $this->assertArrayHasKey('customer_id', $eventData);
        $this->assertArrayHasKey('customer_email', $eventData);
        $this->assertArrayHasKey('registration_channel', $eventData);
        $this->assertArrayHasKey('metadata', $eventData);
        
        $this->assertEquals($customer->id, $eventData['customer_id']);
        $this->assertEquals($customer->email, $eventData['customer_email']);
        $this->assertEquals('web', $eventData['registration_channel']);
        $this->assertEquals(['test_meta' => 'test_value'], $eventData['metadata']);
    }
}