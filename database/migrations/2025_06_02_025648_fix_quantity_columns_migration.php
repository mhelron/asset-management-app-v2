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
        // The column already exists, so we just mark the migration as completed
        $migration = '2025_06_03_000001_replace_quantity_unit_with_max_quantity';
        
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
            ->where('migration', '2025_06_03_000001_replace_quantity_unit_with_max_quantity')
            ->delete();
    }
};
