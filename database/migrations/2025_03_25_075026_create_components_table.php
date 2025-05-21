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
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->string('component_name');
            $table->string('category');
            $table->string('serial_no')->unique();
            $table->string('model_no');
            $table->string('manufacturer');
            $table->foreignId('users_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('date_purchased');
            $table->string('purchased_from');
            $table->text('log_note')->nullable();
            $table->foreignId('inventory_id')->nullable()->constrained('inventories')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
