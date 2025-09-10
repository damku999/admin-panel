<?php

namespace App\Console\Commands\Module;

use App\Modules\Notification\Contracts\NotificationServiceInterface;
use Illuminate\Console\Command;

class ProcessNotificationQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:process-notifications 
                           {--limit=50 : Maximum notifications to process}
                           {--retry : Also retry failed notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process queued notifications (WhatsApp, Email, SMS)';

    public function __construct(
        private NotificationServiceInterface $notificationService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting notification queue processing...');
        
        try {
            // Process regular queue
            $processed = $this->notificationService->processNotificationQueue();
            $this->info("✅ Processed {$processed} notifications from queue");

            // Retry failed notifications if requested
            if ($this->option('retry')) {
                $retried = $this->notificationService->retryFailedNotifications();
                $this->info("🔄 Retried {$retried} failed notifications");
            }

            // Display queue statistics
            $this->displayQueueStats();

            $this->info('✨ Notification processing completed successfully');
            return self::SUCCESS;

        } catch (\Throwable $e) {
            $this->error('❌ Failed to process notifications: ' . $e->getMessage());
            
            if ($this->getOutput()->isVerbose()) {
                $this->line($e->getTraceAsString());
            }
            
            return self::FAILURE;
        }
    }

    private function displayQueueStats(): void
    {
        try {
            $stats = \DB::table('message_queue')
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "queued" THEN 1 ELSE 0 END) as queued,
                    SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                    SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
                    type
                ')
                ->groupBy('type')
                ->get();

            $this->newLine();
            $this->info('📊 Queue Statistics:');
            
            $headers = ['Type', 'Total', 'Queued', 'Sent', 'Failed'];
            $rows = [];
            
            foreach ($stats as $stat) {
                $rows[] = [
                    ucfirst($stat->type),
                    $stat->total,
                    $stat->queued,
                    $stat->sent,
                    $stat->failed,
                ];
            }
            
            if (empty($rows)) {
                $this->line('No messages in queue');
            } else {
                $this->table($headers, $rows);
            }
            
        } catch (\Throwable $e) {
            $this->warn('Could not retrieve queue statistics: ' . $e->getMessage());
        }
    }
}