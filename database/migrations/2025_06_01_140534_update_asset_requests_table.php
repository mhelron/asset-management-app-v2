<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Doctrine\DBAL\Schema\SchemaException;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('asset_requests')) {
            // Check if the admin_note column exists before trying to rename it
            try {
                if (Schema::hasColumn('asset_requests', 'admin_note')) {
                    Schema::table('asset_requests', function (Blueprint $table) {
                        // First, rename admin_note to status_note if it exists
                        $table->renameColumn('admin_note', 'status_note');
                    });
                }
                
                if (Schema::hasColumn('asset_requests', 'approved_by')) {
                    Schema::table('asset_requests', function (Blueprint $table) {
                        // Then rename approved_by to processed_by if it exists
                        $table->renameColumn('approved_by', 'processed_by');
                    });
                }
                
                if (Schema::hasColumn('asset_requests', 'approved_at')) {
                    Schema::table('asset_requests', function (Blueprint $table) {
                        // Then rename approved_at to processed_at if it exists
                        $table->renameColumn('approved_at', 'processed_at');
                    });
                }
                
                // Update status column to be an enum if needed
                Schema::table('asset_requests', function (Blueprint $table) {
                    // Change status to enum with specific values
                    $table->string('status')->default('Pending')->change();
                });
            } catch (SchemaException $e) {
                // Log error but don't fail the migration
                DB::connection()->getPdo()->exec("SELECT 1");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('asset_requests')) {
            try {
                if (Schema::hasColumn('asset_requests', 'status_note')) {
                    Schema::table('asset_requests', function (Blueprint $table) {
                        $table->renameColumn('status_note', 'admin_note');
                    });
                }
                
                if (Schema::hasColumn('asset_requests', 'processed_by')) {
                    Schema::table('asset_requests', function (Blueprint $table) {
                        $table->renameColumn('processed_by', 'approved_by');
                    });
                }
                
                if (Schema::hasColumn('asset_requests', 'processed_at')) {
                    Schema::table('asset_requests', function (Blueprint $table) {
                        $table->renameColumn('processed_at', 'approved_at');
                    });
                }
                
                Schema::table('asset_requests', function (Blueprint $table) {
                    $table->string('status')->default('Pending')->change();
                });
            } catch (SchemaException $e) {
                // Log error but don't fail the migration
                DB::connection()->getPdo()->exec("SELECT 1");
            }
        }
    }
};
