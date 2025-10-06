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
                'description' => 'Time Format (12h, 24h)',
                'is_encrypted' => false,
            ],
            'app_logo' => [
                'value' => '/admin/images/logo.png',
                'type' => 'string',
                'description' => 'Application Logo Path',
                'is_encrypted' => false,
            ],
            'app_favicon' => [
                'value' => '/admin/images/favicon.ico',
                'type' => 'string',
                'description' => 'Application Favicon Path',
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
            'maintenance_mode' => [
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable Maintenance Mode',
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
                'value' => 'your-token-here',
                'type' => 'string',
                'description' => 'WhatsApp API Authentication Token',
                'is_encrypted' => true,
            ],
            'whatsapp_template_language' => [
                'value' => 'en',
                'type' => 'string',
                'description' => 'WhatsApp Template Language (en, hi, etc.)',
                'is_encrypted' => false,
            ],
            'whatsapp_max_retry' => [
                'value' => '3',
                'type' => 'numeric',
                'description' => 'Max Retry Attempts for Failed Messages',
                'is_encrypted' => false,
            ],
            'whatsapp_rate_limit' => [
                'value' => '60',
                'type' => 'numeric',
                'description' => 'Max Messages Per Minute',
                'is_encrypted' => false,
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
                'description' => 'SMTP Server Port',
                'is_encrypted' => false,
            ],
            'mail_smtp_encryption' => [
                'value' => env('MAIL_ENCRYPTION', 'tls'),
                'type' => 'string',
                'description' => 'SMTP Encryption (tls, ssl, null)',
                'is_encrypted' => false,
            ],
            'mail_smtp_username' => [
                'value' => env('MAIL_USERNAME', ''),
                'type' => 'string',
                'description' => 'SMTP Authentication Username',
                'is_encrypted' => false,
            ],
            'mail_smtp_password' => [
                'value' => env('MAIL_PASSWORD', ''),
                'type' => 'string',
                'description' => 'SMTP Authentication Password',
                'is_encrypted' => true,
            ],
            'mail_reply_to_address' => [
                'value' => env('MAIL_FROM_ADDRESS', 'noreply@insuranceadmin.com'),
                'type' => 'string',
                'description' => 'Reply-To Email Address',
                'is_encrypted' => false,
            ],
            'mail_bcc_admin' => [
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'BCC Admin on All Customer Emails',
                'is_encrypted' => false,
            ],
            'mail_queue_enabled' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Queue Emails for Background Sending',
                'is_encrypted' => false,
            ],
        ];

        AppSettingService::setBulk($mailSettings, 'mail');

        // ========================================
        // CATEGORY: Notifications
        // ========================================
        $notificationSettings = [
            'notifications_enabled' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Master Toggle for All Notifications',
                'is_encrypted' => false,
            ],
            'email_notifications_enabled' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable Email Notifications',
                'is_encrypted' => false,
            ],
            'whatsapp_notifications_enabled' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable WhatsApp Notifications',
                'is_encrypted' => false,
            ],
            'sms_notifications_enabled' => [
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable SMS Notifications',
                'is_encrypted' => false,
            ],
            'renewal_reminder_days' => [
                'value' => '30,15,7,1',
                'type' => 'string',
                'description' => 'Days Before Expiry to Send Renewal Reminders (comma-separated)',
                'is_encrypted' => false,
            ],
            'claim_followup_days' => [
                'value' => '7',
                'type' => 'numeric',
                'description' => 'Auto Follow-up on Pending Claims After X Days',
                'is_encrypted' => false,
            ],
            'payment_reminder_days' => [
                'value' => '7',
                'type' => 'numeric',
                'description' => 'Remind for Pending Payments After X Days',
                'is_encrypted' => false,
            ],
            'birthday_wishes_enabled' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Send Birthday Wishes to Customers',
                'is_encrypted' => false,
            ],
            'inactive_customer_alert_days' => [
                'value' => '90',
                'type' => 'numeric',
                'description' => 'Alert for Customers with No Activity for X Days',
                'is_encrypted' => false,
            ],
        ];

        AppSettingService::setBulk($notificationSettings, 'notifications');

        // ========================================
        // CATEGORY: Security
        // ========================================
        $securitySettings = [
            'login_max_attempts' => [
                'value' => '5',
                'type' => 'numeric',
                'description' => 'Maximum Failed Login Attempts',
                'is_encrypted' => false,
            ],
            'login_lockout_minutes' => [
                'value' => '15',
                'type' => 'numeric',
                'description' => 'Account Lockout Duration in Minutes',
                'is_encrypted' => false,
            ],
            'password_min_length' => [
                'value' => '8',
                'type' => 'numeric',
                'description' => 'Minimum Password Length',
                'is_encrypted' => false,
            ],
            'password_require_uppercase' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Require Uppercase Letters in Password',
                'is_encrypted' => false,
            ],
            'password_require_numbers' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Require Numbers in Password',
                'is_encrypted' => false,
            ],
            'password_require_special' => [
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Require Special Characters in Password',
                'is_encrypted' => false,
            ],
            'session_timeout_minutes' => [
                'value' => '120',
                'type' => 'numeric',
                'description' => 'Auto Logout After Inactivity (minutes)',
                'is_encrypted' => false,
            ],
            'enable_2fa' => [
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable Two-Factor Authentication',
                'is_encrypted' => false,
            ],
            'api_rate_limit' => [
                'value' => '60',
                'type' => 'numeric',
                'description' => 'API Rate Limit Per Minute',
                'is_encrypted' => false,
            ],
        ];

        AppSettingService::setBulk($securitySettings, 'security');

        // ========================================
        // CATEGORY: Insurance
        // ========================================
        $insuranceSettings = [
            'quotation_validity_days' => [
                'value' => '30',
                'type' => 'numeric',
                'description' => 'How Long Quotations Remain Valid (days)',
                'is_encrypted' => false,
            ],
            'default_commission_percentage' => [
                'value' => '5',
                'type' => 'numeric',
                'description' => 'Default Commission Percentage',
                'is_encrypted' => false,
            ],
            'minimum_premium_amount' => [
                'value' => '5000',
                'type' => 'numeric',
                'description' => 'Minimum Policy Premium Amount',
                'is_encrypted' => false,
            ],
            'claim_auto_approval_limit' => [
                'value' => '10000',
                'type' => 'numeric',
                'description' => 'Auto-Approve Claims Under This Amount',
                'is_encrypted' => false,
            ],
            'policy_grace_period_days' => [
                'value' => '15',
                'type' => 'numeric',
                'description' => 'Grace Period After Policy Expiry (days)',
                'is_encrypted' => false,
            ],
            'document_retention_years' => [
                'value' => '7',
                'type' => 'numeric',
                'description' => 'Document Retention Period (years)',
                'is_encrypted' => false,
            ],
            'enable_customer_portal' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable Customer Self-Service Portal',
                'is_encrypted' => false,
            ],
            'allow_customer_registration' => [
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Allow Customer Self-Registration',
                'is_encrypted' => false,
            ],
        ];

        AppSettingService::setBulk($insuranceSettings, 'insurance');

        // ========================================
        // CATEGORY: Files
        // ========================================
        $fileSettings = [
            'max_upload_size_mb' => [
                'value' => '10',
                'type' => 'numeric',
                'description' => 'Maximum File Upload Size in MB',
                'is_encrypted' => false,
            ],
            'allowed_document_extensions' => [
                'value' => 'pdf,jpg,jpeg,png,doc,docx',
                'type' => 'string',
                'description' => 'Allowed Document File Extensions (comma-separated)',
                'is_encrypted' => false,
            ],
            'storage_driver' => [
                'value' => env('FILESYSTEM_DRIVER', 'local'),
                'type' => 'string',
                'description' => 'File Storage Driver (local, s3, cloudinary)',
                'is_encrypted' => false,
            ],
            'enable_image_compression' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable Image Compression',
                'is_encrypted' => false,
            ],
            'image_quality' => [
                'value' => '80',
                'type' => 'numeric',
                'description' => 'Image Compression Quality (0-100)',
                'is_encrypted' => false,
            ],
            'watermark_enabled' => [
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Add Watermark to Uploaded Documents',
                'is_encrypted' => false,
            ],
        ];

        AppSettingService::setBulk($fileSettings, 'files');

        // ========================================
        // CATEGORY: Reports
        // ========================================
        $reportSettings = [
            'default_export_format' => [
                'value' => 'xlsx',
                'type' => 'string',
                'description' => 'Default Report Export Format (xlsx, pdf, csv)',
                'is_encrypted' => false,
            ],
            'report_company_name' => [
                'value' => env('APP_NAME', 'Insurance Admin Panel'),
                'type' => 'string',
                'description' => 'Company Name on Reports',
                'is_encrypted' => false,
            ],
            'report_company_address' => [
                'value' => '',
                'type' => 'string',
                'description' => 'Company Address for Reports',
                'is_encrypted' => false,
            ],
            'report_company_phone' => [
                'value' => '',
                'type' => 'string',
                'description' => 'Company Phone for Reports',
                'is_encrypted' => false,
            ],
            'report_company_email' => [
                'value' => env('MAIL_FROM_ADDRESS', 'noreply@insuranceadmin.com'),
                'type' => 'string',
                'description' => 'Company Email for Reports',
                'is_encrypted' => false,
            ],
            'report_logo_url' => [
                'value' => '/admin/images/logo.png',
                'type' => 'string',
                'description' => 'Logo URL for PDF Reports',
                'is_encrypted' => false,
            ],
            'report_watermark_enabled' => [
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Show Watermark on Reports',
                'is_encrypted' => false,
            ],
            'report_include_timestamp' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Include Timestamp on Reports',
                'is_encrypted' => false,
            ],
        ];

        AppSettingService::setBulk($reportSettings, 'reports');

        $this->command->info('✅ App Settings seeded successfully!');
        $this->command->info('📊 Categories: application, whatsapp, mail, notifications, security, insurance, files, reports');
    }
}
