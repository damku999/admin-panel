<?php

namespace App\Listeners\Quotation;

use Illuminate\Support\Facades\Mail;
use App\Events\Quotation\QuotationGenerated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendQuotationWhatsApp implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(QuotationGenerated $event): void
    {
        $quotation = $event->quotation;
        $customer = $quotation->customer;
        
        // Only send if customer has mobile number
        if (empty($customer->mobile)) {
            return;
        }

        // Prepare WhatsApp message
        $message = $this->prepareWhatsAppMessage($quotation, $event);
        
        // Send WhatsApp message directly using actual WhatsApp API
        if ($customer->mobile) {
            // WhatsApp service integration placeholder
            // WhatsAppService::sendMessage($customer->mobile, $message);

            // For development: just skip sending
            // Remove this when WhatsApp service is implemented
        }
    }

    private function prepareWhatsAppMessage($quotation, $event): string
    {
        $customer = $quotation->customer;
        $bestPremium = $event->bestPremium ? '₹' . number_format($event->bestPremium, 2) : 'Please contact us';
        
        return "Hi {$customer->name}! 🎉\n\n" .
               "Your quotation #{$quotation->quotation_number} is ready!\n\n" .
               "📋 Policy: {$quotation->policy_type}\n" .
               "💰 Best Premium: {$bestPremium}\n" .
               "🏢 Companies: {$event->companyCount}\n\n" .
               "View detailed quotation: " . route('customer.quotations.show', $quotation->id) . "\n\n" .
               "Need help? Reply to this message or call us.\n\n" .
               "Thank you for choosing us! 🙏";
    }

    public function failed(QuotationGenerated $event, \Throwable $exception): void
    {
        \Log::error('Failed to send quotation WhatsApp', [
            'quotation_id' => $event->quotation->id,
            'customer_id' => $event->quotation->customer_id,
            'customer_mobile' => $event->quotation->customer->mobile,
            'error' => $exception->getMessage(),
        ]);
    }
}