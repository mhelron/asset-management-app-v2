<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomField extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 
        'type',
        'desc', 
        'text_type', 
        'custom_regex', // Add this line
        'is_required', 
        'options',
        'applies_to', 
    ];  
    
    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
        'applies_to' => 'array',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_custom_field');
    }
}
