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
        'quantity',
        'max_quantity',
        'min_quantity',
        'low_quantity_notified',
        'serial_no',
        'model_no',
        'manufacturer',
        'date_purchased',
        'purchased_from',
        'image_path',
        'log_note',
        'custom_fields', 
        'status',
        'asset_type_id',
        'location_id'
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

    public function assetType()
    {
        return $this->belongsTo(AssetType::class, 'asset_type_id');
    }
    
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
    
    /**
     * Get the notes for the asset.
     */
    public function notes()
    {
        return $this->hasMany(AssetNote::class, 'inventory_id');
    }
    
    /**
     * Get all distributions of this inventory item.
     */
    public function distributions()
    {
        return $this->hasMany(ItemDistribution::class);
    }
    
    /**
     * Get active distributions (where quantity_remaining > 0).
     */
    public function activeDistributions()
    {
        return $this->hasMany(ItemDistribution::class)
            ->where('quantity_remaining', '>', 0);
    }
    
    /**
     * Get total quantity currently distributed to users.
     */
    public function getDistributedQuantityAttribute()
    {
        return $this->activeDistributions()->sum('quantity_remaining');
    }
    
    /**
     * Get total quantity that has been consumed by users.
     */
    public function getConsumedQuantityAttribute()
    {
        $totalAssigned = $this->distributions()->sum('quantity_assigned');
        $totalRemaining = $this->distributions()->sum('quantity_remaining');
        
        return $totalAssigned - $totalRemaining;
    }
    
    /**
     * Get available quantity that can be distributed.
     */
    public function getAvailableQuantityAttribute()
    {
        // Available = Current Quantity (we only care about what's currently in stock)
        return max(0, $this->quantity);
    }
    
    /**
     * Determine if the asset is requestable based on its asset type
     */
    public function getIsRequestableAttribute()
    {
        return $this->assetType && $this->assetType->is_requestable;
    }
    
    /**
     * Determine if the asset has quantity tracking based on its asset type
     */
    public function getHasQuantityAttribute()
    {
        return $this->assetType && $this->assetType->has_quantity;
    }
    
    /**
     * Check if the asset has low quantity
     */
    public function hasLowQuantity()
    {
        if (!$this->has_quantity || $this->min_quantity === null) {
            return false;
        }
        
        return $this->available_quantity <= $this->min_quantity;
    }
    
    /**
     * Check if the asset is available for distribution
     */
    public function isAvailableForDistribution()
    {
        return $this->has_quantity && $this->available_quantity > 0;
    }
    
    /**
     * Distribute a quantity of this item to a user
     */
    public function distributeToUser($userId, $quantity, $assignedBy, $notes = null)
    {
        if ($quantity <= 0) {
            return false;
        }
        
        if ($quantity > $this->quantity) {
            return false;
        }
        
        $distribution = ItemDistribution::create([
            'inventory_id' => $this->id,
            'user_id' => $userId,
            'quantity_assigned' => $quantity,
            'quantity_remaining' => $quantity,
            'assigned_by' => $assignedBy,
            'notes' => $notes
        ]);

        // Decrease the available quantity after distribution
        $this->quantity -= $quantity;
        $this->save();
        
        return $distribution;
    }
}