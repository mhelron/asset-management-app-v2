<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CustomFieldsController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AssetTypeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ItemDistributionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AssetRequestController;
use App\Http\Controllers\ExcelController;
use Illuminate\Support\Facades\Auth;


//Logout Route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


//Login Route (Hiwalay na middleware para di na pwede mag login ulit pag authenticated na)
Route::middleware(['guest'])->group(function () {
        Route::get('/', [LoginController::class, 'showLogin'])->name('login.form');   
        // Show login form
        Route::get('/login', [LoginController::class, 'showLogin'])->name('login.form');
        // Handle login form submission
        Route::post('/login', [LoginController::class, 'processLogin'])->name('login');
});


Route::middleware(['auth' , 'role.permission'])->group(function () {
    
    // Dasbord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');   

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'showLogs'])->name('logs.activity');

    // User Routes
    Route::prefix('/users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/get-users-data', [UserController::class, 'getUsersData'])->name('users.data');
        Route::get('profile/{id}', [UserController::class, 'view'])->name('users.view');
        Route::get('create-user', [UserController::class, 'create'])->name('users.create');
        Route::post('create-user', [UserController::class, 'store'])->name('users.store');
        Route::get('edit-user/{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::put('update-user/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/archive-user/{id}', [UserController::class, 'archive'])->name('users.archive');
        
        // My Profile routes
        Route::get('/my-profile', [UserController::class, 'myProfile'])->name('users.my-profile');
        Route::get('/edit-profile', [UserController::class, 'editProfile'])->name('users.edit-profile');
        Route::post('/update-my-profile', [UserController::class, 'updateMyProfile'])->name('users.update-my-profile');
    });

    // Categories Route
    Route::prefix('/categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('add-category', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('add-category', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('edit-category/{id}', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('update-category/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('archive-category/{id}', [CategoryController::class, 'archive'])->name('categories.archive');
        Route::get('/get-custom-fields/{id}', [CategoryController::class, 'getCustomFields']);
        Route::get('by-type/{type}', [CategoryController::class, 'getCategoriesByType']);
    });

    // Inventory Route
    Route::prefix('/inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/data', [InventoryController::class, 'getInventoryData'])->name('inventory.data');
        Route::get('add-inventory', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('add-inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('edit-inventory/{id}', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('update-inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('archive-item/{id}', [InventoryController::class, 'archive'])->name('inventory.archive');
        Route::get('show-inventory/{id}', [InventoryController::class, 'show'])->name('inventory.show');
        Route::get('get-category-fields/{id}', [InventoryController::class, 'getCategoryFields'])->name('inventory.category.fields');
        Route::get('get-category-fields/refresh', [InventoryController::class, 'getRefreshedCategories'])->name('inventory.refresh.categories');
        Route::get('get-asset-types/refresh', [InventoryController::class, 'getRefreshedAssetTypes'])->name('inventory.refresh.asset-types');
        Route::get('get-item-details/{id}', [InventoryController::class, 'getItemDetails']);
        Route::get('get-custom-fields/{id}', [InventoryController::class, 'getCustomFields']);
        Route::post('add-note/{id}', [InventoryController::class, 'addNote'])->name('inventory.add-note');
        Route::put('update-note/{inventory}/{note}', [InventoryController::class, 'updateNote'])->name('inventory.update-note');
        Route::delete('delete-note/{inventory}/{note}', [InventoryController::class, 'deleteNote'])->name('inventory.delete-note');
        Route::get('test-qr/{id}', [InventoryController::class, 'testQRCode'])->name('inventory.test-qr');
        Route::post('transfer/{id}', [InventoryController::class, 'transferAsset'])->name('inventory.transfer');
        Route::post('request/{id}', [InventoryController::class, 'requestAsset'])->name('inventory.request');
        Route::post('add-stock/{id}', [InventoryController::class, 'addStock'])->name('inventory.add-stock');
    });

    // Custom Fields Route
    Route::prefix('/custom-fields')->group(function () {
        Route::get('/', [CustomFieldsController::class, 'index'])->name('customfields.index');
        Route::get('add-custom-field', [CustomFieldsController::class, 'create'])->name('customfields.create');
        Route::post('add-custom-field', [CustomFieldsController::class, 'store'])->name('customfields.store');
        Route::get('view-custom-field/{id}', [CustomFieldsController::class, 'show'])->name('customfields.show');
        Route::get('edit-custom-field/{id}', [CustomFieldsController::class, 'edit'])->name('customfields.edit');
        Route::put('update-custom-field/{id}', [CustomFieldsController::class, 'update'])->name('customfields.update');
        Route::delete('archive-custom-field/{id}', [CustomFieldsController::class, 'archive'])->name('customfields.archive');
    });

    // Department Routes
    Route::prefix('/departments')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('create-department', [DepartmentController::class, 'create'])->name('departments.create');
        Route::post('create-department', [DepartmentController::class, 'store'])->name('departments.store');
        Route::get('edit-department/{id}', [DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('update-department/{id}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/archive-department/{id}', [DepartmentController::class, 'archive'])->name('departments.archive');
        Route::get('get-location/{id}', [DepartmentController::class, 'getLocation'])->name('departments.get-location');
    });

    // Asset Type Routes
    Route::prefix('/asset-types')->group(function () {
        Route::get('/', [AssetTypeController::class, 'index'])->name('asset-types.index');
        Route::get('create-asset-type', [AssetTypeController::class, 'create'])->name('asset-types.create');
        Route::post('create-asset-type', [AssetTypeController::class, 'store'])->name('asset-types.store');
        Route::get('edit-asset-type/{id}', [AssetTypeController::class, 'edit'])->name('asset-types.edit');
        Route::put('update-asset-type/{id}', [AssetTypeController::class, 'update'])->name('asset-types.update');
        Route::delete('/archive-asset-type/{id}', [AssetTypeController::class, 'archive'])->name('asset-types.archive');
    });

    // Location Routes
    Route::prefix('/locations')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('locations.index');
        Route::get('create-location', [LocationController::class, 'create'])->name('locations.create');
        Route::post('create-location', [LocationController::class, 'store'])->name('locations.store');
        Route::get('edit-location/{id}', [LocationController::class, 'edit'])->name('locations.edit');
        Route::put('update-location/{id}', [LocationController::class, 'update'])->name('locations.update');
        Route::delete('/archive-location/{id}', [LocationController::class, 'archive'])->name('locations.archive');
    });

    // Item Distributions
    Route::group(['prefix' => 'distributions', 'middleware' => ['auth']], function () {
        Route::get('/my-items', [ItemDistributionController::class, 'myItems'])->name('distributions.my-items');
        Route::get('/by-item/{id}', [ItemDistributionController::class, 'indexByItem'])->name('distributions.by-item');
        Route::post('/store/{id}', [ItemDistributionController::class, 'store'])->name('distributions.store');
        Route::post('/use-items/{id}', [ItemDistributionController::class, 'useItems'])->name('distributions.use-items');
    });

    // Notifications
    Route::prefix('/notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/mark-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('/check-low-inventory', [NotificationController::class, 'checkLowInventory'])->name('notifications.check-low-inventory');
        Route::get('/get-latest', [NotificationController::class, 'getLatestNotifications'])->name('notifications.get-latest');
    });

    // Asset Requests
    Route::prefix('/asset-requests')->group(function () {
        Route::get('/', [AssetRequestController::class, 'index'])->name('asset-requests.index');
        Route::get('/create', [AssetRequestController::class, 'create'])->name('asset-requests.create');
        Route::post('/store', [AssetRequestController::class, 'store'])->name('asset-requests.store');
        Route::get('/show/{id}', [AssetRequestController::class, 'show'])->name('asset-requests.show');
        Route::post('/update-status/{id}', [AssetRequestController::class, 'updateStatus'])->name('asset-requests.update-status');
        Route::get('/my-requests', [AssetRequestController::class, 'myRequests'])->name('asset-requests.my-requests');
    });

    // Excel Import/Export
    Route::prefix('/excel')->group(function () {
        Route::post('/generate-template', [ExcelController::class, 'generateTemplate'])->name('excel.generate-template');
        Route::post('/import', [ExcelController::class, 'import'])->name('excel.import');
        Route::post('/export', [ExcelController::class, 'export'])->name('excel.export');
    });

    // Test route for notifications (only in non-production)
    if (app()->environment() !== 'production') {
        Route::get('/test-notification', function() {
            $user = Auth::user();
            // Create a notification with unique message to ensure it's new
            $timestamp = now()->format('H:i:s');
            
            // Find first inventory item
            $inventory = \App\Models\Inventory::first();
            if ($inventory) {
                $user->notify(new \App\Notifications\LowQuantityNotification($inventory));
                return response()->json([
                    'success' => true, 
                    'message' => "Test notification sent at {$timestamp}"
                ]);
            }
            
            return response()->json([
                'success' => false, 
                'message' => 'No inventory items found to create notification'
            ], 404);
        })->name('test.notification');
    }
});