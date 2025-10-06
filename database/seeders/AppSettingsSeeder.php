<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\AppSettingService;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cache before seeding
        AppSettingService::clearCache();

        // ========================================
        // CATEGORY: Application
        // ========================================
        $applicationSettings = [
            'app_name' => [
                'value' => env('APP_NAME', 'Insurance Admin Panel'),
                'type' => 'string',
                'description' => 'Application Display Name',
                'is_encrypted' => false,
            ],
            'app_timezone' => [
                'value' => 'Asia/Kolkata',
                'type' => 'string',
                'description' => 'Application Timezone (Valid PHP timezone identifier)',
                'is_encrypted' => false,
            ],
            'app_locale' => [
                'value' => config('app.locale', 'en'),
                'type' => 'string',
                'description' => 'Default Language (en, hi, etc.)',
                'is_encrypted' => false,
            ],
            'app_currency' => [
                'value' => 'INR',
                'type' => 'string',
                'description' => 'Default Currency Code (INR, USD, EUR, GBP)',
                'is_encrypted' => false,
            ],
            'app_currency_symbol' => [
                'value' => '₹',
                'type' => 'string',
                'description' => 'Default Currency Symbol',
                'is_encrypted' => false,
            ],
            'app_date_format' => [
                'value' => 'd/m/Y',
                'type' => 'string',
                'description' => 'Date Format (d/m/Y, Y-m-d, m/d/Y)',
                'is_encrypted' => false,
            ],
            'app_time_format' => [
                'value' => '12h',
                'type' => 'string',
                'description' => 'Time Format (12h for AM/PM, 24h for 24-hour)',
                'is_encrypted' => false,
            ],
            'pagination_default' => [
                'value' => '15',
                'type' => 'numeric',
                'description' => 'Default Items Per Page',
                'is_encrypted' => false,
            ],
            'session_lifetime' => [
                'value' => env('SESSION_LIFETIME', '120'),
                'type' => 'numeric',
                'description' => 'Session Timeout in Minutes',
                'is_encrypted' => false,
            ],
        ];

        AppSettingService::setBulk($applicationSettings, 'application');

        // ========================================
        // CATEGORY: WhatsApp
        // ========================================
        $whatsappSettings = [
            'whatsapp_sender_id' => [
                'value' => '919727793123',
                'type' => 'string',
                'description' => 'WhatsApp API Sender ID',
                'is_encrypted' => false,
            ],
            'whatsapp_base_url' => [
                'value' => 'https://api.botmastersender.com/api/v1/',
                'type' => 'string',
                'description' => 'WhatsApp API Base URL',
                'is_encrypted' => false,
            ],
            'whatsapp_auth_token' => [
                'value' => '53eb1f03-90be-49ce-9dbe-b23fe982b31f',
                'type' => 'string',
                'description' => 'WhatsApp API Authentication Token',
                'is_encrypted' => true,
            ],
        ];

        AppSettingService::setBulk($whatsappSettings, 'whatsapp');

        // ========================================
        // CATEGORY: Mail
        // ========================================
        $mailSettings = [
            'mail_default_driver' => [
                'value' => env('MAIL_MAILER', 'smtp'),
                'type' => 'string',
                'description' => 'Default Mail Driver (smtp, sendmail, mailgun, etc.)',
                'is_encrypted' => false,
            ],
            'mail_from_address' => [
                'value' => env('MAIL_FROM_ADDRESS', 'noreply@insuranceadmin.com'),
                'type' => 'string',
                'description' => 'Default From Email Address',
                'is_encrypted' => false,
            ],
            'mail_from_name' => [
                'value' => env('MAIL_FROM_NAME', env('APP_NAME', 'Insurance Admin Panel')),
                'type' => 'string',
                'description' => 'Default From Name',
                'is_encrypted' => false,
            ],
            'mail_smtp_host' => [
                'value' => env('MAIL_HOST', 'smtp.mailtrap.io'),
                'type' => 'string',
                'description' => 'SMTP Server Host',
                'is_encrypted' => false,
            ],
            'mail_smtp_port' => [
                'value' => env('MAIL_PORT', '2525'),
                'type' => 'numeric',
                'description' => 'SMTP Server Port (25, 465, 587, 2525)',
                'is_encrypted' => false,
            ],
            'mail_smtp_encryption' => [
                'value' => env('MAIL_ENCRYPTION', 'tls'),
                'type' => 'string',
                'description' => 'SMTP Encryption (tls, ssl, or null)',
                'is_encrypted' => false,
            ],
            'mail_smtp_username' => [
                'value' => env('MAIL_USERNAME', ''),
                'type' => 'string',
                'description' => 'SMTP Authentication Username',
                'is_encrypted' => true,
            ],
            'mail_smtp_password' => [
                'value' => env('MAIL_PASSWORD', ''),
                'type' => 'string',
                'description' => 'SMTP Authentication Password',
                'is_encrypted' => true,
            ],
        ];

        AppSettingService::setBulk($mailSettings, 'mail');

        // ========================================
        // CATEGORY: Notifications
        // ========================================
        $notificationSettings = [
            'email_notifications_enabled' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Master Toggle for Email Notifications',
                'is_encrypted' => false,
            ],
            'whatsapp_notifications_enabled' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Master Toggle for WhatsApp Notifications',
                'is_encrypted' => false,
            ],
            'renewal_reminder_days' => [
                'value' => '30,15,7,1',
                'type' => 'string',
                'description' => 'Days Before Expiry to Send Renewal Reminders (comma-separated: 30,15,7,1)',
                'is_encrypted' => false,
            ],
            'birthday_wishes_enabled' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Send Birthday Wishes to Customers Automatically',
                'is_encrypted' => false,
            ],
        ];

        AppSettingService::setBulk($notificationSettings, 'notifications');

        $this->command->info('✅ App Settings seeded successfully!');
        $this->command->info('📊 Categories: application, whatsapp, mail, notifications');
    }
}
