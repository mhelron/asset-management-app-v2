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
        // Get all inventory items where quantity is set but max_quantity is null or 0
        $items = DB::table('inventories')
            ->whereNotNull('quantity')
            ->where(function($query) {
                $query->whereNull('max_quantity')
                    ->orWhere('max_quantity', 0);
            })
            ->get();
            
        // Update each item to set max_quantity equal to quantity
        foreach ($items as $item) {
            DB::table('inventories')
                ->where('id', $item->id)
                ->update(['max_quantity' => $item->quantity]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this as it's a data correction
    }
};
