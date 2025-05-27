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
            if (!Schema::hasColumn('inventories', 'asset_type_id')) {
                $table->unsignedBigInteger('asset_type_id')->nullable()->after('status');
                $table->foreign('asset_type_id')->references('id')->on('asset_types')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            if (Schema::hasColumn('inventories', 'asset_type_id')) {
                $table->dropForeign(['asset_type_id']);
                $table->dropColumn('asset_type_id');
            }
        });
    }
}; 