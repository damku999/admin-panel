<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AppSettingService;

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

            // Load Security Settings
            $this->loadSecuritySettings();

        } catch (\Exception $e) {
            // Silently fail during migration/installation
            \Log::debug('DynamicConfigServiceProvider failed: ' . $e->getMessage());
        }
    }

    /**
     * Load Application Settings
     */
    protected function loadApplicationSettings(): void
    {
        $settings = AppSettingService::getByCategory('application');

        if (!empty($settings)) {
            config([
                'app.name' => $settings['app_name'] ?? config('app.name'),
                'app.timezone' => $settings['app_timezone'] ?? config('app.timezone'),
                'app.locale' => $settings['app_locale'] ?? config('app.locale'),
                'app.currency' => $settings['app_currency'] ?? '₹',
                'app.date_format' => $settings['app_date_format'] ?? 'd/m/Y',
                'app.time_format' => $settings['app_time_format'] ?? '12h',
                'app.logo' => $settings['app_logo'] ?? '/admin/images/logo.png',
                'app.favicon' => $settings['app_favicon'] ?? '/admin/images/favicon.ico',
                'app.pagination_default' => (int)($settings['pagination_default'] ?? 15),
                'session.lifetime' => (int)($settings['session_lifetime'] ?? config('session.lifetime')),
            ]);
        }
    }

    /**
     * Load WhatsApp Settings
     */
    protected function loadWhatsAppSettings(): void
    {
        $settings = AppSettingService::getByCategory('whatsapp');

        if (!empty($settings)) {
            config([
                'whatsapp.sender_id' => $settings['whatsapp_sender_id'] ?? config('whatsapp.sender_id'),
                'whatsapp.base_url' => $settings['whatsapp_base_url'] ?? config('whatsapp.base_url'),
                'whatsapp.auth_token' => $settings['whatsapp_auth_token'] ?? config('whatsapp.auth_token'),
                'whatsapp.template_language' => $settings['whatsapp_template_language'] ?? 'en',
                'whatsapp.max_retry' => (int)($settings['whatsapp_max_retry'] ?? 3),
                'whatsapp.rate_limit' => (int)($settings['whatsapp_rate_limit'] ?? 60),
            ]);
        }
    }

    /**
     * Load Mail Settings
     */
    protected function loadMailSettings(): void
    {
        $settings = AppSettingService::getByCategory('mail');

        if (!empty($settings)) {
            config([
                'mail.default' => $settings['mail_default_driver'] ?? config('mail.default'),
                'mail.from.address' => $settings['mail_from_address'] ?? config('mail.from.address'),
                'mail.from.name' => $settings['mail_from_name'] ?? config('mail.from.name'),
                'mail.mailers.smtp.host' => $settings['mail_smtp_host'] ?? config('mail.mailers.smtp.host'),
                'mail.mailers.smtp.port' => (int)($settings['mail_smtp_port'] ?? config('mail.mailers.smtp.port')),
                'mail.mailers.smtp.encryption' => $settings['mail_smtp_encryption'] ?? config('mail.mailers.smtp.encryption'),
                'mail.mailers.smtp.username' => $settings['mail_smtp_username'] ?? config('mail.mailers.smtp.username'),
                'mail.mailers.smtp.password' => $settings['mail_smtp_password'] ?? config('mail.mailers.smtp.password'),
                'mail.reply_to.address' => $settings['mail_reply_to_address'] ?? config('mail.from.address'),
            ]);
        }
    }

    /**
     * Load Security Settings
     */
    protected function loadSecuritySettings(): void
    {
        $settings = AppSettingService::getByCategory('security');

        if (!empty($settings)) {
            config([
                'auth.login_max_attempts' => (int)($settings['login_max_attempts'] ?? 5),
                'auth.login_lockout_minutes' => (int)($settings['login_lockout_minutes'] ?? 15),
                'auth.password_min_length' => (int)($settings['password_min_length'] ?? 8),
                'auth.password_require_uppercase' => filter_var($settings['password_require_uppercase'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'auth.password_require_numbers' => filter_var($settings['password_require_numbers'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'auth.password_require_special' => filter_var($settings['password_require_special'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'session.lifetime' => (int)($settings['session_timeout_minutes'] ?? config('session.lifetime')),
                'auth.enable_2fa' => filter_var($settings['enable_2fa'] ?? false, FILTER_VALIDATE_BOOLEAN),
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
