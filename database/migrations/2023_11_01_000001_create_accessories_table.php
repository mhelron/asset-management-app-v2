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
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            $table->string('accessory_name');
            $table->string('category');
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('serial_no')->unique();
            $table->string('model_no');
            $table->string('manufacturer');
            $table->foreignId('users_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date_purchased');
            $table->string('purchased_from');
            $table->text('log_note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessories');
    }
}; 