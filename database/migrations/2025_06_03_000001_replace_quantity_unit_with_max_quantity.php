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
        // Mark related migrations as completed
        $migrations = [
            '2025_06_01_133114_update_inventory_quantity_tracking_to_use_max_quantity',
            '2025_06_01_170300_ensure_max_quantity_exists',
            '2025_06_02_000000_move_quantity_unit_to_inventories_table',
            '2025_06_02_021847_add_missing_quantity_columns_to_inventories_table',
            '2025_06_02_025648_fix_quantity_columns_migration',
            '2025_06_02_031136_fix_quantity_unit_migration'
        ];
        
        foreach ($migrations as $migration) {
            DB::table('migrations')->updateOrInsert(
                ['migration' => $migration],
                ['batch' => 1]
            );
        }
        
        // Handle quantity_unit and max_quantity in inventories table
        if (Schema::hasTable('inventories')) {
            // 1. First check for the existence of columns
            $hasQuantityUnit = Schema::hasColumn('inventories', 'quantity_unit');
            $hasMaxQuantity = Schema::hasColumn('inventories', 'max_quantity');
            $hasMinQuantity = Schema::hasColumn('inventories', 'min_quantity');
            $hasQuantity = Schema::hasColumn('inventories', 'quantity');
            $hasLowQuantityNotified = Schema::hasColumn('inventories', 'low_quantity_notified');
            
            // 2. Update the table based on what columns exist
            if (!$hasMaxQuantity) {
                Schema::table('inventories', function (Blueprint $table) {
                    if (Schema::hasColumn('inventories', 'quantity')) {
                        $table->integer('max_quantity')->nullable()->after('quantity');
                    } else {
                        $table->integer('max_quantity')->nullable();
                    }
                });
            }
            
            if (!$hasMinQuantity) {
                Schema::table('inventories', function (Blueprint $table) {
                    $table->integer('min_quantity')->nullable()->after('max_quantity');
                });
            }
            
            if (!$hasQuantity) {
                Schema::table('inventories', function (Blueprint $table) {
                    $table->integer('quantity')->nullable()->after('model_no');
                });
            }
            
            if (!$hasLowQuantityNotified) {
                Schema::table('inventories', function (Blueprint $table) {
                    $table->boolean('low_quantity_notified')->default(false)->after('min_quantity');
                });
            }
            
            // 3. Remove quantity_unit if it still exists
            if ($hasQuantityUnit) {
                Schema::table('inventories', function (Blueprint $table) {
                    $table->dropColumn('quantity_unit');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't provide a rollback for this consolidation
        // as it would be complex and potentially dangerous
    }
}; 