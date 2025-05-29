<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
/*use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;*/

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        /*Route::middleware('role', RoleMiddleware::class);
        Route::middleware('permission', PermissionMiddleware::class);
        Route::middleware('role_or_permission', RoleOrPermissionMiddleware::class); 

        Route::middleware('role.permission', \App\Http\Middleware\RolePermissionMiddleware::class); */
    }
}
