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
        // Mark other related migrations as completed
        $migrations = [
            '2025_06_02_014330_add_low_quantity_notified_to_item_distributions',
            '2025_06_02_025422_fix_item_distributions_table_migration',
            '2025_06_02_030945_fix_item_distributions_permanent'
        ];
        
        foreach ($migrations as $migration) {
            DB::table('migrations')->updateOrInsert(
                ['migration' => $migration],
                ['batch' => 1]
            );
        }
        
        // Create the complete table if it doesn't exist
        if (!Schema::hasTable('item_distributions')) {
            Schema::create('item_distributions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventory_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->integer('quantity_assigned');
                $table->integer('quantity_remaining');
                $table->foreignId('assigned_by')->constrained('users');
                $table->text('notes')->nullable();
                // Include low_quantity_notified to avoid the need for another migration
                $table->boolean('low_quantity_notified')->default(false)
                      ->comment('Whether the user has been notified about low quantity');
                $table->timestamp('assigned_at')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();
            });
        } else if (!Schema::hasColumn('item_distributions', 'low_quantity_notified')) {
            // If the table exists but doesn't have the low_quantity_notified column, add it
            Schema::table('item_distributions', function (Blueprint $table) {
                $table->boolean('low_quantity_notified')->default(false)
                      ->comment('Whether the user has been notified about low quantity');
            });
        }
        
        // Make sure assigned_at and last_used_at exist
        if (Schema::hasTable('item_distributions')) {
            if (!Schema::hasColumn('item_distributions', 'assigned_at')) {
                Schema::table('item_distributions', function (Blueprint $table) {
                    $table->timestamp('assigned_at')->nullable();
                });
            }
            
            if (!Schema::hasColumn('item_distributions', 'last_used_at')) {
                Schema::table('item_distributions', function (Blueprint $table) {
                    $table->timestamp('last_used_at')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_distributions');
    }
}; 