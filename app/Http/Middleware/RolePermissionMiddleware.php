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
        ],
        'staff' => [
            'dashboard.index',
            'users.my-profile',
            'users.update-my-profile',
            'inventory.index',
            'inventory.show',
            'inventory.data',
        ],
    ];


    public function handle(Request $request, Closure $next): Response
    {
        $role = strtolower(Auth::user()->role); // Adjust this to your actual role field

        // Allow all routes for admin
        if ($this->permissions[$role] === 'all') {
            return $next($request);
        }

        $currentRoute = $request->route()->getName();

        if (!in_array($currentRoute, $this->permissions[$role])) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
