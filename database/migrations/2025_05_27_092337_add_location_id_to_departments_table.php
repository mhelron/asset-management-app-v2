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
        Schema::table('departments', function (Blueprint $table) {
            // Make the existing location field nullable
            $table->string('location')->nullable()->change();
            
            // Add location_id foreign key
            $table->foreignId('location_id')->nullable()->after('location')->constrained('locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
            $table->string('location')->nullable(false)->change();
        });
    }
};
