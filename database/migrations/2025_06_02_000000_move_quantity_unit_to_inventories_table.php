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
        // Add quantity_unit to inventories table
        Schema::table('inventories', function (Blueprint $table) {
            $table->string('quantity_unit')->nullable()->after('quantity');
        });

        // Remove quantity_unit from asset_types table
        Schema::table('asset_types', function (Blueprint $table) {
            $table->dropColumn('quantity_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add quantity_unit back to asset_types table
        Schema::table('asset_types', function (Blueprint $table) {
            $table->string('quantity_unit')->nullable()->after('has_quantity');
        });

        // Remove quantity_unit from inventories table
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('quantity_unit');
        });
    }
}; 