<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any existing NULL type values to 'Asset'
        DB::table('categories')
            ->whereNull('type')
            ->update(['type' => 'Asset']);
        
        // Change the column to have a default value and make it not nullable
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('type', ['Asset', 'Accessory', 'Component', 'Consumable', 'License'])
                ->default('Asset')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('type', ['Asset', 'Accessory', 'Component', 'Consumable', 'License'])
                ->nullable()
                ->change();
        });
    }
};
