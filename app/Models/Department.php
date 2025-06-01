<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'desc',
        'location',
        'location_id',
        'status',
    ];

    /**
     * Always eager load the location relationship
     */
    protected $with = ['location'];

    /**
     * Get the users associated with this department
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    /**
     * Get the location associated with this department
     */
    public function location()
    {
        // We're explicitly defining the local and foreign keys to ensure the relationship works
        return $this->belongsTo(
            Location::class,
            'location_id', // Foreign key on departments table
            'id'           // Primary key on locations table
        );
    }
    
    /**
     * Get inventory items assigned to this department
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'department_id');
    }
}
