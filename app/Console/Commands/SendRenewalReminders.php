<?php

namespace App\Console\Commands;

use App\Models\CustomerInsurance;
use App\Traits\WhatsAppApiTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendRenewalReminders extends Command
{
    use WhatsAppApiTrait;

    protected $signature = 'send:renewal-reminders';
    protected $description = 'Send WhatsApp reminders for insurance renewals based on configured reminder days.';

    /**
     * Execute the console command.
     *
     * Sends renewal reminders to customers whose insurances are expiring on configured days.
     * Days are configurable via App Settings (default: 30,15,7,1)
     *
     * @return void
     */
    public function handle()
    {
        $currentDate = Carbon::now();

        // Get reminder days from app settings (e.g., 30,15,7,1)
        $reminderDays = get_renewal_reminder_days();

        $this->info('Checking for renewals expiring in: ' . implode(', ', $reminderDays) . ' days');

        // Build query dynamically based on configured days
        $insurances = CustomerInsurance::where(function ($query) use ($currentDate, $reminderDays) {
            foreach ($reminderDays as $days) {
                $targetDate = $currentDate->copy()->addDays($days)->startOfDay();
                $query->orWhereDate('expired_date', $targetDate);
            }
        })
            ->where('is_renewed', 0)
            ->where('status', 1)
            ->get();

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($insurances->chunk(100) as $chunkedInurances) {
            foreach ($chunkedInurances as $insurance) {
                $receiverId = $insurance->customer->mobile_number;
                $customerId = $insurance->customer->id;

                // Determine notification type code based on days until expiry
                $daysUntilExpiry = Carbon::now()->diffInDays(Carbon::parse($insurance->expired_date), false);
                $notificationTypeCode = $this->getNotificationTypeCode($daysUntilExpiry);

                // Prepare template data
                $templateData = [
                    'customer_name' => $insurance->customer->name,
                    'policy_number' => $insurance->policy_no,
                    'policy_type' => $insurance->policyType->name ?? 'Insurance',
                    'insurance_company' => $insurance->insuranceCompany->name ?? 'Insurance Company',
                    'expiry_date' => Carbon::parse($insurance->expired_date)->format('d-M-Y'),
                    'days_remaining' => (string)$daysUntilExpiry,
                    'premium_amount' => '₹' . number_format($insurance->premium_amount ?? 0, 0),
                    'vehicle_number' => $insurance->registration_no ?? 'N/A',
                    'advisor_name' => 'Parth Rawal',
                    'company_website' => 'https://parthrawal.in',
                    'company_phone' => '+91 97277 93123',
                ];

                try {
                    // Try to get message from template, fallback to old method
                    $messageText = $this->getMessageFromTemplate($notificationTypeCode, $templateData);

                    if (!$messageText) {
                        // Fallback to old hardcoded method
                        $messageText = $insurance->premiumType->is_vehicle == 1
                            ? $this->renewalReminderVehicle($insurance)
                            : $this->renewalReminder($insurance);
                    }

                    $this->whatsAppSendMessage($messageText, $receiverId, $customerId, $notificationTypeCode);
                    $sentCount++;
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder for insurance #{$insurance->id}: " . $e->getMessage());
                    $skippedCount++;
                }
            }
        }

        $this->info("Renewal reminders sent successfully!");
        $this->info("Total found: {$insurances->count()}, Sent: {$sentCount}, Skipped: {$skippedCount}");
    }

    /**
     * Get notification type code based on days until expiry.
     *
     * @param int $daysUntilExpiry
     * @return string
     */
    private function getNotificationTypeCode(int $daysUntilExpiry): string
    {
        if ($daysUntilExpiry >= 25 && $daysUntilExpiry <= 35) {
            return 'renewal_30_days';
        } elseif ($daysUntilExpiry >= 12 && $daysUntilExpiry <= 18) {
            return 'renewal_15_days';
        } elseif ($daysUntilExpiry >= 5 && $daysUntilExpiry <= 9) {
            return 'renewal_7_days';
        } elseif ($daysUntilExpiry >= 0 && $daysUntilExpiry <= 2) {
            return 'renewal_expired';
        }

        // Default to 30 days if no exact match
        return 'renewal_30_days';
    }
}