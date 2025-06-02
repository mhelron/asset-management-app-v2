<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration marks all the obsolete migrations as completed
        // This ensures they won't cause issues when running migrations
        
        // List of all migrations that have been consolidated
        $migrations = [
            // Asset requests related migrations
            '2025_06_01_135433_create_asset_requests_table',
            '2025_06_01_140534_update_asset_requests_table',
            '2025_06_02_025304_fix_asset_requests_table_migration',
            '2025_06_02_030501_fix_asset_requests_table_permanent',
            
            // Item distributions related migrations
            '2025_06_02_014330_add_low_quantity_notified_to_item_distributions',
            '2025_06_02_025422_fix_item_distributions_table_migration',
            '2025_06_02_030945_fix_item_distributions_permanent',
            
            // Quantity fields related migrations
            '2025_06_01_133114_update_inventory_quantity_tracking_to_use_max_quantity',
            '2025_06_01_170300_ensure_max_quantity_exists',
            '2025_06_02_000000_move_quantity_unit_to_inventories_table',
            '2025_06_02_021847_add_missing_quantity_columns_to_inventories_table',
            '2025_06_02_025648_fix_quantity_columns_migration',
            '2025_06_02_031136_fix_quantity_unit_migration'
        ];
        
        // Mark all these migrations as completed
        foreach ($migrations as $migration) {
            DB::table('migrations')->updateOrInsert(
                ['migration' => $migration],
                ['batch' => 1]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't provide a reverse action for this migration
        // as it would just mark the migrations as not run
    }
};
