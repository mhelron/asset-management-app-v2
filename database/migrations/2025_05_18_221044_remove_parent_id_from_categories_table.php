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
        Schema::table('categories', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['parent_id']);
            
            // Then drop the column
            $table->dropColumn('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Re-add the parent_id column
            $table->unsignedBigInteger('parent_id')->nullable()->after('type');
            
            // Re-add the foreign key constraint
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null');
        });
    }
};
