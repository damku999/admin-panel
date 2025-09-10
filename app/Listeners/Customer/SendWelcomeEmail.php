<?php

namespace App\Listeners\Customer;

use App\Events\Customer\CustomerRegistered;
use App\Events\Communication\EmailQueued;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CustomerRegistered $event): void
    {
        $customer = $event->customer;
        
        // Queue welcome email
        EmailQueued::dispatch(
            recipientEmail: $customer->email,
            recipientName: $customer->name,
            subject: 'Welcome to Our Insurance Platform',
            emailType: 'welcome',
            emailData: [
                'customer_name' => $customer->name,
                'registration_date' => $event->customer->created_at->format('d/m/Y'),
                'next_steps' => [
                    'Complete your profile',
                    'Verify your email address',
                    'Request your first quotation',
                ],
                'support_contact' => config('mail.support_email', 'support@example.com'),
            ],
            priority: 3, // High priority for welcome emails
            referenceId: "customer_registration_{$customer->id}",
            customerId: $customer->id
        );
    }

    public function failed(CustomerRegistered $event, \Throwable $exception): void
    {
        \Log::error('Failed to send welcome email', [
            'customer_id' => $event->customer->id,
            'customer_email' => $event->customer->email,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}