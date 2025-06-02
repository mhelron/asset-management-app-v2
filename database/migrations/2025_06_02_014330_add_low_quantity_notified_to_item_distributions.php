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
        Schema::table('item_distributions', function (Blueprint $table) {
            $table->boolean('low_quantity_notified')->default(false)
                  ->after('notes')->comment('Whether the user has been notified about low quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_distributions', function (Blueprint $table) {
            $table->dropColumn('low_quantity_notified');
        });
    }
};
