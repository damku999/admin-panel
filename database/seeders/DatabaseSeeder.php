<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // Core setup seeders
            AdminSeeder::class,
            CustomAdminSeeder::class,
            RoleSeeder::class,
            UnifiedPermissionsSeeder::class, // Replaces PermissionSeeder, QuotationPermissionsSeeder, ClaimPermissionsSeeder

            // Lookup table seeders (must run before data migration)
            CustomerTypesSeeder::class,
            CommissionTypesSeeder::class,
            QuotationStatusesSeeder::class,
            AddonCoversSeeder::class,

            // Data migration seeders (must run at the end)
            EmailCleanupSeeder::class,
            DataMigrationSeeder::class,
        ]);
    }
}
