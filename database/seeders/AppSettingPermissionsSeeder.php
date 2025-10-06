<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppSettingPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for App Settings
        $permissions = [
            'app-setting-list',
            'app-setting-create',
            'app-setting-edit',
            'app-setting-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all app-setting permissions to Admin role
        $adminRole = Role::where('name', 'Admin')->first();

        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            echo "✓ App Setting permissions assigned to Admin role\n";
        }

        // Also assign to Super Admin if it exists
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
            echo "✓ App Setting permissions assigned to Super Admin role\n";
        }

        echo "✓ App Setting permissions created successfully\n";
    }
}
