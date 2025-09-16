<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FixClaimPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'claims:fix-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign claim permissions to Super Admin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get Super Admin role (ID: 1)
        $role = Role::find(1);

        if (!$role) {
            $this->error('Super Admin role not found!');
            return Command::FAILURE;
        }

        // Get all claim permissions
        $claimPermissions = Permission::where('name', 'like', 'claim-%')->get();

        if ($claimPermissions->count() === 0) {
            $this->error('No claim permissions found in database.');
            return Command::FAILURE;
        }

        // Assign all claim permissions to Super Admin role
        $role->givePermissionTo($claimPermissions);

        $this->info('Claim permissions assigned to Super Admin role successfully!');

        // List the assigned permissions
        $this->info('Assigned permissions:');
        foreach($claimPermissions as $permission) {
            $this->line('- ' . $permission->name);
        }

        return Command::SUCCESS;
    }
}
