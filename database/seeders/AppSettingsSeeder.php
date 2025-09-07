<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\AppSettingService;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // WhatsApp API Settings
        $whatsappSettings = [
            'whatsapp_sender_id' => [
                'value' => '919727793123',
                'description' => 'WhatsApp API Sender ID',
                'is_encrypted' => false,
            ],
            'whatsapp_base_url' => [
                'value' => 'https://api.botmastersender.com/api/v1/',
                'description' => 'WhatsApp API Base URL',
                'is_encrypted' => false,
            ],
            'whatsapp_auth_token' => [
                'value' => '53eb1f03-90be-49ce-9dbe-b23fe982b31f',
                'description' => 'WhatsApp API Authentication Token',
                'is_encrypted' => true,
            ]
        ];

        AppSettingService::setBulk($whatsappSettings, 'whatsapp');

        // Mail Settings (common configurable ones)
        $mailSettings = [
            'mail_from_name' => [
                'value' => 'Insurance Admin Panel',
                'description' => 'Default mail from name',
                'is_encrypted' => false,
            ],
            'mail_from_address' => [
                'value' => 'noreply@insuranceadmin.com',
                'description' => 'Default mail from address',
                'is_encrypted' => false,
            ]
        ];

        AppSettingService::setBulk($mailSettings, 'mail');

        // Application Settings
        $appSettings = [
            'app_name' => [
                'value' => 'Insurance Admin Panel',
                'description' => 'Application Name',
                'is_encrypted' => false,
            ],
            'app_logo' => [
                'value' => '/admin/images/logo.png',
                'description' => 'Application Logo Path',
                'is_encrypted' => false,
            ],
            'contact_phone' => [
                'value' => '+919727793123',
                'description' => 'Contact Phone Number',
                'is_encrypted' => false,
            ],
            'business_website' => [
                'value' => 'https://parthrawal.in',
                'description' => 'Business Website URL',
                'is_encrypted' => false,
            ],
            'business_tagline' => [
                'value' => 'Think of Insurance, Think of Us.',
                'description' => 'Business Tagline',
                'is_encrypted' => false,
            ],
            'insurance_advisor_name' => [
                'value' => 'Parth Rawal',
                'description' => 'Insurance Advisor Name',
                'is_encrypted' => false,
            ]
        ];

        AppSettingService::setBulk($appSettings, 'application');

        // API Settings
        $apiSettings = [
            'api_rate_limit_per_minute' => [
                'value' => '60',
                'type' => 'numeric',
                'description' => 'API Rate Limit per minute',
                'is_encrypted' => false,
            ],
            'session_timeout_minutes' => [
                'value' => '30',
                'type' => 'numeric',
                'description' => 'Customer Session Timeout in minutes',
                'is_encrypted' => false,
            ]
        ];

        AppSettingService::setBulk($apiSettings, 'api');

        // Notification Settings
        $notificationSettings = [
            'renewal_reminder_days_before' => [
                'value' => '30',
                'type' => 'numeric',
                'description' => 'Days before expiry to send renewal reminder',
                'is_encrypted' => false,
            ],
            'enable_whatsapp_notifications' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable WhatsApp notifications',
                'is_encrypted' => false,
            ],
            'enable_email_notifications' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable email notifications',
                'is_encrypted' => false,
            ]
        ];

        AppSettingService::setBulk($notificationSettings, 'notifications');
    }
}
