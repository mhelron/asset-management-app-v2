<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\ItemDistribution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers\ActivityLogger;

class ItemDistributionController extends Controller
{
    /**
     * Display a listing of distributions for a specific inventory item.
     */
    public function indexByItem($id)
    {
        try {
            // Redirect to the inventory show page with the distributions tab active
            return redirect()->route('inventory.show', ['id' => $id, 'tab' => 'distributions']);
        } catch (\Exception $e) {
            Log::error('Error showing distributions: ' . $e->getMessage());
            return redirect()->route('inventory.index')
                ->with('error', 'Failed to load distributions.');
        }
    }
    
    /**
     * Display a listing of distributions for the current user.
     */
    public function myItems()
    {
        // Redirect to the user profile page with the My Items tab active
        return redirect()->route('users.my-profile', ['tab' => 'items']);
    }
    
    /**
     * Store a newly created distribution.
     */
    public function store(Request $request, $id)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string',
            ]);
            
            $item = Inventory::findOrFail($id);
            
            // Check if the item has quantity tracking
            if (!$item->has_quantity) {
                return redirect()->back()
                    ->with('error', 'This item does not support quantity tracking.');
            }
            
            // Check if there's enough quantity available
            $availableQuantity = $item->available_quantity;
            if ($request->quantity > $availableQuantity) {
                return redirect()->back()
                    ->with('error', "Cannot distribute {$request->quantity} items. Only {$availableQuantity} available.");
            }
            
            // Create the distribution
            $distribution = $item->distributeToUser(
                $request->user_id,
                $request->quantity,
                Auth::id(),
                $request->notes
            );
            
            if (!$distribution) {
                return redirect()->back()
                    ->with('error', 'Failed to distribute item.');
            }
            
            // Log activity
            ActivityLogger::log(
                "Distributed {$request->quantity} units of item to user",
                $item->item_name
            );
            
            return redirect()->back()
                ->with('success', "Successfully distributed {$request->quantity} units to user.");
        } catch (\Exception $e) {
            Log::error('Error distributing item: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to distribute item. Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Mark a quantity of a distribution as used.
     */
    public function useItems(Request $request, $id)
    {
        try {
            $request->validate([
                'quantity_used' => 'required|integer|min:1',
            ]);
            
            $distribution = ItemDistribution::findOrFail($id);
            
            // Check if the distribution belongs to the current user
            if ($distribution->user_id !== Auth::id()) {
                return redirect()->back()
                    ->with('error', 'You can only mark your own items as used.');
            }
            
            // Check if there's enough quantity remaining
            if ($request->quantity_used > $distribution->quantity_remaining) {
                return redirect()->back()
                    ->with('error', "Cannot mark {$request->quantity_used} items as used. Only {$distribution->quantity_remaining} remaining.");
            }
            
            // Mark the items as used
            $distribution->useQuantity($request->quantity_used);
            
            $item = $distribution->inventory;
            
            // Log activity
            ActivityLogger::log(
                "Marked {$request->quantity_used} units of item as used",
                $item->item_name
            );
            
            return redirect()->back()
                ->with('success', "Successfully marked {$request->quantity_used} units as consumed. These items have been permanently removed from inventory.");
        } catch (\Exception $e) {
            Log::error('Error marking items as used: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to mark items as used. Error: ' . $e->getMessage());
        }
    }
} 