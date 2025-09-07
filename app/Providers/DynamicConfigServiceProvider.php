<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AppSettingService;

class DynamicConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only apply dynamic config if app is not running migrations or commands that don't need DB
        if (!$this->app->runningInConsole() || $this->app->runningUnitTests()) {
            $this->configureMailSettings();
            $this->configureAppSettings();
        }
    }

    /**
     * Configure mail settings from database
     */
    protected function configureMailSettings(): void
    {
        try {
            $mailFromName = AppSettingService::get('mail_from_name');
            $mailFromAddress = AppSettingService::get('mail_from_address');

            if ($mailFromName) {
                config(['mail.from.name' => $mailFromName]);
            }

            if ($mailFromAddress) {
                config(['mail.from.address' => $mailFromAddress]);
            }
        } catch (\Exception $e) {
            // Silently fail if database is not ready or settings table doesn't exist
            // This prevents errors during migrations
        }
    }

    /**
     * Configure application settings from database
     */
    protected function configureAppSettings(): void
    {
        try {
            $appName = AppSettingService::get('app_name');
            
            if ($appName) {
                config(['app.name' => $appName]);
            }
        } catch (\Exception $e) {
            // Silently fail if database is not ready or settings table doesn't exist
        }
    }
}
