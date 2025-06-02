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
        // The issue is that the migration to add a column runs before the table is created
        // Let's update the batch numbers in the migrations table to fix the order
        
        // First, mark all problematic migrations as completed
        $migrations = [
            '2025_06_02_014330_add_low_quantity_notified_to_item_distributions',
            '2025_06_03_000000_create_item_distributions_table'
        ];
        
        // Delete the problematic migrations if they exist (we'll reinsert them in the correct order)
        DB::table('migrations')->whereIn('migration', $migrations)->delete();
        
        // Add the create table migration first, with a lower batch number
        DB::table('migrations')->insert([
            'migration' => '2025_06_03_000000_create_item_distributions_table',
            'batch' => 1
        ]);
        
        // Then add the column migration with a higher batch number
        DB::table('migrations')->insert([
            'migration' => '2025_06_02_014330_add_low_quantity_notified_to_item_distributions',
            'batch' => 2
        ]);
        
        // Now, let's create the table if it doesn't exist
        if (!Schema::hasTable('item_distributions')) {
            Schema::create('item_distributions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventory_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
                $table->integer('quantity_assigned');
                $table->integer('quantity_remaining');
                $table->timestamp('assigned_at');
                $table->timestamp('last_used_at')->nullable();
                $table->boolean('low_quantity_notified')->default(false);
                $table->timestamps();
            });
        } else if (!Schema::hasColumn('item_distributions', 'low_quantity_notified')) {
            // If the table exists but doesn't have the column, add it
            Schema::table('item_distributions', function (Blueprint $table) {
                $table->boolean('low_quantity_notified')->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the migrations from the table
        $migrations = [
            '2025_06_02_014330_add_low_quantity_notified_to_item_distributions',
            '2025_06_03_000000_create_item_distributions_table'
        ];
        
        DB::table('migrations')->whereIn('migration', $migrations)->delete();
    }
};
