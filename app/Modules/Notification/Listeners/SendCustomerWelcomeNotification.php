<?php

namespace App\Modules\Notification\Listeners;

use App\Events\Customer\CustomerRegistered;
use App\Modules\Notification\Contracts\NotificationServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendCustomerWelcomeNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private NotificationServiceInterface $notificationService
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(CustomerRegistered $event): void
    {
        $customer = $event->customer;
        $metadata = $event->metadata;

        try {
            // Send welcome WhatsApp message
            if ($customer->mobile_number) {
                $whatsappMessage = $this->generateWelcomeWhatsAppMessage($customer);
                
                $this->notificationService->queueNotification(
                    'whatsapp',
                    ['phone' => $customer->mobile_number],
                    ['message' => $whatsappMessage],
                    3 // High priority for welcome messages
                );
            }

            // Send welcome email
            if ($customer->email) {
                $emailContent = $this->generateWelcomeEmailContent($customer);
                
                $this->notificationService->queueNotification(
                    'email',
                    ['email' => $customer->email],
                    [
                        'subject' => 'Welcome to Our Insurance Services!',
                        'body' => $emailContent['body'],
                        'attachments' => $emailContent['attachments'] ?? null
                    ],
                    3 // High priority
                );
            }

            Log::info('Customer welcome notifications queued', [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'has_whatsapp' => !empty($customer->mobile_number),
                'has_email' => !empty($customer->email)
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to queue customer welcome notifications', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw to trigger queue retry
            throw $e;
        }
    }

    private function generateWelcomeWhatsAppMessage($customer): string
    {
        $customerType = $customer->type === 'Corporate' ? 'Corporate' : 'Personal';
        
        return "🙏 *Welcome to Our Insurance Family!*\n\n" .
               "Dear *{$customer->name}*,\n\n" .
               "Thank you for choosing us as your trusted insurance advisor!\n\n" .
               "🔹 Your {$customerType} account has been successfully created\n" .
               "🔹 You can now get instant insurance quotations\n" .
               "🔹 Compare multiple insurance companies\n" .
               "🔹 Get the best rates for your needs\n\n" .
               "📞 *Need help?* Just reply to this message\n" .
               "🌐 *Visit:* https://parthrawal.in\n\n" .
               "*Think of Insurance, Think of Us*\n\n" .
               "Best regards,\n" .
               "Parth Rawal\n" .
               "Your Insurance Advisor";
    }

    private function generateWelcomeEmailContent($customer): array
    {
        $customerType = $customer->type === 'Corporate' ? 'Corporate' : 'Personal';
        
        $body = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 10px; color: white; text-align: center; margin-bottom: 30px;'>
                    <h1 style='margin: 0; font-size: 28px;'>Welcome to Our Insurance Family!</h1>
                    <p style='margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;'>Your Trusted Insurance Advisor</p>
                </div>
                
                <div style='padding: 0 20px;'>
                    <p style='font-size: 18px; color: #667eea;'>Dear <strong>{$customer->name}</strong>,</p>
                    
                    <p>Thank you for choosing us as your trusted insurance advisor! We're excited to help you find the best insurance solutions for your needs.</p>
                    
                    <div style='background: #f8f9ff; padding: 25px; border-radius: 10px; border-left: 4px solid #667eea; margin: 25px 0;'>
                        <h3 style='color: #667eea; margin-top: 0;'>Your {$customerType} Account Benefits:</h3>
                        <ul style='margin: 0; padding-left: 20px;'>
                            <li>Instant insurance quotations</li>
                            <li>Compare multiple insurance companies</li>
                            <li>Get the best rates tailored to your needs</li>
                            <li>Expert guidance throughout the process</li>
                            <li>24/7 customer support</li>
                        </ul>
                    </div>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='https://parthrawal.in' style='display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Visit Our Website</a>
                    </div>
                    
                    <p>If you have any questions or need assistance, please don't hesitate to reach out to us. We're here to help!</p>
                    
                    <div style='border-top: 1px solid #eee; padding-top: 20px; margin-top: 30px; text-align: center; color: #666;'>
                        <p><strong>Parth Rawal</strong><br>
                        Your Insurance Advisor<br>
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