<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'user_id',
        'quantity_assigned',
        'quantity_remaining',
        'assigned_by',
        'notes',
    ];

    /**
     * Get the inventory item associated with this distribution.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Get the user who received this distribution.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who assigned this distribution.
     */
    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
    
    /**
     * Check if the distribution is fully used.
     */
    public function isFullyUsed()
    {
        return $this->quantity_remaining <= 0;
    }
    
    /**
     * Check if the distribution is partially used.
     */
    public function isPartiallyUsed()
    {
        return $this->quantity_remaining < $this->quantity_assigned && $this->quantity_remaining > 0;
    }
    
    /**
     * Use a specific quantity from this distribution.
     */
    public function useQuantity($amount)
    {
        if ($amount <= 0) {
            return false;
        }
        
        if ($amount > $this->quantity_remaining) {
            return false;
        }
        
        $this->quantity_remaining -= $amount;
        $this->save();
        
        return true;
    }
} 