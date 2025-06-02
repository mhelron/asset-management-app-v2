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
        // Mark the other redundant migrations as completed
        $migrations = [
            '2025_06_01_135433_create_asset_requests_table',
            '2025_06_01_140534_update_asset_requests_table',
            '2025_06_02_025304_fix_asset_requests_table_migration',
            '2025_06_02_030501_fix_asset_requests_table_permanent'
        ];
        
        foreach ($migrations as $migration) {
            DB::table('migrations')->updateOrInsert(
                ['migration' => $migration],
                ['batch' => 1]
            );
        }

        // Create the asset_requests table with the final structure if it doesn't exist
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
        } else {
            // If the table exists but has old structure, update it
            if (Schema::hasColumn('asset_requests', 'admin_note') && 
                !Schema::hasColumn('asset_requests', 'status_note')) {
                Schema::table('asset_requests', function (Blueprint $table) {
                    $table->renameColumn('admin_note', 'status_note');
                });
            }
            
            if (Schema::hasColumn('asset_requests', 'approved_by') && 
                !Schema::hasColumn('asset_requests', 'processed_by')) {
                Schema::table('asset_requests', function (Blueprint $table) {
                    $table->renameColumn('approved_by', 'processed_by');
                });
            }
            
            if (Schema::hasColumn('asset_requests', 'approved_at') && 
                !Schema::hasColumn('asset_requests', 'processed_at')) {
                Schema::table('asset_requests', function (Blueprint $table) {
                    $table->renameColumn('approved_at', 'processed_at');
                });
            }
            
            // Make sure status column has the right type
            if (Schema::hasColumn('asset_requests', 'status')) {
                try {
                    Schema::table('asset_requests', function (Blueprint $table) {
                        $table->string('status')->default('Pending')->change();
                    });
                } catch (\Exception $e) {
                    // If change fails, it's likely already in the right format
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_requests');
    }
};
