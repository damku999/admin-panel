<?php

namespace App\Listeners\Insurance;

use Illuminate\Support\Facades\Mail;
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
        
        // Send email directly
        if ($customer->email) {
            $subject = $this->getEmailSubject($event);
            $message = $this->getEmailMessage($event);

            Mail::raw($message, function($mail) use ($customer, $subject) {
                $mail->to($customer->email)
                     ->subject($subject);
            });
        }
    }

    private function sendWhatsAppReminder(PolicyExpiringWarning $event): void
    {
        $policy = $event->policy;
        $customer = $policy->customer;

        // Send WhatsApp message directly using actual WhatsApp API
        if ($customer->mobile) {
            $message = $this->getWhatsAppMessage($event);
            // WhatsApp service integration placeholder
            // WhatsAppService::sendMessage($customer->mobile, $message);

            // For development: just skip sending
            // Remove this when WhatsApp service is implemented
        }
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

    private function getEmailMessage(PolicyExpiringWarning $event): string
    {
        $policy = $event->policy;
        $customer = $policy->customer;

        return "Dear {$customer->name},\n\n" .
               "This is a reminder that your insurance policy is expiring soon:\n\n" .
               "Policy Number: {$policy->policy_number}\n" .
               "Policy Type: " . ($policy->policyType->name ?? 'Insurance Policy') . "\n" .
               "Insurance Company: " . ($policy->insuranceCompany->name ?? 'Insurance Company') . "\n" .
               "Expiry Date: " . ($policy->policy_end_date?->format('d/m/Y') ?? 'N/A') . "\n" .
               "Days to Expiry: {$event->daysToExpiry}\n\n" .
               "Please contact us to renew your policy and avoid any coverage gaps.\n\n" .
               "Best regards,\nYour Insurance Team";
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