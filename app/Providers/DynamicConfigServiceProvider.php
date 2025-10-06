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
