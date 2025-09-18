<?php
/**
 * Database Sync Script - Safely sync local migrations with live database
 *
 * This script will:
 * 1. Mark existing tables as migrated
 * 2. Run only missing migrations
 * 3. Setup basic permissions and roles
 * 4. Create admin user if needed
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

echo "=== DATABASE SYNC SCRIPT ===\n\n";

// 1. Check current database state
echo "1. Checking current database state...\n";
$tables = DB::select('SHOW TABLES');
$tableCount = count($tables);
echo "   Found $tableCount tables in database\n";

// Check migrations table
$migrationCount = DB::table('migrations')->count();
echo "   Found $migrationCount migration records\n\n";

if ($migrationCount == 0) {
    echo "2. Marking existing tables as migrated...\n";

    // List of existing tables to mark as migrated
    $existingMigrations = [
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

    $batch = 1;
    foreach ($existingMigrations as $migration) {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch
        ]);
        echo "   ✓ Marked: $migration\n";
    }
    echo "   Marked " . count($existingMigrations) . " existing migrations as batch $batch\n\n";
} else {
    echo "2. Migration records already exist, skipping marking step\n\n";
}

// 3. Run remaining migrations
echo "3. Running missing migrations...\n";
try {
    Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();
    echo $output;
    echo "   ✓ Migrations completed successfully\n\n";
} catch (Exception $e) {
    echo "   ❌ Migration error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 4. Setup roles and permissions
echo "4. Setting up roles and permissions...\n";

// Check if permissions already exist
$permissionCount = Permission::count();
$roleCount = Role::count();

if ($permissionCount == 0) {
    echo "   Creating permissions...\n";

    $permissions = [
        // Quotation permissions
        'quotations.create', 'quotations.read', 'quotations.update', 'quotations.delete', 'quotations.export',

        // Customer permissions
        'customers.create', 'customers.read', 'customers.update', 'customers.delete', 'customers.export',

        // Broker permissions
        'brokers.create', 'brokers.read', 'brokers.update', 'brokers.delete', 'brokers.export',

        // Insurance company permissions
        'insurance-companies.create', 'insurance-companies.read', 'insurance-companies.update', 'insurance-companies.delete', 'insurance-companies.export',

        // Policy and premium type permissions
        'policy-types.create', 'policy-types.read', 'policy-types.update', 'policy-types.delete',
        'premium-types.create', 'premium-types.read', 'premium-types.update', 'premium-types.delete',
        'fuel-types.create', 'fuel-types.read', 'fuel-types.update', 'fuel-types.delete',
        'branches.create', 'branches.read', 'branches.update', 'branches.delete',
        'reference-users.create', 'reference-users.read', 'reference-users.update', 'reference-users.delete',
        'relationship-managers.create', 'relationship-managers.read', 'relationship-managers.update', 'relationship-managers.delete',

        // Family group permissions
        'family-groups.create', 'family-groups.read', 'family-groups.update', 'family-groups.delete',

        // Claim permissions
        'claims.create', 'claims.read', 'claims.update', 'claims.delete', 'claims.export',

        // Report permissions
        'reports.create', 'reports.read', 'reports.update', 'reports.delete', 'reports.export',

        // User management permissions
        'users.create', 'users.read', 'users.update', 'users.delete', 'users.export',

        // Admin permissions
        'admin.dashboard', 'admin.settings', 'admin.logs', 'admin.system',

        // Export permissions
        'exports.all'
    ];

    foreach ($permissions as $permission) {
        Permission::create(['name' => $permission, 'guard_name' => 'web']);
        echo "   ✓ Created permission: $permission\n";
    }

    echo "   Created " . count($permissions) . " permissions\n\n";
} else {
    echo "   Permissions already exist ($permissionCount found)\n\n";
}

if ($roleCount == 0) {
    echo "   Creating roles...\n";

    // Create Admin role with all permissions
    $adminRole = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
    $adminRole->syncPermissions(Permission::all());
    echo "   ✓ Created Admin role with all permissions\n";

    // Create Manager role with limited permissions
    $managerRole = Role::create(['name' => 'Manager', 'guard_name' => 'web']);
    $managerPermissions = Permission::where('name', 'not like', 'users.%')
        ->where('name', 'not like', 'admin.%')
        ->get();
    $managerRole->syncPermissions($managerPermissions);
    echo "   ✓ Created Manager role with limited permissions\n";

    // Create User role with read-only permissions
    $userRole = Role::create(['name' => 'User', 'guard_name' => 'web']);
    $userPermissions = Permission::where('name', 'like', '%.read')->get();
    $userRole->syncPermissions($userPermissions);
    echo "   ✓ Created User role with read permissions\n";

} else {
    echo "   Roles already exist ($roleCount found)\n\n";
}

// 5. Setup existing admin user with proper role
echo "5. Setting up admin user roles...\n";
$adminUser = User::where('email', 'parthrawal89@gmail.com')->first();

if (!$adminUser) {
    echo "   ❌ Admin user (parthrawal89@gmail.com) not found in database!\n";
    echo "   Please ensure the user exists before running this script.\n\n";
} else {
    $adminRole = Role::where('name', 'Admin')->first();
    if ($adminRole) {
        // Remove any existing roles first
        $adminUser->roles()->detach();
        // Assign Admin role
        $adminUser->assignRole($adminRole);
        echo "   ✓ Assigned Admin role to existing user\n";
        echo "   📧 Admin Email: parthrawal89@gmail.com\n";
        echo "   🔐 Using existing password: Devyaan@1967\n\n";
    } else {
        echo "   ❌ Admin role not found!\n\n";
    }
}

// 6. Final verification
echo "6. Final verification...\n";
$finalTableCount = count(DB::select('SHOW TABLES'));
$finalMigrationCount = DB::table('migrations')->count();
$finalPermissionCount = Permission::count();
$finalRoleCount = Role::count();
$finalUserCount = User::count();

echo "   📊 Database Statistics:\n";
echo "   - Tables: $finalTableCount\n";
echo "   - Migration records: $finalMigrationCount\n";
echo "   - Permissions: $finalPermissionCount\n";
echo "   - Roles: $finalRoleCount\n";
echo "   - Users: $finalUserCount\n\n";

echo "✅ DATABASE SYNC COMPLETED SUCCESSFULLY!\n\n";

echo "🚀 Next steps:\n";
echo "1. Test login at: /login\n";
echo "2. Use credentials: parthrawal89@gmail.com / Devyaan@1967\n";
echo "3. Check application functionality\n";
echo "4. Monitor logs: tail -f storage/logs/laravel.log\n";
echo "5. Clear caches if needed:\n";
echo "   php artisan config:cache\n";
echo "   php artisan route:cache\n";
echo "   php artisan view:cache\n\n";