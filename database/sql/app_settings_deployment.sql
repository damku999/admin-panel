-- ============================================================================
-- App Settings Infrastructure - MariaDB/MySQL Deployment Script
-- ============================================================================
-- Purpose: Create app_settings table and seed default data
-- Database: MariaDB/MySQL
-- Generated: 2025-10-06
-- ============================================================================

-- Create app_settings table
CREATE TABLE IF NOT EXISTS `app_settings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(255) NOT NULL UNIQUE,
    `value` TEXT NULL,
    `type` VARCHAR(255) NOT NULL DEFAULT 'string' COMMENT 'string, json, boolean, numeric',
    `category` VARCHAR(255) NOT NULL DEFAULT 'general' COMMENT 'general, whatsapp, mail, api, notifications',
    `description` TEXT NULL,
    `is_encrypted` TINYINT(1) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    INDEX `idx_key` (`key`),
    INDEX `idx_category` (`category`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Seed Default App Settings Data
-- ============================================================================
--
-- IMPORTANT: ENCRYPTION NOTES FOR PRODUCTION
-- ============================================================================
-- Encrypted settings (is_encrypted=1) CANNOT be pre-encrypted in this SQL file
-- because each environment uses a different Laravel APP_KEY.
--
-- For encrypted settings on production server:
-- 1. Run this SQL to create the record with is_encrypted=1 and placeholder value
-- 2. After deployment, update the value using Laravel:
--    AppSettingService::setEncrypted('setting_key', 'actual-value')
--
-- Or use artisan tinker:
--    php artisan tinker
--    >>> use App\Services\AppSettingService;
--    >>> AppSettingService::setEncrypted('whatsapp_auth_token', 'actual-token-here');
-- ============================================================================

-- WhatsApp API Settings
INSERT INTO `app_settings` (`key`, `value`, `type`, `category`, `description`, `is_encrypted`, `is_active`, `created_at`, `updated_at`)
VALUES
    ('whatsapp_sender_id', '919727793123', 'string', 'whatsapp', 'WhatsApp API Sender ID', 0, 1, NOW(), NOW()),
    ('whatsapp_base_url', 'https://api.botmastersender.com/api/v1/', 'string', 'whatsapp', 'WhatsApp API Base URL', 0, 1, NOW(), NOW()),
    -- NOTE: Replace placeholder after deployment via AppSettingService::setEncrypted()
    ('whatsapp_auth_token', 'CHANGE-ME-ON-PRODUCTION', 'string', 'whatsapp', 'WhatsApp API Authentication Token', 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `value` = VALUES(`value`),
    `type` = VALUES(`type`),
    `category` = VALUES(`category`),
    `description` = VALUES(`description`),
    `is_encrypted` = VALUES(`is_encrypted`),
    `is_active` = VALUES(`is_active`),
    `updated_at` = NOW();

-- Mail Settings
INSERT INTO `app_settings` (`key`, `value`, `type`, `category`, `description`, `is_encrypted`, `is_active`, `created_at`, `updated_at`)
VALUES
    ('mail_from_name', 'Insurance Admin Panel', 'string', 'mail', 'Default mail from name', 0, 1, NOW(), NOW()),
    ('mail_from_address', 'noreply@insuranceadmin.com', 'string', 'mail', 'Default mail from address', 0, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `value` = VALUES(`value`),
    `type` = VALUES(`type`),
    `category` = VALUES(`category`),
    `description` = VALUES(`description`),
    `is_encrypted` = VALUES(`is_encrypted`),
    `is_active` = VALUES(`is_active`),
    `updated_at` = NOW();

-- Application Settings
INSERT INTO `app_settings` (`key`, `value`, `type`, `category`, `description`, `is_encrypted`, `is_active`, `created_at`, `updated_at`)
VALUES
    ('app_name', 'Insurance Admin Panel', 'string', 'application', 'Application Name', 0, 1, NOW(), NOW()),
    ('app_logo', '/admin/images/logo.png', 'string', 'application', 'Application Logo Path', 0, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `value` = VALUES(`value`),
    `type` = VALUES(`type`),
    `category` = VALUES(`category`),
    `description` = VALUES(`description`),
    `is_encrypted` = VALUES(`is_encrypted`),
    `is_active` = VALUES(`is_active`),
    `updated_at` = NOW();

-- Notification Settings
INSERT INTO `app_settings` (`key`, `value`, `type`, `category`, `description`, `is_encrypted`, `is_active`, `created_at`, `updated_at`)
VALUES
    ('renewal_reminder_days_before', '30', 'numeric', 'notifications', 'Days before expiry to send renewal reminder', 0, 1, NOW(), NOW()),
    ('enable_whatsapp_notifications', 'true', 'boolean', 'notifications', 'Enable WhatsApp notifications', 0, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `value` = VALUES(`value`),
    `type` = VALUES(`type`),
    `category` = VALUES(`category`),
    `description` = VALUES(`description`),
    `is_encrypted` = VALUES(`is_encrypted`),
    `is_active` = VALUES(`is_active`),
    `updated_at` = NOW();

-- ============================================================================
-- Verification Queries
-- ============================================================================

-- View all settings
-- SELECT * FROM app_settings ORDER BY category, `key`;

-- View settings by category
-- SELECT * FROM app_settings WHERE category = 'whatsapp';

-- Count settings per category
-- SELECT category, COUNT(*) as count FROM app_settings GROUP BY category;

-- ============================================================================
-- Rollback (if needed)
-- ============================================================================

-- To remove all seeded data:
-- DELETE FROM app_settings;

-- To drop the table entirely:
-- DROP TABLE IF EXISTS app_settings;

-- ============================================================================
-- End of SQL Script
-- ============================================================================
