-- =====================================================================
-- COMPLETE PRODUCTION DATABASE MIGRATION SQL
-- =====================================================================
-- Execute these queries in the exact order listed
-- BACKUP YOUR DATABASE BEFORE RUNNING THESE COMMANDS
-- 
-- This file combines:
-- 1. Missing column additions from pending migrations
-- 2. Foreign key constraints for data integrity
-- 3. Performance optimization indexes
-- 4. Laravel migrations table population
--
-- Created: September 2024
-- Database: Laravel Insurance Management System
-- =====================================================================

-- Start transaction for safety
START TRANSACTION;

-- =====================================================================
-- PART 1: ADD MISSING COLUMNS FROM PENDING MIGRATIONS
-- =====================================================================

-- 1.1 Add coverage fields to quotation_companies table (if not exists)
-- From migration: 2025_09_04_103831_add_coverage_fields_to_quotation_companies_table

-- Check and add policy_type column
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'quotation_companies' AND column_name = 'policy_type');
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `quotation_companies` ADD COLUMN `policy_type` VARCHAR(255) NULL AFTER `plan_name`', 
    'SELECT "policy_type column already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Check and add policy_tenure_years column  
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'quotation_companies' AND column_name = 'policy_tenure_years');
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `quotation_companies` ADD COLUMN `policy_tenure_years` INT NULL AFTER `policy_type`', 
    'SELECT "policy_tenure_years column already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Check and add idv_vehicle column
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'quotation_companies' AND column_name = 'idv_vehicle');
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `quotation_companies` ADD COLUMN `idv_vehicle` DECIMAL(10,2) NULL AFTER `policy_tenure_years`', 
    'SELECT "idv_vehicle column already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Check and add idv_trailer column
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'quotation_companies' AND column_name = 'idv_trailer');
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `quotation_companies` ADD COLUMN `idv_trailer` DECIMAL(10,2) DEFAULT 0 AFTER `idv_vehicle`', 
    'SELECT "idv_trailer column already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Check and add idv_cng_lpg_kit column
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'quotation_companies' AND column_name = 'idv_cng_lpg_kit');
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `quotation_companies` ADD COLUMN `idv_cng_lpg_kit` DECIMAL(10,2) DEFAULT 0 AFTER `idv_trailer`', 
    'SELECT "idv_cng_lpg_kit column already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Check and add idv_electrical_accessories column
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'quotation_companies' AND column_name = 'idv_electrical_accessories');
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `quotation_companies` ADD COLUMN `idv_electrical_accessories` DECIMAL(10,2) DEFAULT 0 AFTER `idv_cng_lpg_kit`', 
    'SELECT "idv_electrical_accessories column already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Check and add idv_non_electrical_accessories column
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'quotation_companies' AND column_name = 'idv_non_electrical_accessories');
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `quotation_companies` ADD COLUMN `idv_non_electrical_accessories` DECIMAL(10,2) DEFAULT 0 AFTER `idv_electrical_accessories`', 
    'SELECT "idv_non_electrical_accessories column already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Check and add total_idv column
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'quotation_companies' AND column_name = 'total_idv');
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `quotation_companies` ADD COLUMN `total_idv` DECIMAL(10,2) NULL AFTER `idv_non_electrical_accessories`', 
    'SELECT "total_idv column already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 1.2 Remove ncb_percentage from quotation_companies (if exists)
-- From migration: 2025_09_04_105205_remove_ncb_percentage_from_quotation_companies_table
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'quotation_companies' AND column_name = 'ncb_percentage');
SET @sql = IF(@column_exists > 0, 
    'ALTER TABLE `quotation_companies` DROP COLUMN `ncb_percentage`', 
    'SELECT "ncb_percentage column does not exist" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 1.3 Remove plan_name from quotation_companies (if exists)
-- From migration: 2025_09_04_123455_drop_plan_name_from_quotation_companies_table
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'quotation_companies' AND column_name = 'plan_name');
SET @sql = IF(@column_exists > 0, 
    'ALTER TABLE `quotation_companies` DROP COLUMN `plan_name`', 
    'SELECT "plan_name column does not exist" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 1.4 Add recommendation_note to quotation_companies (if not exists)
-- From migration: 2025_09_04_131746_add_recommendation_note_to_quotation_companies_table
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'quotation_companies' AND column_name = 'recommendation_note');
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `quotation_companies` ADD COLUMN `recommendation_note` TEXT NULL AFTER `is_recommended` COMMENT "Note explaining why this quote is recommended"', 
    'SELECT "recommendation_note column already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 1.5 Add order_no to addon_covers table (if not exists)
