<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            // Add the missing quantity columns if they don't exist
            if (!Schema::hasColumn('inventories', 'max_quantity')) {
                $table->integer('max_quantity')->nullable();
            }
            
            if (!Schema::hasColumn('inventories', 'min_quantity')) {
                $table->integer('min_quantity')->nullable();
            }
            
            if (!Schema::hasColumn('inventories', 'quantity')) {
                $table->integer('quantity')->nullable();
            }
            
            if (!Schema::hasColumn('inventories', 'low_quantity_notified')) {
                $table->boolean('low_quantity_notified')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            // Drop the quantity columns
            $table->dropColumn('max_quantity');
            $table->dropColumn('min_quantity');
            $table->dropColumn('quantity');
            $table->dropColumn('low_quantity_notified');
        });
    }
};
