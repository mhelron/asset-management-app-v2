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


Route::middleware(['auth'])->group(function () {
    
    // Dasbord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');   

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
        Route::get('get-item-details/{id}', [InventoryController::class, 'getItemDetails']);
        Route::get('get-custom-fields/{id}', [InventoryController::class, 'getCustomFields']);
        Route::post('add-note/{id}', [InventoryController::class, 'addNote'])->name('inventory.add-note');
        Route::put('update-note/{inventory}/{note}', [InventoryController::class, 'updateNote'])->name('inventory.update-note');
        Route::delete('delete-note/{inventory}/{note}', [InventoryController::class, 'deleteNote'])->name('inventory.delete-note');
        Route::get('generate-qr/{id}', [InventoryController::class, 'generateQRCode'])->name('inventory.generate-qr');
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
});