<?php

namespace App\Modules\Notification\Listeners;

use App\Events\Quotation\QuotationGenerated;
use App\Modules\Notification\Contracts\NotificationServiceInterface;
use App\Services\PdfGenerationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendQuotationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private NotificationServiceInterface $notificationService,
        private PdfGenerationService $pdfService
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(QuotationGenerated $event): void
    {
        $quotation = $event->quotation;
        $customer = $quotation->customer;

        try {
            // Generate PDF for attachment
            $pdfPath = null;
            if ($quotation->quotationCompanies()->count() > 0) {
                $pdfPath = $this->pdfService->generateQuotationPdfForWhatsApp($quotation);
            }

            // Send WhatsApp notification with PDF
            if ($customer->mobile_number || $quotation->whatsapp_number) {
                $phoneNumber = $quotation->whatsapp_number ?: $customer->mobile_number;
                $whatsappMessage = $this->generateQuotationWhatsAppMessage($quotation);
                
                $this->notificationService->queueNotification(
                    'whatsapp',
                    ['phone' => $phoneNumber],
                    [
                        'message' => $whatsappMessage,
                        'attachments' => $pdfPath ? [$pdfPath] : null
                    ],
                    2 // High priority for quotations
                );
            }

            // Send email notification with PDF
            if ($customer->email) {
                $emailContent = $this->generateQuotationEmailContent($quotation);
                
                $this->notificationService->queueNotification(
                    'email',
                    ['email' => $customer->email],
                    [
                        'subject' => "Your Insurance Quotation is Ready - {$quotation->make_model_variant}",
                        'body' => $emailContent['body'],
                        'attachments' => $pdfPath ? [$pdfPath] : null
                    ],
                    2 // High priority
                );
            }

            Log::info('Quotation notifications queued', [
                'quotation_id' => $quotation->id,
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'companies_count' => $quotation->quotationCompanies()->count(),
                'has_pdf' => !is_null($pdfPath)
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to queue quotation notifications', [
                'quotation_id' => $quotation->id,
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw to trigger queue retry
            throw $e;
        }
    }

    private function generateQuotationWhatsAppMessage($quotation): string
    {
        $customer = $quotation->customer;
        $quotes = $quotation->quotationCompanies()->orderBy('final_premium')->get();
        $bestQuote = $quotes->first();

        $message = "🚗 *Insurance Quotation Ready*\n\n";
        $message .= "Dear *{$customer->name}*,\n\n";
        $message .= "Your insurance quotation is ready! We have compared *{$quotes->count()} insurance companies* for you.\n\n";

        $message .= "🚙 *Vehicle Details:*\n";
        $message .= "• Vehicle: *{$quotation->make_model_variant}*\n";
        $message .= "• Registration: *{$quotation->vehicle_number}*\n";
        $message .= "• IDV: *₹" . number_format($quotation->total_idv) . "*\n";
        $message .= "• Policy: *{$quotation->policy_type}* - {$quotation->policy_tenure_years} Year(s)\n\n";

        if ($bestQuote) {
            $message .= "💰 *Best Premium:*\n";
            $message .= "• *{$bestQuote->insuranceCompany->name}*\n";
            $message .= "• Premium: *₹" . number_format($bestQuote->final_premium, 2) . "*\n\n";
        }

        $message .= "📊 *Premium Comparison:*\n";
        foreach ($quotes->take(5) as $index => $quote) {
            $icon = $quote->is_recommended ? '⭐' : ($index + 1);
            $ranking = is_numeric($icon) ? "{$icon}." : $icon;
            $message .= "{$ranking} *{$quote->insuranceCompany->name}*: ₹" . number_format($quote->final_premium, 2);
            if ($quote->is_recommended) {
                $message .= " _(Recommended)_";
            }
            $message .= "\n";
        }

        // Calculate savings if more than one quote
        if ($quotes->count() > 1) {
            $highestQuote = $quotes->last();
            $savings = $highestQuote->final_premium - $bestQuote->final_premium;
            if ($savings > 0) {
                $message .= "\n💵 *You can save up to ₹" . number_format($savings) . "*\n";
            }
        }

        $message .= "\n📎 *Detailed PDF comparison attached*";
        $message .= "\n\n📞 For any queries or to proceed with purchase, just reply to this message.";
        $message .= "\n\nBest regards,";
        $message .= "\n*Parth Rawal*";
        $message .= "\nYour Trusted Insurance Advisor";
        $message .= "\n\"Think of Insurance, Think of Us.\"";

        return $message;
    }

    private function generateQuotationEmailContent($quotation): array
    {
        $customer = $quotation->customer;
        $quotes = $quotation->quotationCompanies()->orderBy('final_premium')->get();
        $bestQuote = $quotes->first();

        $body = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 700px; margin: 0 auto; padding: 20px;'>
                <div style='background: linear-gradient(135deg, #2196F3 0%, #21CBF3 100%); padding: 30px; border-radius: 10px; color: white; text-align: center; margin-bottom: 30px;'>
                    <h1 style='margin: 0; font-size: 28px;'>🚗 Your Insurance Quotation is Ready!</h1>
                    <p style='margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;'>Compare {$quotes->count()} Insurance Companies</p>
                </div>
                
                <div style='padding: 0 20px;'>
                    <p style='font-size: 18px; color: #2196F3;'>Dear <strong>{$customer->name}</strong>,</p>
                    
                    <p>Great news! We've prepared your comprehensive insurance quotation comparing multiple insurance companies to get you the best deal.</p>
                    
                    <div style='background: #f8f9ff; padding: 25px; border-radius: 10px; border-left: 4px solid #2196F3; margin: 25px 0;'>
                        <h3 style='color: #2196F3; margin-top: 0;'>🚙 Vehicle Details:</h3>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr><td style='padding: 5px 0; font-weight: bold;'>Vehicle:</td><td>{$quotation->make_model_variant}</td></tr>
                            <tr><td style='padding: 5px 0; font-weight: bold;'>Registration:</td><td>{$quotation->vehicle_number}</td></tr>
                            <tr><td style='padding: 5px 0; font-weight: bold;'>IDV:</td><td>₹" . number_format($quotation->total_idv) . "</td></tr>
                            <tr><td style='padding: 5px 0; font-weight: bold;'>Policy Type:</td><td>{$quotation->policy_type} - {$quotation->policy_tenure_years} Year(s)</td></tr>
                        </table>
                    </div>";

        if ($bestQuote) {
            $body .= "
                    <div style='background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); padding: 25px; border-radius: 10px; color: white; text-align: center; margin: 25px 0;'>
                        <h3 style='margin-top: 0;'>💰 Best Premium Found!</h3>
                        <p style='font-size: 24px; margin: 10px 0;'><strong>₹" . number_format($bestQuote->final_premium, 2) . "</strong></p>
                        <p style='margin-bottom: 0; opacity: 0.9;'>{$bestQuote->insuranceCompany->name}</p>
                    </div>";
        }

        $body .= "
                    <div style='margin: 25px 0;'>
                        <h3 style='color: #2196F3;'>📊 Premium Comparison:</h3>
                        <table style='width: 100%; border-collapse: collapse; border: 1px solid #ddd;'>
                            <thead>
                                <tr style='background: #f5f5f5;'>
                                    <th style='padding: 12px; text-align: left; border-bottom: 1px solid #ddd;'>Rank</th>
                                    <th style='padding: 12px; text-align: left; border-bottom: 1px solid #ddd;'>Insurance Company</th>
                                    <th style='padding: 12px; text-align: right; border-bottom: 1px solid #ddd;'>Premium</th>
                                    <th style='padding: 12px; text-align: center; border-bottom: 1px solid #ddd;'>Status</th>
                                </tr>
                            </thead>
                            <tbody>";

        foreach ($quotes->take(5) as $index => $quote) {
            $rowStyle = $quote->is_recommended ? 'background: #e8f5e8;' : '';
            $icon = $quote->is_recommended ? '⭐' : ($index + 1);
            $status = $quote->is_recommended ? 'Recommended' : '';
            
            $body .= "
                                <tr style='{$rowStyle}'>
                                    <td style='padding: 12px; border-bottom: 1px solid #eee;'>{$icon}</td>
                                    <td style='padding: 12px; border-bottom: 1px solid #eee; font-weight: bold;'>{$quote->insuranceCompany->name}</td>
                                    <td style='padding: 12px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold;'>₹" . number_format($quote->final_premium, 2) . "</td>
                                    <td style='padding: 12px; border-bottom: 1px solid #eee; text-align: center; color: #4CAF50;'>{$status}</td>
                                </tr>";
        }

        $body .= "
                            </tbody>
                        </table>
                    </div>";

        // Calculate savings
        if ($quotes->count() > 1) {
            $highestQuote = $quotes->last();
            $savings = $highestQuote->final_premium - $bestQuote->final_premium;
            if ($savings > 0) {
                $body .= "
                    <div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center;'>
                        <h4 style='color: #856404; margin: 0 0 10px 0;'>💵 Potential Savings</h4>
                        <p style='color: #856404; margin: 0; font-size: 18px;'><strong>You can save up to ₹" . number_format($savings) . "</strong></p>
                    </div>";
            }
        }

        $body .= "
                    <p>📎 <strong>Detailed PDF comparison is attached</strong> with complete breakdown of premiums, add-on covers, and benefits.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <p>Ready to proceed? We're here to help!</p>
                        <a href='tel:+919876543210' style='display: inline-block; background: #2196F3; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 5px; font-weight: bold;'>📞 Call Now</a>
                        <a href='https://parthrawal.in' style='display: inline-block; background: #4CAF50; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 5px; font-weight: bold;'>🌐 Visit Website</a>
                    </div>
                    
                    <div style='border-top: 1px solid #eee; padding-top: 20px; margin-top: 30px; text-align: center; color: #666;'>
                        <p><strong>Parth Rawal</strong><br>
                        Your Trusted Insurance Advisor<br>
                        📧 Email: info@parthrawal.in<br>
                        🌐 Website: https://parthrawal.in</p>
                        
                        <p style='font-style: italic; color: #999; margin-top: 20px;'>
                            \"Think of Insurance, Think of Us\"
                        </p>
                    </div>
                </div>
            </div>
        </body>
        </html>";

        return [
            'body' => $body,
            'attachments' => null
        ];
    }
}