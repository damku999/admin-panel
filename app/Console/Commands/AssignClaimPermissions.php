<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignClaimPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'claims:assign-permissions {user_email? : User email or "list" to show all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign claim permissions to a user or give admin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('user_email');
        
        if (!$email) {
            $email = $this->ask('Enter user email (or "list" to show all users)');
        }

        if ($email === 'list') {
            $this->info("📋 All Users in System:");
            foreach(User::all() as $user) {
                $roles = $user->getRoleNames()->implode(', ');
                $this->line("• {$user->name} - {$user->email} (Roles: {$roles})");
            }
            return;
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found!");
            $this->info("Available users:");
            foreach(User::all() as $u) {
                $this->line("• {$u->name} - {$u->email}");
            }
            return;
        }

        // Check if user already has admin role
        if ($user->hasRole('Admin')) {
            $this->info("User {$user->name} already has Admin role with all claim permissions.");
            return;
        }

        // Try to assign admin role first
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $user->assignRole('Admin');
            $this->info("✅ Assigned Admin role to {$user->name}");
        } else {
            // If no admin role, assign permissions directly
            $claimPermissions = [
                'claim-list',
                'claim-create', 
                'claim-edit',
                'claim-delete',
                'claim-export'
            ];

            foreach ($claimPermissions as $permission) {
                $permissionModel = Permission::where('name', $permission)->first();
                if ($permissionModel) {
                    $user->givePermissionTo($permission);
                }
            }
            
            $this->info("✅ Assigned claim permissions directly to {$user->name}");
        }

        $this->info("User {$user->name} can now access Claims Management module.");
    }
}
