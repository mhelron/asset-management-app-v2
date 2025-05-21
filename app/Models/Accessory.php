<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Accessory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'accessory_name',
        'category_id',
        'department_id',
        'serial_no',
        'model_no',
        'manufacturer',
        'users_id',
        'date_purchased',
        'purchased_from',
        'log_note',
    ];

    protected $dates = [
        'date_purchased',
        'deleted_at',
    ];

    protected $casts = [
        'date_purchased' => 'date'
    ];

    // Relationship with User (assigned to)
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    // Relationship with Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
