# Database Sync Commands - Live to Local Migration Sync

## ⚠️ IMPORTANT ANALYSIS

Your live database contains **27 tables** but the `migrations` table is **empty**, meaning no migration records exist.
Your local codebase has **49 migration files** that are all marked as "Pending".

## 🎯 SITUATION OVERVIEW

**Live Database Tables Found:**
- All core insurance system tables exist
- Tables created but no migration history recorded
- Missing some newer tables from recent migrations

**Missing Tables in Live Database:**
Based on migration files vs live tables, these are likely missing:
- `addon_covers` (from 2025_09_04_111316)
- `claims` (from 2025_01_15_180000)
- `claim_stages` (from 2025_01_15_180001)
- `claim_documents` (from 2025_01_15_180002)
- `claim_liability_details` (from 2025_01_15_180003)
- `message_queue` (from 2024_09_09_000001)
- `delivery_status` (from 2024_09_09_000002)
- `notification_templates` (from 2024_09_09_000003)
- `communication_preferences` (from 2024_09_09_000004)
- `event_store` (from 2024_09_09_140000)

## 🚀 RECOMMENDED APPROACH

### Option 1: Safe Incremental Sync (RECOMMENDED)

```bash
# 1. BACKUP FIRST (CRITICAL!)
mysqldump -u [username] -p [database_name] > backup_before_sync_$(date +%Y%m%d_%H%M%S).sql

# 2. Mark existing tables as migrated (DRY RUN - Review first)
echo "--- DRY RUN: Mark existing migrations as completed ---"
php artisan migrate:status

# 3. Manually insert migration records for existing tables
php artisan tinker
```

```php
// In Tinker - Mark existing tables as migrated
$existingTables = [
    '2019_12_14_000001_create_personal_access_tokens_table',
    '2024_05_28_164618_create_activity_log_table',
    '2024_05_28_164619_create_branches_table',
    '2024_05_28_164620_create_brokers_table',
    '2024_05_28_164621_create_customer_insurances_table',
    '2024_05_28_164622_create_customers_table',
    '2024_05_28_164623_create_failed_jobs_table',
    '2024_05_28_164624_create_fuel_types_table',
    '2024_05_28_164625_create_insurance_companies_table',
    '2024_05_28_164626_create_model_has_permissions_table',
    '2024_05_28_164627_create_model_has_roles_table',
    '2024_05_28_164628_create_password_resets_table',
    '2024_05_28_164629_create_permissions_table',
    '2024_05_28_164631_create_policy_types_table',
    '2024_05_28_164632_create_premium_types_table',
    '2024_05_28_164633_create_reference_users_table',
    '2024_05_28_164634_create_relationship_managers_table',
    '2024_05_28_164635_create_reports_table',
    '2024_05_28_164636_create_role_has_permissions_table',
    '2024_05_28_164637_create_roles_table',
    '2024_05_28_164638_create_users_table',
    '2025_08_21_173409_create_quotations_table',
    '2025_08_23_054202_create_quotation_companies_table',
    '2025_08_23_104556_add_tp_premium_to_quotation_companies_table',
    '2025_08_23_151450_add_ncb_percentage_to_quotations_table',
    '2025_08_24_084259_create_family_groups_table',
    '2025_08_24_084342_create_family_members_table',
    '2025_08_24_084427_add_family_group_id_to_customers_table',
    '2025_08_24_164118_make_customer_email_unique',
    '2025_08_24_164155_add_password_management_fields_to_customers',
    '2025_08_24_192003_create_customer_audit_logs_table',
    '2025_08_24_204606_add_quotation_permissions',
    '2025_08_25_020536_add_password_reset_token_to_customers',
    '2025_09_04_101456_drop_date_of_registration_from_quotations_table',
    '2025_09_04_103831_add_coverage_fields_to_quotation_companies_table',
    '2025_09_04_105205_remove_ncb_percentage_from_quotation_companies_table',
    '2025_09_04_123455_drop_plan_name_from_quotation_companies_table',
    '2025_09_04_131746_add_recommendation_note_to_quotation_companies_table',
    '2025_09_04_133023_add_order_no_to_addon_covers_table',
    '2025_09_08_175453_add_missing_foreign_key_constraints',
    '2025_09_16_101446_add_liability_fields_to_claim_liability_details_table'
];

// Mark as batch 1 (existing database)
foreach ($existingTables as $migration) {
    DB::table('migrations')->insert([
        'migration' => $migration,
        'batch' => 1
    ]);
    echo "Marked: $migration\n";
}

exit;
```

