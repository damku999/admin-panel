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
            // Load WhatsApp config from database
            $whatsappConfig = AppSettingService::getWhatsAppConfig();

            if (!empty($whatsappConfig)) {
                config([
                    'whatsapp.sender_id' => $whatsappConfig['whatsapp_sender_id'] ?? config('whatsapp.sender_id'),
                    'whatsapp.base_url' => $whatsappConfig['whatsapp_base_url'] ?? config('whatsapp.base_url'),
                    'whatsapp.auth_token' => $whatsappConfig['whatsapp_auth_token'] ?? config('whatsapp.auth_token'),
                ]);
            }

            // Load Mail config from database
            $mailConfig = AppSettingService::getMailConfig();

            if (!empty($mailConfig)) {
                config([
                    'mail.default' => $mailConfig['mail_default_driver'] ?? config('mail.default'),
                    'mail.from.address' => $mailConfig['mail_from_address'] ?? config('mail.from.address'),
                    'mail.from.name' => $mailConfig['mail_from_name'] ?? config('mail.from.name'),
                    'mail.mailers.smtp.host' => $mailConfig['mail_smtp_host'] ?? config('mail.mailers.smtp.host'),
                    'mail.mailers.smtp.port' => $mailConfig['mail_smtp_port'] ?? config('mail.mailers.smtp.port'),
                    'mail.mailers.smtp.encryption' => $mailConfig['mail_smtp_encryption'] ?? config('mail.mailers.smtp.encryption'),
                    'mail.mailers.smtp.username' => $mailConfig['mail_smtp_username'] ?? config('mail.mailers.smtp.username'),
                    'mail.mailers.smtp.password' => $mailConfig['mail_smtp_password'] ?? config('mail.mailers.smtp.password'),
                ]);
            }
        } catch (\Exception $e) {
            // Silently fail during migration/installation
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
