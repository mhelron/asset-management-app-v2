<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('text');
            $table->text('desc');
            $table->string('text_type')->nullable(); // Store text input type (text, email, number, etc.)
            $table->string('custom_regex')->nullable(); // Add this line
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable(); // Store options for select, checkbox, radio
            $table->json('applies_to')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
