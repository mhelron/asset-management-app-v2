<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetType;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get all active asset types
        $assetTypes = AssetType::where('status', 'Active')->get();
        
        // Get total count of items
        $totalItems = Inventory::count();
        
        // Get count of unassigned items
        $unassignedItems = Inventory::whereNull('users_id')->count();
        
        // Get total users
        $totalUsers = User::count();
        
        // Get recently added assets
        $recentAssets = Inventory::with(['category', 'assetType'])
            ->latest()
            ->take(5)
            ->get();
            
        // Get asset type statistics with counts
        $assetTypeStats = AssetType::withCount('inventories')
            ->where('status', 'Active')
            ->orderBy('inventories_count', 'desc')
            ->get();
            
        // Get category data for chart - SQLite compatible version
        $categoryData = Category::withCount('inventories')->get()
            ->filter(function ($category) {
                return $category->inventories_count > 0;
            })
            ->sortByDesc('inventories_count')
            ->take(6)
            ->values();
            
        // Get department data for chart - SQLite compatible version
        $departmentData = Department::withCount('inventories')->get()
            ->filter(function ($department) {
                return $department->inventories_count > 0;
            })
            ->sortByDesc('inventories_count')
            ->take(5)
            ->values();
            
        return view('dashboard.index', compact(
            'assetTypes', 
            'totalItems', 
            'unassignedItems', 
            'totalUsers', 
            'recentAssets',
            'assetTypeStats',
            'categoryData',
            'departmentData'
        ));
    }
}
