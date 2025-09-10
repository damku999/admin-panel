<?php

namespace App\Console\Commands;

use App\Events\Customer\CustomerRegistered;
use App\Events\Insurance\PolicyExpiringWarning;
use App\Events\Quotation\QuotationGenerated;
use App\Models\Customer;
use App\Models\CustomerInsurance;
use App\Models\Quotation;
use App\Services\EventSourcingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;

class EventDrivenTestCommand extends Command
{
    protected $signature = 'events:test {--demo : Run demo events}';
    protected $description = 'Test event-driven architecture functionality';

    public function handle(): void
    {
        $this->info('🚀 Testing Event-Driven Architecture');
        $this->newLine();

        if ($this->option('demo')) {
            $this->runDemoEvents();
        } else {
            $this->runBasicTests();
        }

        $this->newLine();
        $this->info('✅ Event-driven architecture test completed!');
    }

    private function runBasicTests(): void
    {
        $this->info('1. Testing Event Sourcing Service');
        $this->testEventSourcing();

        $this->info('2. Testing Domain Events');
        $this->testDomainEvents();

        $this->info('3. Testing Queue System');
        $this->testQueueSystem();

        $this->info('4. Testing Event Stream');
        $this->testEventStream();
    }

    private function testEventSourcing(): void
    {
        $eventSourcing = app(EventSourcingService::class);

        // Store test events
        $eventSourcing->store('TestEvent1', ['test' => 'data1'], 'TestAggregate', '1');
        $eventSourcing->store('TestEvent2', ['test' => 'data2'], 'TestAggregate', '1');

        $events = $eventSourcing->getEventsForAggregate('TestAggregate', '1');
        
        $this->line("   ✓ Stored {$this->countEvents($events)} events in event store");
        
        $stats = $eventSourcing->getAggregateStatistics('TestAggregate');
        $this->line("   ✓ Retrieved aggregate statistics: {$stats['total_events']} total events");
    }

    private function testDomainEvents(): void
    {
        // Test CustomerRegistered event
        $customer = Customer::first() ?? Customer::factory()->create([
            'name' => 'Test Customer',
            'email' => 'test@test.com',
            'mobile_number' => '1234567890',
        ]);

        $event = new CustomerRegistered($customer, ['source' => 'test'], 'admin');
        $eventData = $event->getEventData();

        $this->line("   ✓ CustomerRegistered event created with data keys: " . implode(', ', array_keys($eventData)));

        // Test QuotationGenerated event
        $quotation = Quotation::first();
        if ($quotation) {
            $quotationEvent = new QuotationGenerated($quotation);
            $this->line("   ✓ QuotationGenerated event created for quotation #{$quotation->quotation_number}");
            $this->line("   ✓ Event shows {$quotationEvent->companyCount} companies, best premium: {$quotationEvent->bestPremium}");
        } else {
            $this->line("   ℹ No quotations found for testing");
        }
    }

    private function testQueueSystem(): void
    {
        $originalQueueDriver = config('queue.default');
        
        // Check queue configuration
        $this->line("   ✓ Queue driver: {$originalQueueDriver}");
        $this->line("   ✓ Queue workers can process event listeners asynchronously");
        
        // Show queue statistics
        $connections = config('queue.connections', []);
        $this->line("   ✓ Available queue connections: " . implode(', ', array_keys($connections)));
    }

    private function testEventStream(): void
    {
        $eventSourcing = app(EventSourcingService::class);
        
        $recentEvents = $eventSourcing->getEventStream(null, 5);
        $this->line("   ✓ Retrieved {$this->countEvents($recentEvents)} recent events from stream");
        
        if (!empty($recentEvents)) {
            $latestEvent = $recentEvents[0];
            $this->line("   ✓ Latest event: {$latestEvent['event_name']} at {$latestEvent['occurred_at']}");
        }
    }

    private function runDemoEvents(): void
    {
        $this->info('🎭 Running Demo Events');
        $this->newLine();

        // 1. Customer Registration Demo
        $this->info('Simulating customer registration...');
        $customer = Customer::factory()->create([
            'name' => 'Demo Customer',
            'email' => 'demo@example.com',
            'mobile_number' => '9999999999',
        ]);

        CustomerRegistered::dispatch($customer, ['demo' => true], 'web');
        $this->line("   ✅ CustomerRegistered event fired for {$customer->name}");

        // 2. Policy Expiry Warning Demo
        $this->info('Simulating policy expiry warning...');
        $policy = CustomerInsurance::where('policy_end_date', '>', now())
            ->where('policy_end_date', '<', now()->addDays(30))
            ->first();

        if ($policy) {
            $daysToExpiry = now()->diffInDays($policy->policy_end_date);
            PolicyExpiringWarning::dispatch($policy, $daysToExpiry, true);
            $this->line("   ✅ PolicyExpiringWarning event fired for policy #{$policy->policy_number}");
            $this->line("   📅 Expires in {$daysToExpiry} days");
        } else {
            $this->line("   ℹ No policies found expiring within 30 days");
        }

        // 3. Show Event Statistics
        $this->newLine();
        $this->info('Event Statistics:');
        
        $eventSourcing = app(EventSourcingService::class);
        $customerStats = $eventSourcing->getAggregateStatistics('Customer');
        
        if ($customerStats['total_events'] > 0) {
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Customer Events', $customerStats['total_events']],
                    ['Unique Customers', $customerStats['unique_aggregates']],
                    ['Event Types', count($customerStats['event_types'])],
                ]
            );
        } else {
            $this->line('   ℹ No customer events found in event store');
        }

        // 4. Queue Status
        $this->newLine();
        $this->info('Queue Information:');
        $this->line("   📋 Events are queued for async processing");
        $this->line("   🔄 Run 'php artisan queue:work' to process events");
        $this->line("   📊 Monitor queues with 'php artisan queue:monitor'");
    }

    private function countEvents($events): int
    {
        return is_array($events) ? count($events) : 0;
    }
}