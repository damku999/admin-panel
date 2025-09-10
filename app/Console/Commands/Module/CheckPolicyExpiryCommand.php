<?php

namespace App\Console\Commands\Module;

use App\Modules\Policy\Contracts\PolicyServiceInterface;
use Illuminate\Console\Command;

class CheckPolicyExpiryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:check-policy-expiry 
                           {--days=30 : Days ahead to check for expiring policies}
                           {--notify : Send notifications for expiring policies}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expiring insurance policies and optionally send notifications';

    public function __construct(
        private PolicyServiceInterface $policyService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $daysAhead = (int) $this->option('days');
        $notify = $this->option('notify');

        $this->info("🔍 Checking for policies expiring in the next {$daysAhead} days...");
        
        try {
            $expiringPolicies = $this->policyService->getExpiringPolicies($daysAhead);
            
            if ($expiringPolicies->isEmpty()) {
                $this->info('✅ No policies expiring in the specified period');
                return self::SUCCESS;
            }

            $this->warn("⚠️  Found {$expiringPolicies->count()} policies expiring soon:");

            // Display expiring policies
            $headers = ['Policy Number', 'Customer', 'Company', 'End Date', 'Days Left'];
            $rows = [];

            foreach ($expiringPolicies as $policy) {
                $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($policy->end_date));
                
                $rows[] = [
                    $policy->policy_number,
                    $policy->customer->name,
                    $policy->insuranceCompany->name,
                    $policy->end_date,
                    $daysLeft . ' days',
                ];
            }

            $this->table($headers, $rows);

            // Send notifications if requested
            if ($notify) {
                $this->info('📧 Sending expiry notifications...');
                $notificationsSent = $this->sendExpiryNotifications($expiringPolicies);
                $this->info("✅ Sent {$notificationsSent} expiry notifications");
            } else {
                $this->info('💡 Use --notify flag to send notifications to customers');
            }

            // Display statistics
            $this->displayExpiryStatistics($expiringPolicies);

            return self::SUCCESS;

        } catch (\Throwable $e) {
            $this->error('❌ Failed to check policy expiry: ' . $e->getMessage());
            
            if ($this->getOutput()->isVerbose()) {
                $this->line($e->getTraceAsString());
            }
            
            return self::FAILURE;
        }
    }

    private function sendExpiryNotifications($policies): int
    {
        $notificationsSent = 0;
        $notificationService = app(\App\Modules\Notification\Contracts\NotificationServiceInterface::class);

        foreach ($policies as $policy) {
            try {
                $customer = $policy->customer;
                $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($policy->end_date));
                
                // Send WhatsApp notification
                if ($customer->mobile_number) {
                    $message = $this->generateExpiryWhatsAppMessage($policy, $daysLeft);
                    
                    $queued = $notificationService->queueNotification(
                        'whatsapp',
                        ['phone' => $customer->mobile_number],
                        ['message' => $message],
                        2 // High priority
                    );
                    
                    if ($queued) {
                        $notificationsSent++;
                    }
                }

                // Send email notification
                if ($customer->email) {
                    $emailContent = $this->generateExpiryEmailContent($policy, $daysLeft);
                    
                    $queued = $notificationService->queueNotification(
                        'email',
                        ['email' => $customer->email],
                        [
                            'subject' => "Policy Renewal Reminder - {$policy->policy_number}",
                            'body' => $emailContent
                        ],
                        2 // High priority
                    );
                    
                    if ($queued) {
                        $notificationsSent++;
                    }
                }

            } catch (\Throwable $e) {
                $this->warn("Failed to queue notification for policy {$policy->policy_number}: " . $e->getMessage());
            }
        }

        return $notificationsSent;
    }

    private function generateExpiryWhatsAppMessage($policy, int $daysLeft): string
    {
        $urgency = match (true) {
            $daysLeft <= 3 => '🚨 *URGENT*',
            $daysLeft <= 7 => '⚠️ *IMPORTANT*',
            default => '📅 *REMINDER*'
        };

        return "{$urgency} - Policy Renewal Reminder\n\n" .
               "Dear *{$policy->customer->name}*,\n\n" .
               "Your insurance policy is expiring soon:\n\n" .
               "🚗 *Vehicle*: {$policy->make_model_variant}\n" .
               "📝 *Policy*: {$policy->policy_number}\n" .
               "🏢 *Company*: {$policy->insuranceCompany->name}\n" .
               "📅 *Expires*: " . \Carbon\Carbon::parse($policy->end_date)->format('d M Y') . "\n" .
               "⏰ *Days Left*: {$daysLeft} days\n\n" .
               "To avoid any gap in coverage, please contact us immediately for renewal.\n\n" .
               "📞 *Call*: +91 98765 43210\n" .
               "💬 *Reply* to this message\n\n" .
               "Best regards,\n" .
               "*Parth Rawal*\n" .
               "Your Insurance Advisor\n" .
               "\"Think of Insurance, Think of Us\"";
    }

    private function generateExpiryEmailContent($policy, int $daysLeft): string
    {
        $urgencyClass = match (true) {
            $daysLeft <= 3 => 'urgent',
            $daysLeft <= 7 => 'warning',
            default => 'info'
        };

        return "
        <html>
        <head>
            <style>
                .urgent { background: #dc3545; color: white; }
                .warning { background: #ffc107; color: #212529; }
                .info { background: #17a2b8; color: white; }
                .alert { padding: 15px; border-radius: 5px; text-align: center; margin: 20px 0; }
            </style>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div class='alert {$urgencyClass}'>
                    <h2 style='margin: 0;'>Policy Renewal Reminder</h2>
                    <p style='margin: 5px 0 0 0;'>Your policy expires in {$daysLeft} days</p>
                </div>
                
                <p>Dear <strong>{$policy->customer->name}</strong>,</p>
                
                <p>This is an important reminder that your insurance policy is expiring soon. To ensure continuous coverage, please renew your policy before the expiry date.</p>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='color: #495057; margin-top: 0;'>Policy Details:</h3>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr><td style='padding: 8px 0; font-weight: bold;'>Vehicle:</td><td>{$policy->make_model_variant}</td></tr>
                        <tr><td style='padding: 8px 0; font-weight: bold;'>Policy Number:</td><td>{$policy->policy_number}</td></tr>
                        <tr><td style='padding: 8px 0; font-weight: bold;'>Insurance Company:</td><td>{$policy->insuranceCompany->name}</td></tr>
                        <tr><td style='padding: 8px 0; font-weight: bold;'>Expiry Date:</td><td>" . \Carbon\Carbon::parse($policy->end_date)->format('d M Y') . "</td></tr>
                        <tr><td style='padding: 8px 0; font-weight: bold;'>Days Remaining:</td><td style='color: #dc3545; font-weight: bold;'>{$daysLeft} days</td></tr>
                    </table>
                </div>
                
                <p><strong>Why renew early?</strong></p>
                <ul>
                    <li>Avoid gaps in insurance coverage</li>
                    <li>Maintain your No Claim Bonus (NCB)</li>
                    <li>Compare rates and get the best deal</li>
                    <li>Peace of mind with continuous protection</li>
                </ul>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='tel:+919876543210' style='display: inline-block; background: #007bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 5px;'>📞 Call Now</a>
                    <a href='https://parthrawal.in' style='display: inline-block; background: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 5px;'>🌐 Get Quote</a>
                </div>
                
                <p>For immediate assistance, please contact us:</p>
                <p>📞 <strong>Phone:</strong> +91 98765 43210<br>
                   📧 <strong>Email:</strong> info@parthrawal.in<br>
                   🌐 <strong>Website:</strong> https://parthrawal.in</p>
                
                <div style='border-top: 1px solid #dee2e6; padding-top: 20px; margin-top: 30px; text-align: center; color: #6c757d;'>
                    <p>Best regards,<br>
                    <strong>Parth Rawal</strong><br>
                    Your Trusted Insurance Advisor</p>
                    
                    <p style='font-style: italic; margin-top: 15px;'>\"Think of Insurance, Think of Us\"</p>
                </div>
            </div>
        </body>
        </html>";
    }

    private function displayExpiryStatistics($policies): void
    {
        $this->newLine();
        $this->info('📊 Expiry Statistics:');
        
        $stats = $policies->groupBy(function ($policy) {
            $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($policy->end_date));
            
            return match (true) {
                $daysLeft <= 7 => 'Critical (≤7 days)',
                $daysLeft <= 15 => 'Urgent (8-15 days)', 
                default => 'Upcoming (16+ days)'
            };
        })->map->count();

        foreach ($stats as $category => $count) {
            $this->line("• {$category}: {$count} policies");
        }
    }
}