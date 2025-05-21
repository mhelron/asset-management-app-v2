<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_name', 
        'category_id',
        'department_id',
        'users_id',
        'asset_tag',
        'serial_no',
        'model_no',
        'manufacturer',
        'date_purchased',
        'purchased_from',
        'image_path',
        'log_note',
        'custom_fields', 
        'status'
    ];

    protected $casts = [
        'custom_fields' => 'array',
    ];

    public function getCustomFieldsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
    
    // Define relationship with components
    public function components()
    {
        return $this->hasMany(Components::class, 'inventory_id');
    }
}