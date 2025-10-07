<?php

namespace App\Services;

use App\Models\NotificationTemplate;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Log;

class TemplateService
{
    /**
     * Render a notification template by type code and channel.
     *
     * @param string $notificationTypeCode
     * @param string $channel 'whatsapp' or 'email'
     * @param array $data Variables to replace in template
     * @return string|null
     */
    public function render(string $notificationTypeCode, string $channel, array $data): ?string
    {
        try {
            // Find notification type by code
            $notificationType = NotificationType::where('code', $notificationTypeCode)
                ->where('is_active', true)
                ->first();

            if (!$notificationType) {
                Log::warning("Notification type not found: {$notificationTypeCode}");
                return null;
            }

            // Find active template for this type and channel
            $template = NotificationTemplate::where('notification_type_id', $notificationType->id)
                ->where('channel', $channel)
                ->where('is_active', true)
                ->first();

            if (!$template) {
                Log::info("No active template found for {$notificationTypeCode} ({$channel})");
                return null;
            }

            // Render template with data
            return $this->replaceVariables($template->template_content, $data);

        } catch (\Exception $e) {
            Log::error("Template rendering failed for {$notificationTypeCode}", [
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Replace template variables with actual data.
     *
     * Supports two formats:
     * - {{variable_name}} - Standard template syntax
     * - {variable_name} - Legacy format
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    protected function replaceVariables(string $template, array $data): string
    {
        $result = $template;

        foreach ($data as $key => $value) {
            // Replace {{variable}} format
            $result = str_replace('{{' . $key . '}}', (string)$value, $result);

            // Replace {variable} format for backward compatibility
            $result = str_replace('{' . $key . '}', (string)$value, $result);
        }

        return $result;
    }

    /**
     * Get available variables for a notification type.
     *
     * @param string $notificationTypeCode
     * @param string $channel
     * @return array|null
     */
    public function getAvailableVariables(string $notificationTypeCode, string $channel): ?array
    {
        $notificationType = NotificationType::where('code', $notificationTypeCode)->first();

        if (!$notificationType) {
            return null;
        }

        $template = NotificationTemplate::where('notification_type_id', $notificationType->id)
            ->where('channel', $channel)
            ->first();

        return $template?->available_variables ?? null;
    }

    /**
     * Preview template rendering without saving.
     *
     * @param string $templateContent
     * @param array $data
     * @return string
     */
    public function preview(string $templateContent, array $data): string
    {
        return $this->replaceVariables($templateContent, $data);
    }
}
