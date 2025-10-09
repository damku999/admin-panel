<?php

namespace App\Providers;

use App\Services\AppSettingService;
use Illuminate\Support\ServiceProvider;

class DynamicConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            // Load Application Settings
            $this->loadApplicationSettings();

            // Load WhatsApp Settings
            $this->loadWhatsAppSettings();

            // Load Mail Settings
            $this->loadMailSettings();

            // Load Notification Settings
            $this->loadNotificationSettings();

        } catch (\Exception $e) {
            // Silently fail during migration/installation
            \Log::debug('DynamicConfigServiceProvider failed: '.$e->getMessage());
        }
    }

    /**
     * Load Application Settings
     */
    protected function loadApplicationSettings(): void
    {
        $settings = AppSettingService::getByCategory('application');

        if (! empty($settings)) {
            config([
                'app.name' => $settings['app_name'] ?? config('app.name'),
                'app.timezone' => $settings['app_timezone'] ?? config('app.timezone'),
                'app.locale' => $settings['app_locale'] ?? config('app.locale'),
                'app.currency' => $settings['app_currency'] ?? 'INR',
                'app.currency_symbol' => $settings['app_currency_symbol'] ?? '₹',
                'app.date_format' => $settings['app_date_format'] ?? 'd/m/Y',
                'app.time_format' => $settings['app_time_format'] ?? '12h',
                'app.pagination_default' => (int) ($settings['pagination_default'] ?? 15),
                'session.lifetime' => (int) ($settings['session_lifetime'] ?? config('session.lifetime')),
            ]);
        }
    }

    /**
     * Load WhatsApp Settings
     */
    protected function loadWhatsAppSettings(): void
    {
        $settings = AppSettingService::getByCategory('whatsapp');

        if (! empty($settings)) {
            config([
                'whatsapp.sender_id' => $settings['whatsapp_sender_id'] ?? config('whatsapp.sender_id'),
                'whatsapp.base_url' => $settings['whatsapp_base_url'] ?? config('whatsapp.base_url'),
                'whatsapp.auth_token' => $settings['whatsapp_auth_token'] ?? config('whatsapp.auth_token'),
            ]);
        }
    }

    /**
     * Load Mail Settings
     */
    protected function loadMailSettings(): void
    {
        $settings = AppSettingService::getByCategory('mail');

        if (! empty($settings)) {
            config([
                'mail.default' => $settings['mail_default_driver'] ?? config('mail.default'),
                'mail.from.address' => $settings['mail_from_address'] ?? config('mail.from.address'),
                'mail.from.name' => $settings['mail_from_name'] ?? config('mail.from.name'),
                'mail.mailers.smtp.host' => $settings['mail_smtp_host'] ?? config('mail.mailers.smtp.host'),
                'mail.mailers.smtp.port' => (int) ($settings['mail_smtp_port'] ?? config('mail.mailers.smtp.port')),
                'mail.mailers.smtp.encryption' => $settings['mail_smtp_encryption'] ?? config('mail.mailers.smtp.encryption'),
                'mail.mailers.smtp.username' => $settings['mail_smtp_username'] ?? config('mail.mailers.smtp.username'),
                'mail.mailers.smtp.password' => $settings['mail_smtp_password'] ?? config('mail.mailers.smtp.password'),
            ]);
        }
    }

    /**
     * Load Notification Settings
     */
    protected function loadNotificationSettings(): void
    {
        $settings = AppSettingService::getByCategory('notifications');

        if (! empty($settings)) {
            config([
                'notifications.email_enabled' => filter_var($settings['email_notifications_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'notifications.whatsapp_enabled' => filter_var($settings['whatsapp_notifications_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'notifications.renewal_reminder_days' => $settings['renewal_reminder_days'] ?? '30,15,7,1',
                'notifications.birthday_wishes_enabled' => filter_var($settings['birthday_wishes_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }
}
