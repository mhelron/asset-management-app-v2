<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SyncUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:sync-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync roles for all users based on their user_role field';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting role synchronization for users...');
        
        // Ensure roles exist
        $roles = ['Admin', 'Manager', 'Staff'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
        
        // Get all users
        $users = User::all();
        $count = 0;
        
        foreach ($users as $user) {
            $roleName = ucfirst(strtolower($user->user_role));
            
            // Check if role exists
            if (!in_array($roleName, $roles)) {
                $this->warn("Skipping user {$user->email} - Invalid role: {$user->user_role}");
                continue;
            }
            
            // Sync role
            $user->syncRoles([$roleName]);
            $count++;
            
            $this->line("Assigned role '{$roleName}' to {$user->email}");
        }
        
        $this->info("Role synchronization completed. Updated {$count} users.");
        
        return Command::SUCCESS;
    }
} 