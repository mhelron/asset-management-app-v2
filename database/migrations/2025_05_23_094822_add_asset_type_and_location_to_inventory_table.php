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
            $table->foreignId('asset_type_id')->nullable()->after('category_id')->constrained('asset_types');
            $table->foreignId('location_id')->nullable()->after('department_id')->constrained('locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['asset_type_id']);
            $table->dropForeign(['location_id']);
            $table->dropColumn(['asset_type_id', 'location_id']);
        });
    }
};
