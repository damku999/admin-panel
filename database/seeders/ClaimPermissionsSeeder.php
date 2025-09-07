<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ClaimPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create claim permissions
        $permissions = [
            'claim-list',
            'claim-create', 
            'claim-edit',
            'claim-delete',
            'claim-export'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Assign claim permissions to Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            echo "✅ Assigned claim permissions to Admin role\n";
        }

        // Also check for other admin-like roles
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
            echo "✅ Assigned claim permissions to Super Admin role\n";
        }

        echo "✅ Created " . count($permissions) . " claim permissions\n";
    }
}
