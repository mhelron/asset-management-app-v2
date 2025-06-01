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
            // Drop quantity_unit column
            $table->dropColumn('quantity_unit');
            
            // Add max_quantity column
            $table->integer('max_quantity')->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            // Remove max_quantity column
            $table->dropColumn('max_quantity');
            
            // Add quantity_unit column back
            $table->string('quantity_unit')->nullable()->after('quantity');
        });
    }
}; 