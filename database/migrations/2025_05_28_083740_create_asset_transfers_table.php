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
        Schema::create('asset_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories');
            $table->foreignId('from_user_id')->nullable()->constrained('users');
            $table->foreignId('from_department_id')->nullable()->constrained('departments');
            $table->foreignId('from_location_id')->nullable()->constrained('locations');
            $table->foreignId('to_user_id')->nullable()->constrained('users');
            $table->foreignId('to_department_id')->nullable()->constrained('departments');
            $table->foreignId('to_location_id')->nullable()->constrained('locations');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->string('status')->default('Completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_transfers');
    }
};
