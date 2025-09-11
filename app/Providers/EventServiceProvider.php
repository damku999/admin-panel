<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // Customer Events
        \App\Events\Customer\CustomerRegistered::class => [
            // SendWelcomeEmail is now handled synchronously in CustomerService
            \App\Listeners\Customer\CreateCustomerAuditLog::class,
            \App\Listeners\Customer\NotifyAdminOfRegistration::class,
        ],
        
        \App\Events\Customer\CustomerEmailVerified::class => [
            \App\Listeners\Customer\CreateCustomerAuditLog::class,
        ],
        
        \App\Events\Customer\CustomerProfileUpdated::class => [
            \App\Listeners\Customer\CreateCustomerAuditLog::class,
        ],
        
        // Quotation Events
        \App\Events\Quotation\QuotationRequested::class => [
            \App\Listeners\Customer\CreateCustomerAuditLog::class,
        ],
        
        \App\Events\Quotation\QuotationGenerated::class => [
            \App\Listeners\Quotation\GenerateQuotationPDF::class,
            \App\Listeners\Quotation\SendQuotationWhatsApp::class,
        ],
        
        // Insurance Policy Events
        \App\Events\Insurance\PolicyCreated::class => [
            \App\Listeners\Customer\CreateCustomerAuditLog::class,
        ],
        
        \App\Events\Insurance\PolicyRenewed::class => [
            \App\Listeners\Customer\CreateCustomerAuditLog::class,
        ],
        
        \App\Events\Insurance\PolicyExpiringWarning::class => [
            \App\Listeners\Insurance\SendPolicyRenewalReminder::class,
        ],
        
        // Communication Events
        \App\Events\Communication\WhatsAppMessageQueued::class => [
            \App\Listeners\Communication\ProcessWhatsAppMessage::class,
        ],
        
        \App\Events\Communication\EmailQueued::class => [
            \App\Listeners\Communication\ProcessEmailMessage::class,
        ],
        
        // Audit Events
        \App\Events\Audit\CustomerActionLogged::class => [
            // Future listeners for security monitoring, analytics, etc.
        ],
        
        // Document Events
        \App\Events\Document\PDFGenerationRequested::class => [
            // PDF generation will be handled by dedicated service
        ],
        
        // Legacy Events (to be phased out)
        \App\Events\CustomerCreated::class => [
            \App\Listeners\SendWelcomeEmail::class,
        ],
        \App\Events\PolicyExpiring::class => [
            \App\Listeners\SendPolicyReminderNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // Register global event listener for event sourcing
        $this->registerEventSourcingListener();
    }

    /**
     * Register a global listener to capture all events for event sourcing
     */
    private function registerEventSourcingListener(): void
    {
        Event::listen('*', function ($eventName, array $data) {
            // Only capture domain events, not framework events
            if ($this->isDomainEvent($eventName)) {
                $event = $data[0] ?? null;
                if ($event && method_exists($event, 'shouldQueue') && $event->shouldQueue()) {
                    // Dispatch to event sourcing
                    app(\App\Listeners\EventSourcing\StoreEventInEventStore::class)
                        ->handle($event);
                }
            }
        });
    }

    /**
     * Check if the event is a domain event that should be stored
     */
    private function isDomainEvent(string $eventName): bool
    {
        $domainEventNamespaces = [
            'App\\Events\\Customer\\',
            'App\\Events\\Quotation\\',
            'App\\Events\\Insurance\\',
            'App\\Events\\Communication\\',
            'App\\Events\\Document\\',
            'App\\Events\\Audit\\',
        ];

        foreach ($domainEventNamespaces as $namespace) {
            if (str_starts_with($eventName, $namespace)) {
                return true;
            }
        }

        return false;
    }
}
