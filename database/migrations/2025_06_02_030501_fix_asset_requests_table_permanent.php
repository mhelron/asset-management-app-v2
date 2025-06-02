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
        // 1. Create the asset_requests table if it doesn't exist
        if (!Schema::hasTable('asset_requests')) {
            Schema::create('asset_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventory_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->text('reason');
                $table->date('date_needed');
                $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Completed', 'Cancelled'])->default('Pending');
                $table->text('status_note')->nullable();
                $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
            });
        }

        // 2. Mark all the asset_requests migrations as completed to prevent future migration errors
        $migrations = [
            '2025_05_28_083840_create_asset_requests_table',
            '2025_06_01_135433_create_asset_requests_table',
            '2025_06_01_140534_update_asset_requests_table'
        ];
        
        foreach ($migrations as $migration) {
            if (!DB::table('migrations')->where('migration', $migration)->exists()) {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => 1
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop the table on rollback as it might cause data loss
        // Just remove the migration records
        $migrations = [
            '2025_05_28_083840_create_asset_requests_table',
            '2025_06_01_135433_create_asset_requests_table',
            '2025_06_01_140534_update_asset_requests_table'
        ];
        
        DB::table('migrations')->whereIn('migration', $migrations)->delete();
    }
};
