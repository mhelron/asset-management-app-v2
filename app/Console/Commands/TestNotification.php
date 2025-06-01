<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\AssetRequest;
use App\Notifications\ItemRequestNotification;
use Illuminate\Support\Facades\Log;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending a notification to admin users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing notification system...');
        
        // Find admin users using case-insensitive role names
        $adminUsers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Admin', 'admin', 'Manager', 'manager']);
        })->get();
        
        $this->info("Found {$adminUsers->count()} admin/manager users:");
        foreach ($adminUsers as $admin) {
            $this->line(" - {$admin->email} (Role: {$admin->user_role}, Spatie Roles: " . implode(', ', $admin->getRoleNames()->toArray()) . ")");
        }
        
        // Find the most recent asset request or create a test one
        $assetRequest = AssetRequest::latest()->first();
        
        if (!$assetRequest) {
            $this->info('No existing asset requests found. Creating a test request...');
            
            // Find first inventory item
            $inventory = \App\Models\Inventory::first();
            
            if (!$inventory) {
                $this->error('No inventory items found. Cannot create test request.');
                return Command::FAILURE;
            }
            
            // Create a test asset request
            $assetRequest = AssetRequest::create([
                'inventory_id' => $inventory->id,
                'user_id' => $adminUsers->first()->id,
                'reason' => 'Test request for notification debugging',
                'date_needed' => now()->addDays(3),
                'status' => 'Pending',
            ]);
            
            $this->info("Created test asset request with ID: {$assetRequest->id}");
        } else {
            $this->info("Using existing asset request with ID: {$assetRequest->id}");
        }
        
        // Send notifications
        $this->info('Sending notifications to admins and managers...');
        
        foreach ($adminUsers as $admin) {
            try {
                $this->line("Sending to {$admin->email}...");
                
                // Log for debugging
                Log::info('Sending test notification to user', [
                    'admin_id' => $admin->id,
                    'admin_email' => $admin->email
                ]);
                
                // Send notification
                $admin->notify(new ItemRequestNotification($assetRequest));
                
                $this->info("✓ Notification sent to {$admin->email}");
                
                // Log for debugging
                Log::info('Test notification sent successfully');
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$admin->email}: {$e->getMessage()}");
                
                // Log for debugging
                Log::error('Failed to send test notification', [
                    'admin_id' => $admin->id,
                    'admin_email' => $admin->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $this->info('Test completed. Check the database for notifications.');
        
        return Command::SUCCESS;
    }
} 