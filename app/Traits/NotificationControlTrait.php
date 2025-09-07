<?php

namespace App\Traits;

use App\Services\AppSettingService;

trait NotificationControlTrait
{
    /**
     * Check if WhatsApp notifications are enabled
     */
    protected function isWhatsAppNotificationEnabled(): bool
    {
        return AppSettingService::get('enable_whatsapp_notifications', 'true') === 'true';
    }

    /**
     * Check if email notifications are enabled
     */
    protected function isEmailNotificationEnabled(): bool
    {
        return AppSettingService::get('enable_email_notifications', 'true') === 'true';
    }

    /**
     * Send WhatsApp message if notifications are enabled
     */
    protected function sendWhatsAppIfEnabled(string $messageText, string $receiverId): bool
    {
        if ($this->isWhatsAppNotificationEnabled()) {
            $this->whatsAppSendMessage($messageText, $receiverId);
            return true;
        }
        return false;
    }

    /**
     * Send WhatsApp message with attachment if notifications are enabled
     */
    protected function sendWhatsAppWithAttachmentIfEnabled(string $messageText, string $receiverId, string $filePath): bool
    {
        if ($this->isWhatsAppNotificationEnabled()) {
            $this->whatsAppSendMessageWithAttachment($messageText, $receiverId, $filePath);
            return true;
        }
        return false;
    }
}