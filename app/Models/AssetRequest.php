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
        'status_note',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'date_needed' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the inventory item associated with this request.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Get the user who made this request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who processed this request.
     */
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    /**
     * Scope a query to only include rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    /**
     * Scope a query to only include completed requests.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }
}
