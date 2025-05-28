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
        // Migration not needed as we moved is_requestable to asset_types
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migration not needed as we moved is_requestable to asset_types
    }
};