-- From migration: 2025_09_04_133023_add_order_no_to_addon_covers_table
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'addon_covers' AND column_name = 'order_no');
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `addon_covers` ADD COLUMN `order_no` INT NOT NULL DEFAULT 0 AFTER `status`', 
    'SELECT "order_no column already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 1.6 Remove date_of_registration from quotations table (if exists)
-- From migration: 2025_09_04_101456_drop_date_of_registration_from_quotations_table
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'quotations' AND column_name = 'date_of_registration');
SET @sql = IF(@column_exists > 0, 
    'ALTER TABLE `quotations` DROP COLUMN `date_of_registration`', 
    'SELECT "date_of_registration column does not exist" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================================
-- PART 2: FOREIGN KEY CONSTRAINTS MIGRATION
-- =====================================================================

-- 2.1 Fix customers table constraints
ALTER TABLE `customers` 
MODIFY COLUMN `created_by` BIGINT UNSIGNED NULL,
MODIFY COLUMN `updated_by` BIGINT UNSIGNED NULL,
MODIFY COLUMN `deleted_by` BIGINT UNSIGNED NULL;

-- Add foreign key constraints to customers table (with error handling)
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'customers' AND constraint_name = 'fk_customers_created_by');
SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `customers` ADD CONSTRAINT `fk_customers_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL', 
    'SELECT "fk_customers_created_by constraint already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'customers' AND constraint_name = 'fk_customers_updated_by');
SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `customers` ADD CONSTRAINT `fk_customers_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL', 
    'SELECT "fk_customers_updated_by constraint already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'customers' AND constraint_name = 'fk_customers_deleted_by');
SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `customers` ADD CONSTRAINT `fk_customers_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL', 
    'SELECT "fk_customers_deleted_by constraint already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2.2 Fix customer_insurances table constraints
ALTER TABLE `customer_insurances`
MODIFY COLUMN `created_by` BIGINT UNSIGNED NULL,
MODIFY COLUMN `updated_by` BIGINT UNSIGNED NULL,
MODIFY COLUMN `deleted_by` BIGINT UNSIGNED NULL;

-- Add foreign key constraints to customer_insurances table
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'customer_insurances' AND constraint_name = 'fk_customer_insurances_customer_id');
SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `customer_insurances` ADD CONSTRAINT `fk_customer_insurances_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE', 
    'SELECT "fk_customer_insurances_customer_id constraint already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add other customer_insurances foreign keys (with checks to prevent duplicates)
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'customer_insurances' AND constraint_name = 'fk_customer_insurances_insurance_company_id');
SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `customer_insurances` ADD CONSTRAINT `fk_customer_insurances_insurance_company_id` FOREIGN KEY (`insurance_company_id`) REFERENCES `insurance_companies` (`id`) ON DELETE SET NULL', 
    'SELECT "fk_customer_insurances_insurance_company_id constraint already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Continue with other constraints for customer_insurances...
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'customer_insurances' AND constraint_name = 'fk_customer_insurances_created_by');
SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `customer_insurances` ADD CONSTRAINT `fk_customer_insurances_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL', 
    'SELECT "fk_customer_insurances_created_by constraint already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2.3 Add foreign keys to family_groups if table exists
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'family_groups');
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'family_groups' AND constraint_name = 'fk_family_groups_created_by');

SET @sql = IF(@table_exists > 0 AND @constraint_exists = 0, 
    'ALTER TABLE `family_groups` 
     ADD CONSTRAINT `fk_family_groups_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
     ADD CONSTRAINT `fk_family_groups_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
     ADD CONSTRAINT `fk_family_groups_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
    'SELECT "family_groups table does not exist or constraints already exist" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2.4 Add foreign keys to family_members if table exists
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'family_members');
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'family_members' AND constraint_name = 'fk_family_members_family_group_id');

