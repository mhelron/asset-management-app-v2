<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'desc',
        'status',
        'requires_qr_code',
        'is_requestable',
        'has_quantity',
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
    
    /**
     * Get all inventory items with low quantity for this asset type
     */
    public function getLowQuantityItems()
    {
        if (!$this->has_quantity) {
            return collect();
        }
        
        return $this->inventories()
            ->whereNotNull('quantity')
            ->whereNotNull('min_quantity')
            ->whereRaw('quantity <= min_quantity')
            ->where('low_quantity_notified', false)
            ->get();
    }
}
