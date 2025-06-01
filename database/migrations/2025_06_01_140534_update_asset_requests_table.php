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
        Schema::table('asset_requests', function (Blueprint $table) {
            // First, rename admin_note to status_note
            $table->renameColumn('admin_note', 'status_note');
            
            // Then rename approved_by to processed_by
            $table->renameColumn('approved_by', 'processed_by');
            
            // Then rename approved_at to processed_at
            $table->renameColumn('approved_at', 'processed_at');
            
            // Change status to enum with specific values
            $table->string('status')->default('Pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_requests', function (Blueprint $table) {
            // Revert changes
            $table->renameColumn('status_note', 'admin_note');
            $table->renameColumn('processed_by', 'approved_by');
            $table->renameColumn('processed_at', 'approved_at');
            $table->string('status')->default('Pending')->change();
        });
    }
};
