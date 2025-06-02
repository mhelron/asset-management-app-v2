<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ItemDistribution;
use App\Notifications\UserLowItemsNotification;
use Illuminate\Support\Facades\Log;

class CheckUserLowItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-user-low-items {threshold=3 : The threshold for low items}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for users with low quantities of distributed items and notify them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = $this->argument('threshold');
        $this->info("Checking for user distributions with quantity at or below threshold: {$threshold}");
        
        // Get all distributions where quantity_remaining is low but above 0
        // and the user hasn't been notified yet (we'll track this with a new column)
        $lowDistributions = ItemDistribution::where('quantity_remaining', '>', 0)
            ->where('quantity_remaining', '<=', $threshold)
            ->where('low_quantity_notified', false)
            ->with(['user', 'inventory'])
            ->get();
            
        $this->info("Found {$lowDistributions->count()} distributions with low quantities.");
        
        $notifiedCount = 0;
        
        foreach ($lowDistributions as $distribution) {
            try {
                $user = $distribution->user;
                $inventory = $distribution->inventory;
                
                if ($user && $inventory) {
                    $this->info("Notifying user {$user->id} about low quantity of {$inventory->item_name} ({$distribution->quantity_remaining} remaining)");
                    
                    // Send notification
                    $user->notify(new UserLowItemsNotification($distribution, $threshold));
                    
                    // Mark as notified
                    $distribution->update(['low_quantity_notified' => true]);
                    
                    $notifiedCount++;
                }
            } catch (\Exception $e) {
                $this->error("Error notifying user about distribution {$distribution->id}: {$e->getMessage()}");
                Log::error("Error in CheckUserLowItems command: {$e->getMessage()}", [
                    'distribution_id' => $distribution->id,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $this->info("Successfully notified {$notifiedCount} users about low item quantities.");
        
        return 0;
    }
} 