SET @sql = IF(@table_exists > 0 AND @constraint_exists = 0, 
    'ALTER TABLE `family_members` 
     ADD CONSTRAINT `fk_family_members_family_group_id` FOREIGN KEY (`family_group_id`) REFERENCES `family_groups` (`id`) ON DELETE CASCADE,
     ADD CONSTRAINT `fk_family_members_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE',
    'SELECT "family_members table does not exist or constraints already exist" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2.5 Add family_group_id foreign key to customers if column exists
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'customers' AND column_name = 'family_group_id');
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'customers' AND constraint_name = 'fk_customers_family_group_id');

SET @sql = IF(@column_exists > 0 AND @constraint_exists = 0, 
    'ALTER TABLE `customers` ADD CONSTRAINT `fk_customers_family_group_id` FOREIGN KEY (`family_group_id`) REFERENCES `family_groups` (`id`) ON DELETE SET NULL',
    'SELECT "family_group_id column does not exist or constraint already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2.6 Add foreign keys to quotation_companies if table exists
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'quotation_companies');
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'quotation_companies' AND constraint_name = 'fk_quotation_companies_quotation_id');

SET @sql = IF(@table_exists > 0 AND @constraint_exists = 0, 
    'ALTER TABLE `quotation_companies` 
     ADD CONSTRAINT `fk_quotation_companies_quotation_id` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE,
     ADD CONSTRAINT `fk_quotation_companies_insurance_company_id` FOREIGN KEY (`insurance_company_id`) REFERENCES `insurance_companies` (`id`) ON DELETE CASCADE',
    'SELECT "quotation_companies table does not exist or constraints already exist" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2.7 Add foreign keys to customer_audit_logs if table exists  
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'customer_audit_logs');
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'customer_audit_logs' AND constraint_name = 'fk_customer_audit_logs_customer_id');

SET @sql = IF(@table_exists > 0 AND @constraint_exists = 0, 
    'ALTER TABLE `customer_audit_logs` ADD CONSTRAINT `fk_customer_audit_logs_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE',
    'SELECT "customer_audit_logs table does not exist or constraint already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2.8 Add foreign keys to addon_covers if table exists
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'addon_covers');
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'addon_covers' AND constraint_name = 'fk_addon_covers_created_by');

