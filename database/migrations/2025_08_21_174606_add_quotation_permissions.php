<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create quotation permissions
        $permissions = [
            'quotation-list',
            'quotation-create', 
            'quotation-edit',
            'quotation-delete',
            'quotation-generate',
            'quotation-send-whatsapp',
            'quotation-download-pdf',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Assign all quotation permissions to admin role (assuming role ID 1 is admin)
        $adminRole = Role::find(1);
        if ($adminRole) {
            $quotationPermissions = Permission::whereIn('name', $permissions)->get();
            $adminRole->givePermissionTo($quotationPermissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove quotation permissions
        $permissions = [
            'quotation-list',
            'quotation-create', 
            'quotation-edit',
            'quotation-delete',
            'quotation-generate',
            'quotation-send-whatsapp',
            'quotation-download-pdf',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
