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
            $table->integer('quantity')->nullable()->after('asset_tag');
            $table->integer('min_quantity')->nullable()->after('quantity');
            $table->boolean('low_quantity_notified')->default(false)->after('min_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('quantity');
            $table->dropColumn('min_quantity');
            $table->dropColumn('low_quantity_notified');
        });
    }
}; 