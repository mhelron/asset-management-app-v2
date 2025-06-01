<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventory;
use App\Models\AssetType;

class CreateTestInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test inventory items for low quantity testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating test inventory items with quantity tracking...');
        
        // First, make sure we have an asset type with quantity tracking
        $assetType = AssetType::where('has_quantity', true)->first();
        
        if (!$assetType) {
            $this->info('Creating a new asset type with quantity tracking...');
            
            $assetType = AssetType::create([
                'name' => 'Test Consumable',
                'desc' => 'Test asset type for quantity tracking',
                'status' => 'Active',
                'requires_qr_code' => false,
                'is_requestable' => true,
                'has_quantity' => true,
            ]);
            
            $this->info("Created asset type: {$assetType->name} (ID: {$assetType->id})");
        } else {
            $this->info("Using existing asset type: {$assetType->name} (ID: {$assetType->id})");
        }
        
        // Create test inventory item with quantity tracking
        $inventory = Inventory::create([
            'item_name' => 'Test Item - Low Quantity',
            'asset_type_id' => $assetType->id,
            'quantity' => 5,
            'min_quantity' => 10,
            'max_quantity' => 50,
            'low_quantity_notified' => false,
            'asset_tag' => 'TEST-' . rand(1000, 9999),
            'serial_no' => 'SN-TEST-' . rand(1000, 9999),
            'model_no' => 'MODEL-TEST',
            'manufacturer' => 'Test Manufacturer',
            'date_purchased' => now(),
            'purchased_from' => 'Test Supplier',
            'status' => 'Active',
        ]);
        
        $this->info("Created test inventory item: {$inventory->item_name}");
        $this->info("Quantity: {$inventory->quantity}, Min: {$inventory->min_quantity}, Max: {$inventory->max_quantity}");
        $this->info("This item has low quantity and should trigger notifications");
        
        // Create another item that's not low
        $inventory2 = Inventory::create([
            'item_name' => 'Test Item - Normal Quantity',
            'asset_type_id' => $assetType->id,
            'quantity' => 20,
            'min_quantity' => 10,
            'max_quantity' => 50,
            'low_quantity_notified' => false,
            'asset_tag' => 'TEST-' . rand(1000, 9999),
            'serial_no' => 'SN-TEST-' . rand(1000, 9999),
            'model_no' => 'MODEL-TEST',
            'manufacturer' => 'Test Manufacturer',
            'date_purchased' => now(),
            'purchased_from' => 'Test Supplier',
            'status' => 'Active',
        ]);
        
        $this->info("Created test inventory item: {$inventory2->item_name}");
        $this->info("Quantity: {$inventory2->quantity}, Min: {$inventory2->min_quantity}, Max: {$inventory2->max_quantity}");
        $this->info("This item has normal quantity and should NOT trigger notifications");
        
        $this->info('');
        $this->info('Test inventory items created successfully.');
        $this->info('You can now run the test:low-quantity command to test notifications.');
        
        return Command::SUCCESS;
    }
} 