SET @sql = IF(@table_exists > 0 AND @constraint_exists = 0, 
    'ALTER TABLE `addon_covers` 
     ADD CONSTRAINT `fk_addon_covers_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
     ADD CONSTRAINT `fk_addon_covers_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
     ADD CONSTRAINT `fk_addon_covers_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
    'SELECT "addon_covers table does not exist or constraints already exist" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2.9 Add foreign keys to quotations table
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'quotations' AND constraint_name = 'fk_quotations_customer_id');
SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `quotations` ADD CONSTRAINT `fk_quotations_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE', 
    'SELECT "fk_quotations_customer_id constraint already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add audit trail foreign keys to quotations
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'quotations' AND constraint_name = 'fk_quotations_created_by');
SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `quotations` ADD CONSTRAINT `fk_quotations_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL', 
    'SELECT "fk_quotations_created_by constraint already exists" as message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================================
-- PART 3: PERFORMANCE OPTIMIZATION INDEXES
-- =====================================================================

-- Add performance indexes for commonly queried columns
CREATE INDEX IF NOT EXISTS `idx_customers_email` ON `customers` (`email`);
CREATE INDEX IF NOT EXISTS `idx_customers_mobile` ON `customers` (`mobile_number`);
CREATE INDEX IF NOT EXISTS `idx_customers_status` ON `customers` (`status`);
CREATE INDEX IF NOT EXISTS `idx_customers_created_at` ON `customers` (`created_at`);
CREATE INDEX IF NOT EXISTS `idx_customers_family_group` ON `customers` (`family_group_id`);

CREATE INDEX IF NOT EXISTS `idx_customer_insurances_customer_id` ON `customer_insurances` (`customer_id`);
CREATE INDEX IF NOT EXISTS `idx_customer_insurances_status` ON `customer_insurances` (`status`);
CREATE INDEX IF NOT EXISTS `idx_customer_insurances_expired_date` ON `customer_insurances` (`expired_date`);
CREATE INDEX IF NOT EXISTS `idx_customer_insurances_policy_no` ON `customer_insurances` (`policy_no`);

CREATE INDEX IF NOT EXISTS `idx_quotations_customer_id` ON `quotations` (`customer_id`);
CREATE INDEX IF NOT EXISTS `idx_quotations_status` ON `quotations` (`status`);
CREATE INDEX IF NOT EXISTS `idx_quotations_created_at` ON `quotations` (`created_at`);

CREATE INDEX IF NOT EXISTS `idx_quotation_companies_quotation_id` ON `quotation_companies` (`quotation_id`);
CREATE INDEX IF NOT EXISTS `idx_quotation_companies_insurance_company` ON `quotation_companies` (`insurance_company_id`);
CREATE INDEX IF NOT EXISTS `idx_quotation_companies_recommended` ON `quotation_companies` (`is_recommended`);

-- =====================================================================
-- PART 4: POPULATE LARAVEL MIGRATIONS TABLE
-- =====================================================================

-- Clear existing migration records and repopulate
DELETE FROM `migrations`;

-- Insert all migration records to mark them as completed
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2019_12_14_000001_create_personal_access_tokens_table', 1),
('2024_05_28_164618_create_activity_log_table', 1),
('2024_05_28_164619_create_branches_table', 1),
('2024_05_28_164620_create_brokers_table', 1),
('2024_05_28_164621_create_customer_insurances_table', 1),
('2024_05_28_164622_create_customers_table', 1),
('2024_05_28_164623_create_failed_jobs_table', 1),
('2024_05_28_164624_create_fuel_types_table', 1),
('2024_05_28_164625_create_insurance_companies_table', 1),
('2024_05_28_164626_create_model_has_permissions_table', 1),
('2024_05_28_164627_create_model_has_roles_table', 1),
('2024_05_28_164628_create_password_resets_table', 1),
('2024_05_28_164629_create_permissions_table', 1),
('2024_05_28_164631_create_policy_types_table', 1),
('2024_05_28_164632_create_premium_types_table', 1),
('2024_05_28_164633_create_reference_users_table', 1),
('2024_05_28_164634_create_relationship_managers_table', 1),
('2024_05_28_164635_create_reports_table', 1),
('2024_05_28_164636_create_role_has_permissions_table', 1),
('2024_05_28_164637_create_roles_table', 1),
('2024_05_28_164638_create_users_table', 1),
('2025_08_21_173409_create_quotations_table', 2),
('2025_08_23_054202_create_quotation_companies_table', 2),
('2025_08_23_104556_add_tp_premium_to_quotation_companies_table', 2),
('2025_08_23_151450_add_ncb_percentage_to_quotations_table', 2),
('2025_08_24_084259_create_family_groups_table', 3),
('2025_08_24_084342_create_family_members_table', 3),
('2025_08_24_084427_add_family_group_id_to_customers_table', 3),
('2025_08_24_164118_make_customer_email_unique', 3),
('2025_08_24_164155_add_password_management_fields_to_customers', 3),
('2025_08_24_192003_create_customer_audit_logs_table', 3),
('2025_08_24_204606_add_quotation_permissions', 3),
('2025_08_25_020536_add_password_reset_token_to_customers', 4),
('2025_09_04_101456_drop_date_of_registration_from_quotations_table', 5),
('2025_09_04_103831_add_coverage_fields_to_quotation_companies_table', 5),
('2025_09_04_105205_remove_ncb_percentage_from_quotation_companies_table', 5),
('2025_09_04_111316_create_addon_covers_table', 5),
('2025_09_04_123455_drop_plan_name_from_quotation_companies_table', 5),
('2025_09_04_131746_add_recommendation_note_to_quotation_companies_table', 5),
('2025_09_04_133023_add_order_no_to_addon_covers_table', 5),
('2025_09_08_175453_add_missing_foreign_key_constraints', 6);

-- =====================================================================
-- PART 5: DATA INTEGRITY VALIDATION QUERIES
-- =====================================================================

-- Check for orphaned records that might cause foreign key issues
SELECT 'VALIDATION: Checking for orphaned records...' as status;

-- Check customers with invalid created_by references
SELECT COUNT(*) as orphaned_customers_created_by 
FROM customers c 
LEFT JOIN users u ON c.created_by = u.id 
WHERE c.created_by IS NOT NULL AND u.id IS NULL;

-- Check customer_insurances with invalid customer_id references
SELECT COUNT(*) as orphaned_customer_insurances 
FROM customer_insurances ci 
LEFT JOIN customers c ON ci.customer_id = c.id 
WHERE ci.customer_id IS NOT NULL AND c.id IS NULL;

-- Check quotations with invalid customer_id references
SELECT COUNT(*) as orphaned_quotations 
FROM quotations q 
LEFT JOIN customers c ON q.customer_id = c.id 
WHERE q.customer_id IS NOT NULL AND c.id IS NULL;

-- =====================================================================
-- PART 6: COMPLETION STATUS AND COMMIT
-- =====================================================================

-- If we reach here, all operations were successful
SELECT 'Database migration completed successfully!' as status, 
       NOW() as completed_at,
       'All pending migrations have been applied' as details;

-- Commit the transaction
COMMIT;

-- =====================================================================
-- POST-MIGRATION VERIFICATION QUERIES (OPTIONAL - RUN MANUALLY)
-- =====================================================================

/*
-- Verify new columns were added
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'quotation_companies' 
  AND COLUMN_NAME IN ('policy_type', 'idv_vehicle', 'total_idv', 'recommendation_note')
ORDER BY ORDINAL_POSITION;

-- Verify foreign key constraints were created
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
  AND CONSTRAINT_NAME LIKE 'fk_%'
ORDER BY TABLE_NAME, CONSTRAINT_NAME;

-- Verify Laravel migrations table is populated
SELECT migration, batch FROM migrations ORDER BY batch, migration;

-- Check indexes were created
SELECT DISTINCT INDEX_NAME, TABLE_NAME 
FROM information_schema.STATISTICS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND INDEX_NAME LIKE 'idx_%'
ORDER BY TABLE_NAME, INDEX_NAME;
*/

-- =====================================================================
-- END OF MIGRATION SCRIPT
-- =====================================================================