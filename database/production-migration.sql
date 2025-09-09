-- Production Database Migration SQL
-- Execute these queries in the exact order listed
-- BACKUP YOUR DATABASE BEFORE RUNNING THESE COMMANDS

-- =================================================================
-- FOREIGN KEY CONSTRAINTS MIGRATION
-- =================================================================

-- 1. Fix customers table constraints
ALTER TABLE `customers` 
MODIFY COLUMN `created_by` BIGINT UNSIGNED NULL,
MODIFY COLUMN `updated_by` BIGINT UNSIGNED NULL,
MODIFY COLUMN `deleted_by` BIGINT UNSIGNED NULL;

-- Add foreign key constraints to customers table
ALTER TABLE `customers` 
ADD CONSTRAINT `fk_customers_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_customers_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_customers_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- 2. Fix customer_insurances table constraints
ALTER TABLE `customer_insurances`
MODIFY COLUMN `created_by` BIGINT UNSIGNED NULL,
MODIFY COLUMN `updated_by` BIGINT UNSIGNED NULL,
MODIFY COLUMN `deleted_by` BIGINT UNSIGNED NULL;

-- Add foreign key constraints to customer_insurances table
ALTER TABLE `customer_insurances`
ADD CONSTRAINT `fk_customer_insurances_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_customer_insurances_insurance_company_id` FOREIGN KEY (`insurance_company_id`) REFERENCES `insurance_companies` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_customer_insurances_policy_type_id` FOREIGN KEY (`policy_type_id`) REFERENCES `policy_types` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_customer_insurances_premium_type_id` FOREIGN KEY (`premium_type_id`) REFERENCES `premium_types` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_customer_insurances_fuel_type_id` FOREIGN KEY (`fuel_type_id`) REFERENCES `fuel_types` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_customer_insurances_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_customer_insurances_broker_id` FOREIGN KEY (`broker_id`) REFERENCES `brokers` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_customer_insurances_relationship_manager_id` FOREIGN KEY (`relationship_manager_id`) REFERENCES `relationship_managers` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_customer_insurances_reference_by` FOREIGN KEY (`reference_by`) REFERENCES `reference_users` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_customer_insurances_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_customer_insurances_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_customer_insurances_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- 3. Add foreign keys to family_groups if table exists
-- Check if table exists before adding constraints
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'family_groups');

SET @sql = IF(@table_exists > 0, 
    'ALTER TABLE `family_groups` 
     ADD CONSTRAINT `fk_family_groups_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
     ADD CONSTRAINT `fk_family_groups_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
     ADD CONSTRAINT `fk_family_groups_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
    'SELECT "family_groups table does not exist" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. Add foreign keys to family_members if table exists
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'family_members');

SET @sql = IF(@table_exists > 0, 
    'ALTER TABLE `family_members` 
     ADD CONSTRAINT `fk_family_members_family_group_id` FOREIGN KEY (`family_group_id`) REFERENCES `family_groups` (`id`) ON DELETE CASCADE,
     ADD CONSTRAINT `fk_family_members_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE',
    'SELECT "family_members table does not exist" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5. Add family_group_id foreign key to customers if column exists
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'customers' AND column_name = 'family_group_id');

SET @sql = IF(@column_exists > 0, 
    'ALTER TABLE `customers` ADD CONSTRAINT `fk_customers_family_group_id` FOREIGN KEY (`family_group_id`) REFERENCES `family_groups` (`id`) ON DELETE SET NULL',
    'SELECT "family_group_id column does not exist in customers table" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 6. Add foreign keys to quotation_companies if table exists
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'quotation_companies');

SET @sql = IF(@table_exists > 0, 
    'ALTER TABLE `quotation_companies` 
     ADD CONSTRAINT `fk_quotation_companies_quotation_id` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE,
     ADD CONSTRAINT `fk_quotation_companies_insurance_company_id` FOREIGN KEY (`insurance_company_id`) REFERENCES `insurance_companies` (`id`) ON DELETE CASCADE',
    'SELECT "quotation_companies table does not exist" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 7. Add foreign keys to customer_audit_logs if table exists  
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'customer_audit_logs');

SET @sql = IF(@table_exists > 0, 
    'ALTER TABLE `customer_audit_logs` ADD CONSTRAINT `fk_customer_audit_logs_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE',
    'SELECT "customer_audit_logs table does not exist" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 8. Add foreign keys to addon_covers if table exists
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'addon_covers');

SET @sql = IF(@table_exists > 0, 
    'ALTER TABLE `addon_covers` 
     ADD CONSTRAINT `fk_addon_covers_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
     ADD CONSTRAINT `fk_addon_covers_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
     ADD CONSTRAINT `fk_addon_covers_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
    'SELECT "addon_covers table does not exist" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =================================================================
-- PERFORMANCE OPTIMIZATION INDEXES
-- =================================================================

-- Add performance indexes for commonly queried columns
CREATE INDEX IF NOT EXISTS `idx_customers_email` ON `customers` (`email`);
CREATE INDEX IF NOT EXISTS `idx_customers_mobile` ON `customers` (`mobile_number`);
CREATE INDEX IF NOT EXISTS `idx_customers_status` ON `customers` (`status`);
CREATE INDEX IF NOT EXISTS `idx_customers_created_at` ON `customers` (`created_at`);

CREATE INDEX IF NOT EXISTS `idx_customer_insurances_customer_id` ON `customer_insurances` (`customer_id`);
CREATE INDEX IF NOT EXISTS `idx_customer_insurances_status` ON `customer_insurances` (`status`);
CREATE INDEX IF NOT EXISTS `idx_customer_insurances_expired_date` ON `customer_insurances` (`expired_date`);
CREATE INDEX IF NOT EXISTS `idx_customer_insurances_policy_no` ON `customer_insurances` (`policy_no`);

CREATE INDEX IF NOT EXISTS `idx_quotations_customer_id` ON `quotations` (`customer_id`);
CREATE INDEX IF NOT EXISTS `idx_quotations_status` ON `quotations` (`status`);
CREATE INDEX IF NOT EXISTS `idx_quotations_created_at` ON `quotations` (`created_at`);

-- =================================================================
-- DATA INTEGRITY VALIDATION
-- =================================================================

-- Run these queries to validate data integrity after migration
-- SELECT COUNT(*) as orphaned_customers FROM customers c LEFT JOIN users u ON c.created_by = u.id WHERE c.created_by IS NOT NULL AND u.id IS NULL;
-- SELECT COUNT(*) as orphaned_customer_insurances FROM customer_insurances ci LEFT JOIN customers c ON ci.customer_id = c.id WHERE ci.customer_id IS NOT NULL AND c.id IS NULL;

-- =================================================================
-- COMPLETION STATUS
-- =================================================================
SELECT 'Database optimization migration completed successfully' as status, NOW() as completed_at;