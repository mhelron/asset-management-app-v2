<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->foreignId('users_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('serial_no')->unique();
            $table->string('asset_tag')->unique();
            $table->string('model_no');
            $table->string('manufacturer');
            $table->date('date_purchased');
            $table->string('purchased_from');
            $table->string('image_path')->nullable();
            $table->text('log_note')->nullable();
            $table->json('custom_fields')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down() {
        Schema::dropIfExists('inventories');
    }
};
