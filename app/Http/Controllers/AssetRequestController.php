<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\AssetRequest;
use App\Models\Inventory;
use App\Models\User;
use App\Notifications\ItemRequestNotification;
use App\Helpers\ActivityLogger;

class AssetRequestController extends Controller
{
    /**
     * Display a listing of asset requests.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assetRequests = AssetRequest::with(['inventory', 'user'])
            ->latest()
            ->paginate(10);
            
        return view('asset-requests.index', compact('assetRequests'));
    }
    
    /**
     * Show the form for creating a new asset request.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $requestableAssets = Inventory::whereHas('assetType', function($query) {
            $query->where('is_requestable', true);
        })->get();
        
        return view('asset-requests.create', compact('requestableAssets'));
    }
    
    /**
     * Store a newly created asset request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'reason' => 'required|string',
            'date_needed' => 'required|date|after:today',
        ]);
        
        $assetRequest = AssetRequest::create([
            'inventory_id' => $request->inventory_id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'date_needed' => $request->date_needed,
            'status' => 'Pending',
        ]);
        
        // Debug logging
        Log::info('Asset request created', ['request_id' => $assetRequest->id]);
        
        // Send notifications to admins and managers
        // Using lowercase role names to match what might be in the database
        $adminUsers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Admin', 'admin', 'Manager', 'manager']);
        })->get();
        
        // Debug logging
        Log::info('Found admin users', [
            'count' => $adminUsers->count(),
            'emails' => $adminUsers->pluck('email')->toArray()
        ]);
        
        foreach ($adminUsers as $admin) {
            try {
                Log::info('Sending notification to user', [
                    'admin_id' => $admin->id,
                    'admin_email' => $admin->email,
                    'admin_role' => $admin->user_role,
                    'admin_roles' => $admin->getRoleNames()->toArray()
                ]);
                
                $admin->notify(new ItemRequestNotification($assetRequest));
                
                Log::info('Notification sent successfully');
            } catch (\Exception $e) {
                Log::error('Failed to send notification', [
                    'admin_id' => $admin->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        // Log activity
        ActivityLogger::log('Requested asset', $assetRequest->inventory->item_name);
        
        return redirect()->route('asset-requests.my-requests')
            ->with('success', 'Asset request submitted successfully.');
    }
    
    /**
     * Display the specified asset request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $assetRequest = AssetRequest::with(['inventory', 'user'])->findOrFail($id);
        
        return view('asset-requests.show', compact('assetRequest'));
    }
    
    /**
     * Update the status of an asset request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected,Completed,Cancelled',
            'status_note' => 'nullable|string',
        ]);
        
        $assetRequest = AssetRequest::findOrFail($id);
        
        $assetRequest->update([
            'status' => $request->status,
            'status_note' => $request->status_note,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);
        
        // If approved and item has quantity tracking, we may want to handle that here
        if ($request->status == 'Approved' && $assetRequest->inventory->has_quantity) {
            // Logic to reserve or distribute the item could be added here
        }
        
        // Log activity
        ActivityLogger::log('Updated asset request status to ' . $request->status, $assetRequest->inventory->item_name);
        
        return redirect()->route('asset-requests.show', $id)
            ->with('success', 'Asset request status updated successfully.');
    }
    
    /**
     * My requests - show requests for the current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function myRequests()
    {
        $assetRequests = AssetRequest::with(['inventory'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
            
        return view('asset-requests.my-requests', compact('assetRequests'));
    }
} 