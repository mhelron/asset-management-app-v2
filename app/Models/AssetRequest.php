<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'user_id',
        'reason',
        'date_needed',
        'status',
        'admin_note',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'date_needed' => 'date',
        'approved_at' => 'datetime'
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
