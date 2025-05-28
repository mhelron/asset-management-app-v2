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
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
