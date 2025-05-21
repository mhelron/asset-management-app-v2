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
        Schema::table('accessories', function (Blueprint $table) {
            // First, create the new category_id column
            $table->unsignedBigInteger('category_id')->nullable()->after('accessory_name');
            
            // Add foreign key constraint
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null');
        });
        
        // Transfer data: Find categories by name and update category_id
        $accessories = DB::table('accessories')->get();
        foreach ($accessories as $accessory) {
            if (!empty($accessory->category)) {
                $category = DB::table('categories')
                    ->where('category', $accessory->category)
                    ->where('type', 'Accessory')
                    ->first();
                
                if ($category) {
                    DB::table('accessories')
                        ->where('id', $accessory->id)
                        ->update(['category_id' => $category->id]);
                }
            }
        }
        
        Schema::table('accessories', function (Blueprint $table) {
            // Finally, drop the old category column
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accessories', function (Blueprint $table) {
            // First, add back the category column
            $table->string('category')->nullable()->after('accessory_name');
        });
        
        // Transfer data back
        $accessories = DB::table('accessories')->get();
        foreach ($accessories as $accessory) {
            if (!empty($accessory->category_id)) {
                $category = DB::table('categories')->find($accessory->category_id);
                if ($category) {
                    DB::table('accessories')
                        ->where('id', $accessory->id)
                        ->update(['category' => $category->category]);
                }
            }
        }
        
        Schema::table('accessories', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['category_id']);
            
            // Then drop the column
            $table->dropColumn('category_id');
        });
    }
};
