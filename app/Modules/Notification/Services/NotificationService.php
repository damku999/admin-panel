<?php

namespace App\Modules\Notification\Services;

use App\Modules\Notification\Contracts\NotificationServiceInterface;
use App\Traits\WhatsAppApiTrait;
use App\Events\Communication\WhatsAppMessageQueued;
use App\Events\Communication\EmailQueued;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NotificationService implements NotificationServiceInterface
{
    use WhatsAppApiTrait;

    private const HIGH_PRIORITY = 1;
    private const NORMAL_PRIORITY = 5;
    private const LOW_PRIORITY = 9;

    private const NOTIFICATION_TYPES = [
        'whatsapp' => 'WhatsApp',
        'email' => 'Email',
        'sms' => 'SMS'
    ];

    public function sendWhatsAppMessage(string $message, string $phoneNumber, ?array $attachments = null): bool
    {
        try {
            // Format phone number
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);

            if ($attachments) {
                $response = $this->whatsAppSendMessageWithAttachment($message, $phoneNumber, $attachments[0]);
            } else {
                $response = $this->whatsAppSendMessage($message, $phoneNumber);
            }

            // Log successful delivery
            $this->logNotificationStatus([
                'type' => 'whatsapp',
                'recipient' => $phoneNumber,
                'status' => $response ? 'sent' : 'failed',
                'message_id' => $this->generateMessageId(),
                'sent_at' => now(),
            ]);

            return $response;
        } catch (\Throwable $e) {
            Log::error('WhatsApp message send failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            $this->logNotificationStatus([
                'type' => 'whatsapp',
                'recipient' => $phoneNumber,
                'status' => 'failed',
                'error' => $e->getMessage(),
                'attempted_at' => now(),
            ]);

            return false;
        }
    }

    public function sendEmail(string $email, string $subject, string $body, ?array $attachments = null): bool
    {
        try {
            Mail::send([], [], function ($message) use ($email, $subject, $body, $attachments) {
                $message->to($email)
                        ->subject($subject)
                        ->html($body);

                if ($attachments) {
                    foreach ($attachments as $attachment) {
                        $message->attach($attachment);
                    }
                }
            });

            $this->logNotificationStatus([
                'type' => 'email',
                'recipient' => $email,
                'status' => 'sent',
                'message_id' => $this->generateMessageId(),
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Email send failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            $this->logNotificationStatus([
                'type' => 'email',
                'recipient' => $email,
                'status' => 'failed',
                'error' => $e->getMessage(),
                'attempted_at' => now(),
            ]);

            return false;
        }
    }

    public function sendSms(string $phoneNumber, string $message): bool
    {
        try {
            // SMS implementation would go here
            // For now, log as placeholder
            Log::info('SMS message queued', [
                'phone' => $phoneNumber,
                'message' => substr($message, 0, 100)
            ]);

            $this->logNotificationStatus([
                'type' => 'sms',
                'recipient' => $phoneNumber,
                'status' => 'sent',
                'message_id' => $this->generateMessageId(),
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('SMS send failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    public function queueNotification(string $type, array $recipient, array $content, int $priority = 5): bool
    {
        try {
            $notificationData = [
                'id' => $this->generateMessageId(),
                'type' => $type,
                'recipient' => $recipient,
                'content' => $content,
                'priority' => $priority,
                'status' => 'queued',
                'queued_at' => now(),
                'attempts' => 0,
                'max_attempts' => 3,
            ];

            // Store in message queue table
            DB::table('message_queue')->insert($notificationData);

            // Fire appropriate queued event
            switch ($type) {
                case 'whatsapp':
                    WhatsAppMessageQueued::dispatch(
                        $recipient['phone'] ?? $recipient['number'],
                        $content['message'],
                        $content['attachments'] ?? null,
                        $priority
                    );
                    break;

                case 'email':
                    EmailQueued::dispatch(
                        $recipient['email'],
                        $content['subject'],
                        $content['body'],
                        $content['attachments'] ?? null,
                        $priority
                    );
                    break;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Notification queue failed', [
                'type' => $type,
                'recipient' => $recipient,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    public function getNotificationStatus(string $notificationId): array
    {
        $status = DB::table('delivery_status')
            ->where('message_id', $notificationId)
            ->first();

        if (!$status) {
            return [
                'message_id' => $notificationId,
                'status' => 'not_found',
                'error' => 'Notification not found'
            ];
        }

        return [
            'message_id' => $status->message_id,
            'type' => $status->type,
            'recipient' => $status->recipient,
            'status' => $status->status,
            'sent_at' => $status->sent_at,
            'delivered_at' => $status->delivered_at,
            'error' => $status->error,
            'attempts' => $status->attempts ?? 1,
        ];
    }

    public function getDeliveryReport(string $startDate, string $endDate): array
    {
        $report = DB::table('delivery_status')
            ->whereBetween('sent_at', [$startDate, $endDate])
            ->selectRaw('
                type,
                status,
                COUNT(*) as count,
                AVG(CASE WHEN delivered_at IS NOT NULL 
                    THEN TIMESTAMPDIFF(SECOND, sent_at, delivered_at) 
                    ELSE NULL END) as avg_delivery_time_seconds
            ')
            ->groupBy(['type', 'status'])
            ->get();

        $summary = [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'total_sent' => 0,
            'success_rate' => 0,
            'by_type' => [],
            'by_status' => [],
        ];

        foreach ($report as $row) {
            $summary['total_sent'] += $row->count;
            
            if (!isset($summary['by_type'][$row->type])) {
                $summary['by_type'][$row->type] = [
                    'total' => 0,
                    'sent' => 0,
                    'failed' => 0,
                    'success_rate' => 0
                ];
            }
            
            $summary['by_type'][$row->type]['total'] += $row->count;
            $summary['by_type'][$row->type][$row->status] = $row->count;
            
            $summary['by_status'][$row->status] = ($summary['by_status'][$row->status] ?? 0) + $row->count;
        }

        // Calculate success rates
        $totalSuccess = $summary['by_status']['sent'] ?? 0;
        $summary['success_rate'] = $summary['total_sent'] > 0 
            ? round(($totalSuccess / $summary['total_sent']) * 100, 2) 
            : 0;

        foreach ($summary['by_type'] as $type => &$typeData) {
            $typeData['success_rate'] = $typeData['total'] > 0 
                ? round(($typeData['sent'] / $typeData['total']) * 100, 2) 
                : 0;
        }

        return $summary;
    }

    public function processNotificationQueue(): int
    {
        $processed = 0;
        
        // Get high priority notifications first
        $notifications = DB::table('message_queue')
            ->where('status', 'queued')
            ->where('attempts', '<', DB::raw('max_attempts'))
            ->orderBy('priority')
            ->orderBy('queued_at')
            ->limit(50)
            ->get();

        foreach ($notifications as $notification) {
            try {
                $success = $this->processQueuedNotification($notification);
                
                if ($success) {
                    DB::table('message_queue')
                        ->where('id', $notification->id)
                        ->update([
                            'status' => 'sent',
                            'sent_at' => now(),
                        ]);
                } else {
                    DB::table('message_queue')
                        ->where('id', $notification->id)
                        ->update([
                            'attempts' => $notification->attempts + 1,
                            'last_attempt_at' => now(),
                        ]);
                }

                $processed++;
            } catch (\Throwable $e) {
                Log::error('Queue processing failed', [
                    'notification_id' => $notification->id,
                    'error' => $e->getMessage()
                ]);

                DB::table('message_queue')
                    ->where('id', $notification->id)
                    ->update([
                        'status' => 'failed',
                        'attempts' => $notification->attempts + 1,
                        'error' => $e->getMessage(),
                        'last_attempt_at' => now(),
                    ]);
            }
        }

        return $processed;
    }

    public function retryFailedNotifications(): int
    {
        $retried = 0;
        
        $failedNotifications = DB::table('message_queue')
            ->where('status', 'failed')
            ->where('attempts', '<', DB::raw('max_attempts'))
            ->where('last_attempt_at', '<', now()->subMinutes(15))
            ->limit(20)
            ->get();

        foreach ($failedNotifications as $notification) {
            DB::table('message_queue')
                ->where('id', $notification->id)
                ->update([
                    'status' => 'queued',
                    'retry_at' => now(),
                ]);
            
            $retried++;
        }

        return $retried;
    }

    public function getNotificationTemplates(string $type): array
    {
        $templates = DB::table('notification_templates')
            ->where('type', $type)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return $templates->map(function ($template) {
            return [
                'id' => $template->id,
                'name' => $template->name,
                'subject' => $template->subject,
                'content' => $template->content,
                'variables' => json_decode($template->variables, true),
            ];
        })->toArray();
    }

    public function updateCommunicationPreferences(int $customerId, array $preferences): bool
    {
        try {
            DB::table('communication_preferences')
                ->updateOrInsert(
                    ['customer_id' => $customerId],
                    [
                        'whatsapp_enabled' => $preferences['whatsapp'] ?? true,
                        'email_enabled' => $preferences['email'] ?? true,
                        'sms_enabled' => $preferences['sms'] ?? false,
                        'marketing_enabled' => $preferences['marketing'] ?? true,
                        'updated_at' => now(),
                    ]
                );

            return true;
        } catch (\Throwable $e) {
            Log::error('Communication preferences update failed', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    private function processQueuedNotification($notification): bool
    {
        $recipient = json_decode($notification->recipient, true);
        $content = json_decode($notification->content, true);

        return match ($notification->type) {
            'whatsapp' => $this->sendWhatsAppMessage(
                $content['message'],
                $recipient['phone'] ?? $recipient['number'],
                $content['attachments'] ?? null
            ),
            'email' => $this->sendEmail(
                $recipient['email'],
                $content['subject'],
                $content['body'],
                $content['attachments'] ?? null
            ),
            'sms' => $this->sendSms(
                $recipient['phone'] ?? $recipient['number'],
                $content['message']
            ),
            default => false,
        };
    }

    private function logNotificationStatus(array $data): void
    {
        try {
            DB::table('delivery_status')->insert([
                'message_id' => $data['message_id'] ?? $this->generateMessageId(),
                'type' => $data['type'],
                'recipient' => $data['recipient'],
                'status' => $data['status'],
                'sent_at' => $data['sent_at'] ?? null,
                'delivered_at' => $data['delivered_at'] ?? null,
                'error' => $data['error'] ?? null,
                'attempts' => $data['attempts'] ?? 1,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log notification status', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-digit characters
        $cleaned = preg_replace('/\D/', '', $phoneNumber);
        
        // Add country code if not present
        if (strlen($cleaned) === 10) {
            $cleaned = '91' . $cleaned;
        }
        
        return $cleaned;
    }

    private function generateMessageId(): string
    {
        return 'msg_' . now()->format('Ymd') . '_' . Str::random(8);
    }
}