```bash
# 4. Run remaining migrations for missing tables
php artisan migrate --dry-run  # Review what will be created
php artisan migrate             # Execute missing migrations

# 5. Verify migration status
php artisan migrate:status
```

### Option 2: Full Fresh Migration (DESTRUCTIVE - Use with caution)

```bash
# ⚠️ DESTROYS ALL DATA - Only for fresh start
php artisan migrate:fresh --seed --force
```

## 📋 MISSING TABLES THAT WILL BE CREATED

These migrations will create new tables:

```bash
# Claim Management System
2025_01_15_180000_create_claims_table
2025_01_15_180001_create_claim_stages_table
2025_01_15_180002_create_claim_documents_table
2025_01_15_180003_create_claim_liability_details_table

# Addon Covers
2025_09_04_111316_create_addon_covers_table

# Communication System
2024_09_09_000001_create_message_queue_table
2024_09_09_000002_create_delivery_status_table
2024_09_09_000003_create_notification_templates_table
2024_09_09_000004_create_communication_preferences_table

# Event Sourcing
2024_09_09_140000_create_event_store_table

# Performance & Optimization
2024_09_09_100000_add_foreign_key_constraints
2024_09_09_100001_add_performance_indexes
2024_09_09_100002_optimize_enum_compatibility
```

## 🔧 PERMISSION & ROLE SETUP COMMANDS

### Check Current Permissions & Roles

```bash
# Check existing permissions
php artisan tinker
Permission::count();  // Check if permissions exist
Role::count();        // Check if roles exist
User::count();        // Check if users exist
exit;
```

### Setup Roles & Permissions (if needed)

```bash
# Create admin user and basic roles/permissions
php artisan db:seed --class=RolePermissionSeeder  # If seeder exists
# OR manually via tinker:
php artisan tinker
```

```php
// Create basic roles
$adminRole = Spatie\Permission\Models\Role::create(['name' => 'Admin', 'guard_name' => 'web']);
$userRole = Spatie\Permission\Models\Role::create(['name' => 'User', 'guard_name' => 'web']);

// Create permissions
$permissions = [
    'quotations.create', 'quotations.read', 'quotations.update', 'quotations.delete',
    'customers.create', 'customers.read', 'customers.update', 'customers.delete',
    'brokers.create', 'brokers.read', 'brokers.update', 'brokers.delete',
    'reports.read', 'reports.create', 'users.manage', 'system.admin'
];

foreach ($permissions as $permission) {
    Spatie\Permission\Models\Permission::create(['name' => $permission, 'guard_name' => 'web']);
}

// Assign all permissions to admin
$adminRole->syncPermissions(Spatie\Permission\Models\Permission::all());

// Assign Admin role to existing user
$admin = App\Models\User::where('email', 'parthrawal89@gmail.com')->first();

if ($admin) {
    $admin->roles()->detach(); // Remove existing roles
    $admin->assignRole('Admin');
    echo "Admin role assigned to existing user: parthrawal89@gmail.com\n";
} else {
    echo "ERROR: Admin user not found! Please ensure parthrawal89@gmail.com exists in database.\n";
}
exit;
```

## 🏁 FINAL VERIFICATION COMMANDS

```bash
# 1. Check migration status
php artisan migrate:status

# 2. Check table count
php artisan tinker
echo "Tables: " . count(DB::select('SHOW TABLES'));
echo "Permissions: " . Permission::count();
echo "Roles: " . Role::count();
echo "Users: " . User::count();
exit;

# 3. Check application health
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Test login
# Navigate to /login and use parthrawal89@gmail.com / Devyaan@1967

# 5. Check logs for errors
tail -f storage/logs/laravel.log
```

## 📊 EXECUTION CHECKLIST

- [ ] **BACKUP DATABASE** (mysqldump)
- [ ] Review migration files for data compatibility
- [ ] Mark existing tables as migrated in migrations table
- [ ] Run pending migrations for missing tables
- [ ] Setup roles and permissions
- [ ] Create admin user
- [ ] Test login functionality
- [ ] Verify all features work
- [ ] Clear application caches
- [ ] Monitor logs for errors

## ⚠️ WARNINGS

1. **Always backup before running migrations**
2. **Test in development environment first**
3. **Enum columns may cause issues with some MySQL versions**
4. **Foreign key constraints will be added - ensure data integrity**
5. **Some migrations modify existing columns - review for data loss**

## 🔍 TROUBLESHOOTING

If you encounter enum errors:
```bash
# Fix enum compatibility
php artisan migrate --step --pretend  # See what each migration does
```

If foreign key errors occur:
```bash
# Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;
# Run migrations
# Re-enable checks
SET FOREIGN_KEY_CHECKS = 1;
```