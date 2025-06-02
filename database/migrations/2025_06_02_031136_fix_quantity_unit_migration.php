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
        DB::table('migrations')->updateOrInsert(
            ['migration' => '2025_06_03_000001_replace_quantity_unit_with_max_quantity'],
            ['batch' => 1]
        );
        
        // Check if quantity_unit column exists and max_quantity doesn't
        if (Schema::hasTable('inventories')) {
            if (Schema::hasColumn('inventories', 'quantity_unit') && 
                !Schema::hasColumn('inventories', 'max_quantity')) {
                
                Schema::table('inventories', function (Blueprint $table) {
                    // Add max_quantity column
                    $table->integer('max_quantity')->nullable()->after('quantity');
                    
                    // Drop quantity_unit column
                    $table->dropColumn('quantity_unit');
                });
            } 
            // If quantity_unit doesn't exist but max_quantity also doesn't exist
            else if (!Schema::hasColumn('inventories', 'quantity_unit') && 
                     !Schema::hasColumn('inventories', 'max_quantity')) {
                
                Schema::table('inventories', function (Blueprint $table) {
                    // Just add max_quantity column
                    $table->integer('max_quantity')->nullable()->after('quantity');
                });
            }
            // If both columns exist, we need to drop quantity_unit
            else if (Schema::hasColumn('inventories', 'quantity_unit') && 
                     Schema::hasColumn('inventories', 'max_quantity')) {
                
                Schema::table('inventories', function (Blueprint $table) {
                    // Drop quantity_unit column
                    $table->dropColumn('quantity_unit');
                });
            }
            // If only max_quantity exists, we're already good
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to do here, we can't reliably reverse these operations
    }
};
