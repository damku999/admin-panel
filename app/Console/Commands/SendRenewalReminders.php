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

        $dates = [
            '5_days' => $currentDate->copy()->addDays(5)->startOfDay(),
            '10_days' => $currentDate->copy()->addDays(10)->startOfDay(),
            '15_days' => $currentDate->copy()->addDays(15)->startOfDay(),
            '30_days' => $currentDate->copy()->addDays(30)->startOfDay(),
        ];

        $insurances = CustomerInsurance::where(function ($query) use ($dates) {
            $query->whereDate('expired_date', $dates['5_days'])
                ->orWhereDate('expired_date', $dates['10_days'])
                ->orWhereDate('expired_date', $dates['15_days'])
                ->orWhereDate('expired_date', $dates['30_days']);
        })
            ->where('is_renewed', 0)
            ->where('status', 1)
            ->get();

        foreach ($insurances->chunk(100) as $chunkedInurances) {
            foreach ($chunkedInurances as $insurance) {
                $messageText = $insurance->premiumType->is_vehicle == 1 ? $this->renewalReminderVehicle($insurance) : $this->renewalReminder($insurance);
                $receiverId = $insurance->customer->mobile_number;
                $this->whatsAppSendMessage($messageText, $receiverId);
                // $this->whatsAppSendMessage($messageText, '+918000071413');
            }
        }

        $this->info('Renewal reminders sent successfully.');
    }
}