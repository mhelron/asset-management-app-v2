<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category',
        'desc',
        'status',
        'custom_fields',
        'type'
    ];
    
    // Add a cast to handle the JSON
    protected $casts = [
        'custom_fields' => 'array'
    ];

    /**
     * Get custom fields associated with this category
     */
    public function customFields()
    {
         return $this->hasMany(CustomField::class);
    }
    
    /**
     * Get inventory items in this category
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'category_id');
    }
}
