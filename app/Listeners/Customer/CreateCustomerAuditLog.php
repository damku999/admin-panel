<?php

namespace App\Listeners\Customer;

use App\Events\Customer\CustomerRegistered;
use App\Models\CustomerAuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateCustomerAuditLog implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CustomerRegistered $event): void
    {
        $customer = $event->customer;
        $eventData = $event->getEventData();
        
        CustomerAuditLog::create([
            'customer_id' => $customer->id,
            'action' => 'customer_registered',
            'description' => "Customer registered via {$event->registrationChannel}",
            'metadata' => [
                'registration_channel' => $event->registrationChannel,
                'registration_ip' => $eventData['registration_ip'],
                'user_agent' => $eventData['user_agent'],
                'event_metadata' => $event->metadata,
            ],
            'ip_address' => $eventData['registration_ip'],
            'user_agent' => $eventData['user_agent'],
            'created_at' => now(),
        ]);
    }

    public function failed(CustomerRegistered $event, \Throwable $exception): void
    {
        \Log::error('Failed to create customer audit log', [
            'customer_id' => $event->customer->id,
            'error' => $exception->getMessage(),
            'event_data' => $event->getEventData(),
        ]);
    }
}