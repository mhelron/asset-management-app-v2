<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventory;
use App\Models\User;
use App\Models\AssetType;
use App\Notifications\LowQuantityNotification;
use Illuminate\Support\Facades\Log;

class TestLowQuantityNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:low-quantity {--force : Force send notifications even for items already notified}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test low quantity notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing low quantity notification system...');
        
        // Find admin users using case-insensitive role names
        $adminUsers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Admin', 'admin', 'Manager', 'manager']);
        })->get();
        
        $this->info("Found {$adminUsers->count()} admin/manager users:");
        foreach ($adminUsers as $admin) {
            $this->line(" - {$admin->email} (Role: {$admin->user_role}, Spatie Roles: " . implode(', ', $admin->getRoleNames()->toArray()) . ")");
        }
        
        // Get all asset types with quantity tracking
        $assetTypes = AssetType::where('has_quantity', true)
            ->where('status', 'Active')
            ->get();
            
        $this->info("Found {$assetTypes->count()} asset types with quantity tracking");
        
        // Find low quantity items
        $lowQuantityItems = collect();
        
        foreach ($assetTypes as $assetType) {
            $query = $assetType->inventories()
                ->whereNotNull('quantity')
                ->whereNotNull('min_quantity')
                ->whereRaw('quantity <= min_quantity');
                
            // If not forcing, only get items not yet notified
            if (!$this->option('force')) {
                $query->where('low_quantity_notified', false);
            }
            
            $items = $query->get();
            $lowQuantityItems = $lowQuantityItems->merge($items);
        }
        
        $this->info("Found {$lowQuantityItems->count()} items with low quantity");
        
        if ($lowQuantityItems->count() == 0) {
            // Create a test item with low quantity
            $this->info("Creating a test item with low quantity...");
            
            // Find an inventory item to modify
            $inventory = Inventory::whereNotNull('min_quantity')
                ->where('has_quantity', true)
                ->first();
                
            if (!$inventory) {
                $this->error("No suitable inventory items found for testing. Please create an inventory item with quantity tracking.");
                return Command::FAILURE;
            }
            
            // Backup original values
            $originalQuantity = $inventory->quantity;
            $originalNotified = $inventory->low_quantity_notified;
            
            // Set quantity below minimum
            $inventory->update([
                'quantity' => max(0, $inventory->min_quantity - 1),
                'low_quantity_notified' => false
            ]);
            
            $this->info("Modified item '{$inventory->item_name}' to have low quantity (Quantity: {$inventory->quantity}, Min: {$inventory->min_quantity})");
            
            // Add to collection
            $lowQuantityItems = collect([$inventory]);
        }
        
        // Send notifications
        $this->info('Sending low quantity notifications...');
        
        foreach ($lowQuantityItems as $item) {
            $this->line("Item: {$item->item_name} (Quantity: {$item->quantity}, Min: {$item->min_quantity})");
            
            foreach ($adminUsers as $admin) {
                try {
                    $this->line("  Sending to {$admin->email}...");
                    
                    // Log for debugging
                    Log::info('Sending low quantity notification', [
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                        'item_id' => $item->id,
                        'item_name' => $item->item_name
                    ]);
                    
                    // Send notification
                    $admin->notify(new LowQuantityNotification($item));
                    
                    // Mark as notified
                    $item->update(['low_quantity_notified' => true]);
                    
                    $this->info("  ✓ Notification sent to {$admin->email}");
                    
                    // Log for debugging
                    Log::info('Low quantity notification sent successfully');
                } catch (\Exception $e) {
                    $this->error("  ✗ Failed to send to {$admin->email}: {$e->getMessage()}");
                    
                    // Log for debugging
                    Log::error('Failed to send low quantity notification', [
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                        'item_id' => $item->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        }
        
        $this->info('Test completed. Check the database for notifications.');
        
        return Command::SUCCESS;
    }
} 