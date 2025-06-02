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
        // Insert a record in the migrations table to mark the create_asset_requests_table migration as completed
        // This fixes the issue where the asset_requests table already exists but the migration is still pending
        $migration = '2025_06_01_135433_create_asset_requests_table';
        
        // Only insert if not already present
        if (!DB::table('migrations')->where('migration', $migration)->exists()) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => 1
            ]);
        }
        
        // Also mark the update_asset_requests_table migration as completed
        $updateMigration = '2025_06_01_140534_update_asset_requests_table';
        
        // Only insert if not already present
        if (!DB::table('migrations')->where('migration', $updateMigration)->exists()) {
            DB::table('migrations')->insert([
                'migration' => $updateMigration,
                'batch' => 1
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove these migrations from the migrations table
        DB::table('migrations')
            ->whereIn('migration', [
                '2025_06_01_135433_create_asset_requests_table',
                '2025_06_01_140534_update_asset_requests_table'
            ])
            ->delete();
    }
};
