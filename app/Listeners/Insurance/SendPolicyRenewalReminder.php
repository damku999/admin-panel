<?php

namespace App\Listeners\Insurance;

use App\Events\Communication\EmailQueued;
use App\Events\Communication\WhatsAppMessageQueued;
use App\Events\Insurance\PolicyExpiringWarning;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPolicyRenewalReminder implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PolicyExpiringWarning $event): void
    {
        $policy = $event->policy;
        $customer = $policy->customer;
        
        // Send email reminder if customer has email
        if ($event->shouldSendEmail()) {
            $this->sendEmailReminder($event);
        }
        
        // Send WhatsApp reminder if appropriate
        if ($event->shouldSendWhatsApp()) {
            $this->sendWhatsAppReminder($event);
        }
    }

    private function sendEmailReminder(PolicyExpiringWarning $event): void
    {
        $policy = $event->policy;
        $customer = $policy->customer;
        
        EmailQueued::dispatch(
            recipientEmail: $customer->email,
            recipientName: $customer->name,
            subject: $this->getEmailSubject($event),
            emailType: 'renewal_reminder',
            emailData: [
                'customer_name' => $customer->name,
                'policy_number' => $policy->policy_number,
                'policy_type' => $policy->policyType->name ?? 'Insurance Policy',
                'insurance_company' => $policy->insuranceCompany->name ?? 'Insurance Company',
                'expiry_date' => $policy->policy_end_date?->format('d/m/Y'),
                'days_to_expiry' => $event->daysToExpiry,
                'warning_type' => $event->warningType,
                'current_premium' => number_format($policy->premium_amount, 2),
                'sum_assured' => number_format($policy->sum_assured, 2),
                'renewal_url' => route('customer.policies.renew', $policy->id),
                'contact_url' => route('customer.contact'),
            ],
            priority: $event->isUrgent() ? 2 : 4,
            referenceId: "renewal_reminder_email_{$policy->id}_{$event->daysToExpiry}",
            customerId: $customer->id
        );
    }

    private function sendWhatsAppReminder(PolicyExpiringWarning $event): void
    {
        $policy = $event->policy;
        $customer = $policy->customer;
        
        WhatsAppMessageQueued::dispatch(
            phoneNumber: $customer->mobile,
            message: $this->getWhatsAppMessage($event),
            messageType: 'template',
            templateData: [
                'customer_name' => $customer->name,
                'policy_number' => $policy->policy_number,
                'expiry_date' => $policy->policy_end_date?->format('d/m/Y'),
                'days_to_expiry' => $event->daysToExpiry,
                'urgency_emoji' => $event->isUrgent() ? '🚨' : '⏰',
                'renewal_link' => route('customer.policies.renew', $policy->id),
            ],
            priority: $event->isUrgent() ? 1 : 3,
            referenceId: "renewal_reminder_whatsapp_{$policy->id}_{$event->daysToExpiry}",
            customerId: $customer->id
        );
    }

    private function getEmailSubject(PolicyExpiringWarning $event): string
    {
        return match ($event->warningType) {
            'urgent' => "🚨 URGENT: Policy expires in {$event->daysToExpiry} days",
            'important' => "⏰ Important: Policy renewal required",
            'early' => "📋 Policy Renewal Notice",
            default => "📋 Policy Renewal Reminder"
        };
    }

    private function getWhatsAppMessage(PolicyExpiringWarning $event): string
    {
        $policy = $event->policy;
        $customer = $policy->customer;
        $emoji = $event->isUrgent() ? '🚨' : '⏰';
        
        return "{$emoji} Hi {$customer->name}!\n\n" .
               "Your insurance policy is expiring soon:\n\n" .
               "📋 Policy: {$policy->policy_number}\n" .
               "📅 Expires: {$policy->policy_end_date?->format('d/m/Y')}\n" .
               "⏳ Days left: {$event->daysToExpiry}\n\n" .
               "Renew now to avoid coverage gap:\n" .
               route('customer.policies.renew', $policy->id) . "\n\n" .
               "Need help? Reply to this message.\n\n" .
               "Stay protected! 🛡️";
    }

    public function failed(PolicyExpiringWarning $event, \Throwable $exception): void
    {
        \Log::error('Failed to send policy renewal reminder', [
            'policy_id' => $event->policy->id,
            'customer_id' => $event->policy->customer_id,
            'days_to_expiry' => $event->daysToExpiry,
            'warning_type' => $event->warningType,
            'error' => $exception->getMessage(),
        ]);
    }
}