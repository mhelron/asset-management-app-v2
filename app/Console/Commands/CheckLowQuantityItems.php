<?php

namespace App\Console\Commands;

use App\Models\AssetType;
use App\Models\Inventory;
use App\Models\User;
use App\Notifications\LowQuantityNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckLowQuantityItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-low-quantity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for inventory items with low quantity and send notifications';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking for low quantity inventory items...');
        
        // Get all asset types that have quantity tracking
        $assetTypes = AssetType::where('has_quantity', true)
            ->where('status', 'Active')
            ->get();
            
        $this->info('Found ' . $assetTypes->count() . ' asset types with quantity tracking');
        
        $lowQuantityItems = collect();
        
        // Check each asset type for low quantity items
        foreach ($assetTypes as $assetType) {
            $items = $assetType->getLowQuantityItems();
            $lowQuantityItems = $lowQuantityItems->merge($items);
        }
        
        $this->info('Found ' . $lowQuantityItems->count() . ' items with low quantity');
        
        // If there are low quantity items, send notifications
        if ($lowQuantityItems->count() > 0) {
            // Get admin and manager users to notify
            $admins = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Admin', 'admin', 'Manager', 'manager']);
            })->get();
            
            if ($admins->isEmpty()) {
                $this->warn('No admin or manager users found to notify');
                Log::warning('No admin or manager users found to notify about low quantity items');
                return 1;
            }
            
            foreach ($lowQuantityItems as $item) {
                $this->info('Sending notification for ' . $item->item_name . ' (Quantity: ' . $item->quantity . ')');
                
                // Send notification to all admins and managers
                foreach ($admins as $admin) {
                    $admin->notify(new LowQuantityNotification($item));
                }
                
                // Mark as notified
                $item->update(['low_quantity_notified' => true]);
            }
            
            $this->info('Notifications sent successfully');
        }
        
        return 0;
    }
} 