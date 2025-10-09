<?php

namespace App\Services\Notification;

use App\Models\Customer;
use App\Services\EmailService;
use App\Services\PushNotificationService;
use App\Services\SmsService;
use App\Traits\WhatsAppApiTrait;
use Illuminate\Support\Facades\Log;

/**
 * Channel Manager Service
 *
 * Manages sending notifications across multiple channels with fallback logic
 */
class ChannelManager
{
    use WhatsAppApiTrait;

    public function __construct(
        protected SmsService $smsService,
        protected PushNotificationService $pushService,
        protected EmailService $emailService,
        protected \App\Services\TemplateService $templateService
    ) {}

    /**
     * Send notification to all enabled channels
     *
     * @param  string  $notificationTypeCode  Notification type code
     * @param  NotificationContext  $context  Notification context
     * @param  array  $channels  Channels to send to (default: all)
     * @param  Customer|null  $customer  Customer model (for preferences check)
     * @return array Results for each channel
     */
    public function sendToAllChannels(
        string $notificationTypeCode,
        NotificationContext $context,
        array $channels = ['push', 'whatsapp', 'sms', 'email'],
        ?Customer $customer = null
    ): array {
        $results = [
            'notification_type' => $notificationTypeCode,
            'channels_attempted' => [],
            'channels_succeeded' => [],
            'channels_failed' => [],
            'details' => [],
        ];

        // Filter channels based on customer preferences
        if ($customer) {
            $channels = $this->filterChannelsByPreferences($customer, $channels, $notificationTypeCode);
        }

        // Send to each channel
        foreach ($channels as $channel) {
            $results['channels_attempted'][] = $channel;

            try {
                $success = match ($channel) {
                    'push' => $this->sendPush($notificationTypeCode, $context, $customer),
                    'whatsapp' => $this->sendWhatsApp($notificationTypeCode, $context, $customer),
                    'sms' => $this->sendSms($notificationTypeCode, $context, $customer),
                    'email' => $this->sendEmail($notificationTypeCode, $context, $customer),
                    default => false,
                };

                if ($success) {
                    $results['channels_succeeded'][] = $channel;
                    $results['details'][$channel] = ['success' => true, 'message' => 'Sent successfully'];
                } else {
                    $results['channels_failed'][] = $channel;
                    $results['details'][$channel] = ['success' => false, 'message' => 'Failed to send'];
                }

            } catch (\Exception $e) {
                $results['channels_failed'][] = $channel;
                $results['details'][$channel] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];

                Log::error("Channel {$channel} failed", [
                    'notification_type' => $notificationTypeCode,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Send with fallback chain (Push -> WhatsApp -> SMS -> Email)
     *
     * @param  string  $notificationTypeCode  Notification type code
     * @param  NotificationContext  $context  Notification context
     * @param  Customer|null  $customer  Customer model
     * @param  array  $fallbackChain  Custom fallback chain
     * @return array Result with successful channel
     */
    public function sendWithFallback(
        string $notificationTypeCode,
        NotificationContext $context,
        ?Customer $customer = null,
        array $fallbackChain = ['push', 'whatsapp', 'sms', 'email']
    ): array {
        // Filter channels based on preferences
        if ($customer) {
            $fallbackChain = $this->filterChannelsByPreferences($customer, $fallbackChain, $notificationTypeCode);
        }

        $attemptedChannels = [];
        $lastError = null;

        foreach ($fallbackChain as $channel) {
            $attemptedChannels[] = $channel;

            try {
                $success = match ($channel) {
                    'push' => $this->sendPush($notificationTypeCode, $context, $customer),
                    'whatsapp' => $this->sendWhatsApp($notificationTypeCode, $context, $customer),
                    'sms' => $this->sendSms($notificationTypeCode, $context, $customer),
                    'email' => $this->sendEmail($notificationTypeCode, $context, $customer),
                    default => false,
                };

                if ($success) {
                    return [
                        'success' => true,
                        'channel' => $channel,
                        'attempted_channels' => $attemptedChannels,
                        'message' => "Successfully sent via {$channel}",
                    ];
                }

            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::warning("Fallback channel {$channel} failed, trying next", [
                    'notification_type' => $notificationTypeCode,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // All channels failed
        return [
            'success' => false,
            'channel' => null,
            'attempted_channels' => $attemptedChannels,
            'message' => 'All channels failed',
            'last_error' => $lastError,
        ];
    }

    /**
     * Send to specific channel only
     *
     * @param  string  $channel  Channel name (push, whatsapp, sms, email)
     * @param  string  $notificationTypeCode  Notification type code
     * @param  NotificationContext  $context  Notification context
     * @param  Customer|null  $customer  Customer model
     * @return bool Success status
     */
    public function sendToChannel(
        string $channel,
        string $notificationTypeCode,
        NotificationContext $context,
        ?Customer $customer = null
    ): bool {
        return match ($channel) {
            'push' => $this->sendPush($notificationTypeCode, $context, $customer),
            'whatsapp' => $this->sendWhatsApp($notificationTypeCode, $context, $customer),
            'sms' => $this->sendSms($notificationTypeCode, $context, $customer),
            'email' => $this->sendEmail($notificationTypeCode, $context, $customer),
            default => false,
        };
    }

    /**
     * Send push notification
     *
     * @param  string  $notificationTypeCode  Notification type code
     * @param  NotificationContext  $context  Notification context
     * @param  Customer|null  $customer  Customer model
     * @return bool Success status
     */
    protected function sendPush(
        string $notificationTypeCode,
        NotificationContext $context,
        ?Customer $customer = null
    ): bool {
        if (! $customer) {
            $customer = $context->customer;
        }

        if (! $customer) {
            return false;
        }

        $result = $this->pushService->sendToCustomer($customer, $notificationTypeCode, $context);

        return $result['success'] ?? false;
    }

    /**
     * Send WhatsApp notification
     *
     * @param  string  $notificationTypeCode  Notification type code
     * @param  NotificationContext  $context  Notification context
     * @param  Customer|null  $customer  Customer model
     * @return bool Success status
     */
    protected function sendWhatsApp(
        string $notificationTypeCode,
        NotificationContext $context,
        ?Customer $customer = null
    ): bool {
        if (! $customer) {
            $customer = $context->customer;
        }

        if (! $customer || empty($customer->mobile)) {
            return false;
        }

        // Render template
        $message = $this->templateService->render($notificationTypeCode, 'whatsapp', $context);

        if (! $message) {
            return false;
        }

        // Send WhatsApp
        try {
            $this->whatsAppSendMessage($message, $customer->mobile, $customer->id, $notificationTypeCode);

            return true;
        } catch (\Exception $e) {
            Log::error('WhatsApp sending failed in ChannelManager', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send SMS notification
     *
     * @param  string  $notificationTypeCode  Notification type code
     * @param  NotificationContext  $context  Notification context
     * @param  Customer|null  $customer  Customer model
     * @return bool Success status
     */
    protected function sendSms(
        string $notificationTypeCode,
        NotificationContext $context,
        ?Customer $customer = null
    ): bool {
        if (! $customer) {
            $customer = $context->customer;
        }

        if (! $customer) {
            return false;
        }

        return $this->smsService->sendToCustomer($customer, $notificationTypeCode, $context);
    }

    /**
     * Send Email notification
     *
     * @param  string  $notificationTypeCode  Notification type code
     * @param  NotificationContext  $context  Notification context
     * @param  Customer|null  $customer  Customer model
     * @return bool Success status
     */
    protected function sendEmail(
        string $notificationTypeCode,
        NotificationContext $context,
        ?Customer $customer = null
    ): bool {
        if (! $customer) {
            Log::warning('Email sending skipped - customer not provided', [
                'notification_type' => $notificationTypeCode,
            ]);

            return false;
        }

        if (! $customer->email) {
            Log::warning('Email sending skipped - customer has no email', [
                'customer_id' => $customer->id,
                'notification_type' => $notificationTypeCode,
            ]);

            return false;
        }

        try {
            return $this->emailService->sendTemplatedEmail(
                to: $customer->email,
                notificationTypeCode: $notificationTypeCode,
                context: $context
            );
        } catch (\Exception $e) {
            Log::error('Email sending failed in ChannelManager', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'notification_type' => $notificationTypeCode,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Filter channels based on customer preferences
     *
     * @param  Customer  $customer  Customer model
     * @param  array  $channels  Original channels
     * @param  string  $notificationTypeCode  Notification type code
     * @return array Filtered channels
     */
    protected function filterChannelsByPreferences(
        Customer $customer,
        array $channels,
        string $notificationTypeCode
    ): array {
        $preferences = $customer->notification_preferences ?? [];

        // Filter by enabled channels
        if (isset($preferences['channels']) && is_array($preferences['channels'])) {
            $channels = array_intersect($channels, $preferences['channels']);
        }

        // Remove opted-out notification types
        if (isset($preferences['opt_out_types']) && is_array($preferences['opt_out_types'])) {
            if (in_array($notificationTypeCode, $preferences['opt_out_types'])) {
                return []; // Customer opted out of this notification type entirely
            }
        }

        // Check quiet hours
        if (isset($preferences['quiet_hours'])) {
            $quietHours = $preferences['quiet_hours'];
            $currentHour = now()->format('H:i');

            if (
                isset($quietHours['start']) &&
                isset($quietHours['end']) &&
                $currentHour >= $quietHours['start'] &&
                $currentHour <= $quietHours['end']
            ) {
                // During quiet hours, only allow push (silent) and email
                $channels = array_intersect($channels, ['push', 'email']);
            }
        }

        return array_values($channels); // Re-index array
    }

    /**
     * Get available channels for notification type
     *
     * @param  string  $notificationTypeCode  Notification type code
     * @return array Available channels
     */
    public function getAvailableChannels(string $notificationTypeCode): array
    {
        $availableChannels = [];

        $channels = ['push', 'whatsapp', 'sms', 'email'];

        foreach ($channels as $channel) {
            // Check if template exists for this channel
            $template = \App\Models\NotificationTemplate::whereHas('notificationType', function ($query) use ($notificationTypeCode) {
                $query->where('code', $notificationTypeCode);
            })
                ->where('channel', $channel)
                ->where('is_active', true)
                ->exists();

            if ($template) {
                $availableChannels[] = $channel;
            }
        }

        return $availableChannels;
    }

    /**
     * Test notification sending on all channels
     *
     * @param  string  $notificationTypeCode  Notification type code
     * @param  NotificationContext  $context  Test context
     * @return array Test results
     */
    public function testAllChannels(
        string $notificationTypeCode,
        NotificationContext $context
    ): array {
        $channels = ['push', 'whatsapp', 'sms', 'email'];
        $results = [];

        foreach ($channels as $channel) {
            // Check if template exists
            $hasTemplate = \App\Models\NotificationTemplate::whereHas('notificationType', function ($query) use ($notificationTypeCode) {
                $query->where('code', $notificationTypeCode);
            })
                ->where('channel', $channel)
                ->where('is_active', true)
                ->exists();

            $results[$channel] = [
                'has_template' => $hasTemplate,
                'enabled' => $this->isChannelEnabled($channel),
                'can_send' => $hasTemplate && $this->isChannelEnabled($channel),
            ];
        }

        return $results;
    }

    /**
     * Check if channel is globally enabled
     *
     * @param  string  $channel  Channel name
     * @return bool Is enabled
     */
    protected function isChannelEnabled(string $channel): bool
    {
        return match ($channel) {
            'push' => $this->isPushNotificationEnabled(),
            'whatsapp' => $this->isWhatsAppNotificationEnabled(),
            'sms' => $this->smsService->isSmsNotificationEnabled(),
            'email' => true, // Email assumed always enabled
            default => false,
        };
    }
}
