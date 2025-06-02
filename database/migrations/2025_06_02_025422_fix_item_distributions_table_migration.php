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
        // Mark the problematic migration as completed
        // The table doesn't exist yet, so we just mark the migration as completed
        // and let the table creation migration handle adding this column
        $migration = '2025_06_02_014330_add_low_quantity_notified_to_item_distributions';
        
        // Only insert if not already present
        if (!DB::table('migrations')->where('migration', $migration)->exists()) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => 1
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove migration record
        DB::table('migrations')
            ->where('migration', '2025_06_02_014330_add_low_quantity_notified_to_item_distributions')
            ->delete();
    }
};
