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
        Schema::table('components', function (Blueprint $table) {
            // First, create the new category_id column
            $table->unsignedBigInteger('category_id')->nullable()->after('component_name');
            
            // Add foreign key constraint
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null');
        });
        
        // Transfer data: Find categories by name and update category_id
        // Note: This is a simple approach. You may need a more sophisticated approach
        // depending on your data
        $components = DB::table('components')->get();
        foreach ($components as $component) {
            if (!empty($component->category)) {
                $category = DB::table('categories')
                    ->where('category', $component->category)
                    ->where('type', 'Component')
                    ->first();
                
                if ($category) {
                    DB::table('components')
                        ->where('id', $component->id)
                        ->update(['category_id' => $category->id]);
                }
            }
        }
        
        Schema::table('components', function (Blueprint $table) {
            // Finally, drop the old category column
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('components', function (Blueprint $table) {
            // First, add back the category column
            $table->string('category')->nullable()->after('component_name');
        });
        
        // Transfer data back
        $components = DB::table('components')->get();
        foreach ($components as $component) {
            if (!empty($component->category_id)) {
                $category = DB::table('categories')->find($component->category_id);
                if ($category) {
                    DB::table('components')
                        ->where('id', $component->id)
                        ->update(['category' => $category->category]);
                }
            }
        }
        
        Schema::table('components', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['category_id']);
            
            // Then drop the column
            $table->dropColumn('category_id');
        });
    }
};
