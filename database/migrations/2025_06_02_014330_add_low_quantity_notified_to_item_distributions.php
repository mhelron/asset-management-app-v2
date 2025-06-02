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
        // Only try to add the column if the table exists and the column doesn't
        if (Schema::hasTable('item_distributions') && !Schema::hasColumn('item_distributions', 'low_quantity_notified')) {
            Schema::table('item_distributions', function (Blueprint $table) {
                // Check if the notes column exists to place after
                if (Schema::hasColumn('item_distributions', 'notes')) {
                    $table->boolean('low_quantity_notified')->default(false)
                          ->after('notes')->comment('Whether the user has been notified about low quantity');
                } else {
                    // If notes column doesn't exist, just add the column without after()
                    $table->boolean('low_quantity_notified')->default(false)
                          ->comment('Whether the user has been notified about low quantity');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only try to drop the column if the table and column exist
        if (Schema::hasTable('item_distributions') && Schema::hasColumn('item_distributions', 'low_quantity_notified')) {
            Schema::table('item_distributions', function (Blueprint $table) {
                $table->dropColumn('low_quantity_notified');
            });
        }
    }
};
