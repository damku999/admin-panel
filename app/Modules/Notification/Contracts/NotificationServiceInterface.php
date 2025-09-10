<?php

namespace App\Modules\Notification\Contracts;

interface NotificationServiceInterface
{
    public function sendWhatsAppMessage(string $message, string $phoneNumber, ?array $attachments = null): bool;
    
    public function sendEmail(string $email, string $subject, string $body, ?array $attachments = null): bool;
    
    public function sendSms(string $phoneNumber, string $message): bool;
    
    public function queueNotification(string $type, array $recipient, array $content, int $priority = 5): bool;
    
    public function getNotificationStatus(string $notificationId): array;
    
    public function getDeliveryReport(string $startDate, string $endDate): array;
    
    public function processNotificationQueue(): int;
    
    public function retryFailedNotifications(): int;
    
    public function getNotificationTemplates(string $type): array;
    
    public function updateCommunicationPreferences(int $customerId, array $preferences): bool;
}