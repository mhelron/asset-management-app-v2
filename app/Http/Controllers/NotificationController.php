<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Inventory;
use App\Models\AssetRequest;
use App\Notifications\LowQuantityNotification;
use App\Notifications\ItemRequestNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(10);
        
        return view('notifications.index', compact('notifications'));
    }
    
    /**
     * Mark a notification as read.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        // Check if this is an AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Notification marked as read']);
        }
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }
    
    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        // Check if this is an AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
        }
        
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
    
    /**
     * Check for low inventory and send notifications to admins and managers.
     * This can be called via scheduler or manually.
     *
     * @return void
     */
    public function checkLowInventory()
    {
        // Get all inventory items with quantity less than or equal to min_quantity
        // and that have not been notified about yet
        $lowInventoryItems = Inventory::whereRaw('quantity <= min_quantity')
            ->where('low_quantity_notified', false)
            ->where('min_quantity', '>', 0)
            ->get();
        
        if ($lowInventoryItems->count() > 0) {
            // Get all admin and manager users
            $adminUsers = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Admin', 'admin', 'Manager', 'manager']);
            })->get();
            
            foreach ($lowInventoryItems as $item) {
                // Send notification to each admin user
                foreach ($adminUsers as $admin) {
                    $admin->notify(new LowQuantityNotification($item));
                }
                
                // Mark this item as notified
                $item->update(['low_quantity_notified' => true]);
            }
        }
        
        return response()->json(['message' => $lowInventoryItems->count() . ' low inventory notifications sent.']);
    }
    
    /**
     * Process a new asset request and send notifications.
     *
     * @param  AssetRequest  $assetRequest
     * @return void
     */
    public function processAssetRequest(AssetRequest $assetRequest)
    {
        // Get all admin and manager users
        $adminUsers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Admin', 'admin', 'Manager', 'manager']);
        })->get();
        
        // Send notification to each admin user
        foreach ($adminUsers as $admin) {
            $admin->notify(new ItemRequestNotification($assetRequest));
        }
        
        return response()->json(['message' => 'Asset request notifications sent.']);
    }
    
    /**
     * Get latest notifications for the authenticated user.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestNotifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications->take(5);
        $unreadCount = $user->unreadNotifications->count();
        
        $formattedNotifications = $notifications->map(function($notification) {
            $route = isset($notification->data['request_id']) 
                ? route('asset-requests.show', $notification->data['request_id']) 
                : (isset($notification->data['inventory_id']) 
                    ? route('inventory.show', $notification->data['inventory_id']) 
                    : route('notifications.index'));
                    
            $iconClass = 'bi-bell-fill';
            $bgClass = 'bg-primary';
            
            if(isset($notification->data['message'])) {
                if(str_contains($notification->data['message'], 'Low quantity')) {
                    $iconClass = 'bi-exclamation-triangle-fill';
                    $bgClass = 'bg-warning';
                } elseif(str_contains($notification->data['message'], 'request')) {
                    $iconClass = 'bi-box-seam-fill';
                    $bgClass = 'bg-info';
                }
            }
            
            return [
                'id' => $notification->id,
                'message' => $notification->data['message'] ?? 'New notification',
                'time' => $notification->created_at->diffForHumans(),
                'read' => !is_null($notification->read_at),
                'route' => $route,
                'iconClass' => $iconClass,
                'bgClass' => $bgClass
            ];
        });
        
        return response()->json([
            'notifications' => $formattedNotifications,
            'unreadCount' => $unreadCount
        ]);
    }
} 