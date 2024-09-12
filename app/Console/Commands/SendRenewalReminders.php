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
        $currentDate = Carbon::now();

        // Calculate the target dates
        $dates = [
            '5_days' => $currentDate->copy()->addDays(5),
            '10_days' => $currentDate->copy()->addDays(10),
            '15_days' => $currentDate->copy()->addDays(15),
            '30_days' => $currentDate->copy()->addDays(30),
        ];

        // Query for insurances expiring in these intervals
        $insurances = CustomerInsurance::whereBetween('expired_date', [$dates['5_days']->startOfDay(), $dates['30_days']->endOfDay()])
            ->where('is_renewed', 0)
            ->where('status', 1)
            ->get();
        foreach ($insurances->chunk(100) as $chunkedInurances) {
            foreach ($chunkedInurances as $insurance) {
                $messageText = $insurance->premiumType->is_vehicle == 1 ? $this->renewalReminderVehicle($insurance) : $this->renewalReminder($insurance);
                $receiverId = $insurance->customer->mobile_number;
                $this->whatsAppSendMessage($messageText, $receiverId);
                $this->whatsAppSendMessage($messageText, '+918000071413');
            }
        }

        $this->info('Renewal reminders sent successfully.');
    }
}

