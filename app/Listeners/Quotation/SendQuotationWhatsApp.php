<?php

namespace App\Listeners\Quotation;

use App\Events\Communication\WhatsAppMessageQueued;
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
        
        WhatsAppMessageQueued::dispatch(
            $customer->mobile,
            $message,
            'template',
            null,
            [
                'customer_name' => $customer->name,
                'quotation_number' => $quotation->quotation_number,
                'best_premium' => $event->bestPremium ? number_format($event->bestPremium, 2) : 'N/A',
                'company_count' => $event->companyCount,
                'policy_type' => $quotation->policy_type ?? 'Insurance',
                'portal_link' => route('customer.quotations.show', $quotation->id),
            ],
            $event->isHighValueQuotation() ? 2 : 5,
            "quotation_whatsapp_{$quotation->id}",
            $customer->id
        );
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