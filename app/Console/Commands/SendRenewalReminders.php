<?php

namespace App\Console\Commands;

use App\Models\CustomerInsurance;
use App\Services\AppSettingService;
use App\Traits\WhatsAppApiTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendRenewalReminders extends Command
{
    use WhatsAppApiTrait;

    protected $signature = 'send:renewal-reminders';
    protected $description = 'Send WhatsApp reminders for insurance renewals expiring today or within a month.';

    /**
     * Execute the console command.
     *
     * Sends renewal reminders to customers whose insurances are expiring today or within a month.
     *
     * @return void
     */
    public function handle()
    {
        // Check if notifications are enabled
        $whatsappEnabled = AppSettingService::get('enable_whatsapp_notifications', 'true') === 'true';
        $emailEnabled = AppSettingService::get('enable_email_notifications', 'true') === 'true';

        if (!$whatsappEnabled && !$emailEnabled) {
            $this->info('Notifications are disabled. Skipping renewal reminders.');
            return;
        }

        $currentDate = Carbon::now();
        $reminderDays = (int) AppSettingService::get('renewal_reminder_days_before', 30);

        // Dynamic date ranges based on settings
        $reminderDates = [];
        $reminderIntervals = [5, 10, 15, $reminderDays];

        foreach ($reminderIntervals as $days) {
            if ($days <= $reminderDays) {
                $reminderDates[] = $currentDate->copy()->addDays($days)->startOfDay();
            }
        }

        $insurances = CustomerInsurance::where(function ($query) use ($reminderDates) {
            foreach ($reminderDates as $date) {
                $query->orWhereDate('expired_date', $date);
            }
        })
            ->where('is_renewed', 0)
            ->where('status', 1)
            ->get();

        $sent = 0;
        foreach ($insurances->chunk(100) as $chunkedInurances) {
            foreach ($chunkedInurances as $insurance) {
                $messageText = $insurance->premiumType->is_vehicle == 1 
                    ? $this->renewalReminderVehicle($insurance) 
                    : $this->renewalReminder($insurance);
                $receiverId = $insurance->customer->mobile_number;

                // Send WhatsApp notification if enabled
                if ($whatsappEnabled) {
                    $this->whatsAppSendMessage($messageText, $receiverId);
                    $sent++;
                }

                // TODO: Add email notification here if email is enabled
                // if ($emailEnabled) {
                //     // Send email notification
                // }
            }
        }

        $this->info("Renewal reminders processed. {$sent} WhatsApp messages sent to customers with policies expiring in the next {$reminderDays} days.");
    }
}
