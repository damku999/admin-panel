<?php

namespace App\Services;

use App\Models\NotificationDeliveryTracking;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Models\NotificationType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationLoggerService
{
    /**
     * Log a notification before sending
     *
     * @param  Model  $notifiable  The entity being notified (Customer, Insurance, Quotation, Claim)
     * @param  string  $channel  Channel: whatsapp, email, sms
     * @param  string  $recipient  Phone number or email address
     * @param  string  $message  Message content
     * @param  array  $options  Additional options
     */
    public function logNotification(
        Model $notifiable,
        string $channel,
        string $recipient,
        string $message,
        array $options = []
    ): NotificationLog {
        try {
            $notificationType = null;
            $template = null;

            // Get notification type if code provided
            if (isset($options['notification_type_code'])) {
                $notificationType = NotificationType::where('code', $options['notification_type_code'])
                    ->where('is_active', true)
                    ->first();
            }

            // Get template if ID provided
            if (isset($options['template_id'])) {
                $template = NotificationTemplate::find($options['template_id']);
            }

            // Create the log entry
            $log = NotificationLog::create([
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => $notifiable->id,
                'notification_type_id' => $notificationType?->id,
                'template_id' => $template?->id,
                'channel' => $channel,
                'recipient' => $recipient,
                'subject' => $options['subject'] ?? null,
                'message_content' => $message,
                'variables_used' => $options['variables'] ?? null,
                'status' => 'pending',
                'sent_by' => auth()->id(),
                'retry_count' => 0,
            ]);

            Log::info('Notification logged', [
                'log_id' => $log->id,
                'channel' => $channel,
                'recipient' => $recipient,
                'notifiable' => get_class($notifiable).'#'.$notifiable->id,
            ]);

            return $log;

        } catch (\Exception $e) {
            Log::error('Failed to log notification', [
                'error' => $e->getMessage(),
                'channel' => $channel,
                'recipient' => $recipient,
            ]);
            throw $e;
        }
    }

    /**
     * Update notification status to sent
     *
     * @param  array  $apiResponse  API provider response
     */
    public function markAsSent(NotificationLog $log, array $apiResponse = []): NotificationLog
    {
        DB::transaction(function () use ($log, $apiResponse) {
            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
                'api_response' => $apiResponse,
            ]);

            // Create tracking record
            $this->addDeliveryTracking($log, 'sent', $apiResponse);
        });

        return $log->fresh();
    }

    /**
     * Update notification status to delivered
     *
     * @param  array  $providerStatus  Provider status data
     */
    public function markAsDelivered(NotificationLog $log, array $providerStatus = []): NotificationLog
    {
        DB::transaction(function () use ($log, $providerStatus) {
            $log->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);

            // Create tracking record
            $this->addDeliveryTracking($log, 'delivered', $providerStatus);
        });

        return $log->fresh();
    }

    /**
     * Update notification status to read
     *
     * @param  array  $providerStatus  Provider status data
     */
    public function markAsRead(NotificationLog $log, array $providerStatus = []): NotificationLog
    {
        DB::transaction(function () use ($log, $providerStatus) {
            $log->update([
                'status' => 'read',
                'read_at' => now(),
            ]);

            // Create tracking record
            $this->addDeliveryTracking($log, 'read', $providerStatus);
        });

        return $log->fresh();
    }

    /**
     * Mark notification as failed
     *
     * @param  string  $errorMessage  Error message
     * @param  array  $apiResponse  API response if available
     */
    public function markAsFailed(NotificationLog $log, string $errorMessage, array $apiResponse = []): NotificationLog
    {
        DB::transaction(function () use ($log, $errorMessage, $apiResponse) {
            $retryCount = $log->retry_count + 1;
            $nextRetryAt = $this->calculateNextRetryTime($retryCount);

            $log->update([
                'status' => 'failed',
                'error_message' => $errorMessage,
                'api_response' => $apiResponse,
                'retry_count' => $retryCount,
                'next_retry_at' => $nextRetryAt,
            ]);

            // Create tracking record
            $this->addDeliveryTracking($log, 'failed', $apiResponse);

            Log::warning('Notification marked as failed', [
                'log_id' => $log->id,
                'error' => $errorMessage,
                'retry_count' => $retryCount,
                'next_retry_at' => $nextRetryAt?->format('Y-m-d H:i:s'),
            ]);
        });

        return $log->fresh();
    }

    /**
     * Update notification status from webhook
     *
     * @param  int  $logId  Notification log ID
     * @param  string  $status  New status
     * @param  array  $providerData  Provider data
     */
    public function updateStatusFromWebhook(int $logId, string $status, array $providerData = []): ?NotificationLog
    {
        $log = NotificationLog::find($logId);

        if (! $log) {
            Log::warning('Notification log not found for webhook update', ['log_id' => $logId]);

            return null;
        }

        switch ($status) {
            case 'delivered':
                return $this->markAsDelivered($log, $providerData);
            case 'read':
                return $this->markAsRead($log, $providerData);
            case 'failed':
                $errorMessage = $providerData['error'] ?? 'Delivery failed (webhook)';

                return $this->markAsFailed($log, $errorMessage, $providerData);
            default:
                Log::warning('Unknown status in webhook', ['status' => $status, 'log_id' => $logId]);

                return $log;
        }
    }

    /**
     * Get notification history for an entity
     *
     * @param  array  $filters  Optional filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNotificationHistory(Model $notifiable, array $filters = [])
    {
        $query = NotificationLog::where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->id)
            ->with(['notificationType', 'template', 'sender', 'deliveryTracking'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (isset($filters['channel'])) {
            $query->where('channel', $filters['channel']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from_date'])) {
            $query->where('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->where('created_at', '<=', $filters['to_date']);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get failed notifications ready for retry
     *
     * @param  int  $limit  Maximum notifications to retrieve
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFailedNotifications(int $limit = 100)
    {
        return NotificationLog::readyToRetry()
            ->with(['notificationType', 'template'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get notification statistics
     *
     * @param  array  $filters  Date range and other filters
     */
    public function getStatistics(array $filters = []): array
    {
        $query = NotificationLog::query();

        // Apply date filters
        if (isset($filters['from_date'])) {
            $query->where('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->where('created_at', '<=', $filters['to_date']);
        }

        // Get counts by status
        $statusCounts = (clone $query)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get counts by channel
        $channelCounts = (clone $query)
            ->select('channel', DB::raw('count(*) as count'))
            ->groupBy('channel')
            ->pluck('count', 'channel')
            ->toArray();

        // Calculate success rate
        $totalSent = $query->count();
        $successful = $query->whereIn('status', ['sent', 'delivered', 'read'])->count();
        $successRate = $totalSent > 0 ? round(($successful / $totalSent) * 100, 2) : 0;

        // Get failed notifications count
        $failedCount = NotificationLog::failed()->count();

        // Get most used templates
        $topTemplates = NotificationLog::select('template_id', DB::raw('count(*) as count'))
            ->whereNotNull('template_id')
            ->groupBy('template_id')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->with('template.notificationType')
            ->get();

        return [
            'total_sent' => $totalSent,
            'successful' => $successful,
            'failed' => $failedCount,
            'success_rate' => $successRate,
            'status_counts' => $statusCounts,
            'channel_counts' => $channelCounts,
            'top_templates' => $topTemplates,
        ];
    }

    /**
     * Add delivery tracking record
     */
    protected function addDeliveryTracking(NotificationLog $log, string $status, array $providerStatus = []): NotificationDeliveryTracking
    {
        return NotificationDeliveryTracking::create([
            'notification_log_id' => $log->id,
            'status' => $status,
            'tracked_at' => now(),
            'provider_status' => $providerStatus,
            'metadata' => [
                'previous_status' => $log->status,
                'updated_by_webhook' => request()->ip() ?? 'system',
            ],
        ]);
    }

    /**
     * Calculate next retry time based on retry count
     */
    protected function calculateNextRetryTime(int $retryCount): ?Carbon
    {
        // Exponential backoff: 1h, 4h, 24h
        return match ($retryCount) {
            1 => now()->addHour(),
            2 => now()->addHours(4),
            3 => now()->addHours(24),
            default => null, // No more retries after 3 attempts
        };
    }

    /**
     * Retry a failed notification
     *
     * @return bool Success status
     */
    public function retryNotification(NotificationLog $log): bool
    {
        if (! $log->canRetry()) {
            Log::warning('Notification cannot be retried', [
                'log_id' => $log->id,
                'retry_count' => $log->retry_count,
                'status' => $log->status,
            ]);

            return false;
        }

        try {
            // Reset to pending for retry
            $log->update([
                'status' => 'pending',
                'error_message' => null,
            ]);

            // Dispatch the notification again based on channel
            // This will be handled by the respective services (WhatsApp, Email, etc.)

            Log::info('Notification queued for retry', [
                'log_id' => $log->id,
                'retry_count' => $log->retry_count,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to retry notification', [
                'log_id' => $log->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Archive old notification logs
     *
     * @param  int  $daysOld  Number of days to keep
     * @return int Number of archived logs
     */
    public function archiveOldLogs(int $daysOld = 90): int
    {
        $cutoffDate = now()->subDays($daysOld);

        $count = NotificationLog::where('created_at', '<', $cutoffDate)
            ->whereIn('status', ['sent', 'delivered', 'read'])
            ->delete();

        Log::info('Archived old notification logs', [
            'cutoff_date' => $cutoffDate->format('Y-m-d'),
            'count' => $count,
        ]);

        return $count;
    }
}
