<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
