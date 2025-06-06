<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionMiddleware
{
    // Defining permissions
    protected $permissions = [
        'admin' => 'all',
        'manager' => [
            'dashboard.index',
            'users.my-profile',
            'users.update-my-profile',
            'inventory.index',
            'inventory.create',
            'inventory.store',
            'inventory.edit',
            'inventory.update',
            'inventory.archive',
            'inventory.show',
            'inventory.data',
            'inventory.transfer',
            'inventory.request',
            'distributions.use-items',
            'distributions.by-item',
            'distributions.store',
            'distributions.use-items',
            'notifications.index',
            'notifications.mark-read',
            'notifications.mark-all-read',
            'notifications.check-low-inventory',
            'notifications.get-latest',
            'asset-requests.my-requests',
            'asset-requests.update-status',
            'asset-requests.show',
            'asset-requests.create',
            'asset-requests.store'
        ],
        'staff' => [
            'dashboard.index',
            'users.my-profile',
            'users.update-my-profile',
            'inventory.index',
            'inventory.show',
            'inventory.data',
            'inventory.transfer',
            'inventory.request',
            'distributions.use-items',
            'distributions.by-item',
            'distributions.store',
            'distributions.use-items',
            'notifications.index',
            'notifications.mark-read',
            'notifications.mark-all-read',
            'notifications.check-low-inventory',
            'notifications.get-latest',
            'asset-requests.my-requests',
            'asset-requests.update-status',
            'asset-requests.show',
            'asset-requests.create',
            'asset-requests.store'
        ],
    ];


    public function handle(Request $request, Closure $next): Response
    {
        $role = strtolower(Auth::user()->user_role); // Adjust this to your actual role field

        // Allow all routes for admin
        if ($this->permissions[$role] === 'all') {
            return $next($request);
        }

        $currentRoute = $request->route()->getName();

        if (!in_array($currentRoute, $this->permissions[$role])) {
            // Instead of abort, return the custom error view with 403 status code
            return response()->view('error-page.error', [], 403);
        }

        return $next($request);
    }
}
