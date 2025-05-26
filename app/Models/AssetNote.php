<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetNote extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'inventory_id',
        'user_id',
        'content',
    ];
    
    /**
     * Get the inventory item that the note belongs to.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
    
    /**
     * Get the user that created the note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
