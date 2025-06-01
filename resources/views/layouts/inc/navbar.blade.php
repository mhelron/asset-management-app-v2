    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <div class="d-flex align-items-center">
            <button class="btn btn-dark me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop" aria-controls="staticBackdrop">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="{{ route('dashboard.index') }}">Asset Inventory Management</a>
        </div>
        <div class="d-flex align-items-center">
            @auth
            <div class="dropdown me-3">
                <a href="#" class="position-relative text-white" role="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell-fill fs-5"></i>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ auth()->user()->unreadNotifications->count() > 99 ? '99+' : auth()->user()->unreadNotifications->count() }}
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <h6 class="dropdown-header m-0 p-0 fw-bold">Notifications</h6>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm text-primary p-0 border-0 bg-transparent">Mark all as read</button>
                            </form>
                        @endif
                    </div>
                    
                    <div class="notifications-container">
                        @forelse(auth()->user()->notifications->take(5) as $notification)
                            <a href="{{ isset($notification->data['request_id']) 
                                ? route('asset-requests.show', $notification->data['request_id']) 
                                : (isset($notification->data['inventory_id']) 
                                    ? route('inventory.show', $notification->data['inventory_id']) 
                                    : route('notifications.index')) }}" 
                               class="dropdown-item d-flex border-bottom py-2 {{ $notification->read_at ? '' : 'unread' }}"
                               data-notification-id="{{ $notification->id }}">
                                <div class="me-3">
                                    @if(isset($notification->data['message']) && str_contains($notification->data['message'], 'Low quantity'))
                                        <div class="bg-warning text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                        </div>
                                    @elseif(isset($notification->data['message']) && str_contains($notification->data['message'], 'request'))
                                        <div class="bg-info text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-box-seam-fill"></i>
                                        </div>
                                    @else
                                        <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-bell-fill"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0 {{ $notification->read_at ? 'text-muted' : 'fw-bold' }} text-truncate" style="max-width: 220px;">
                                        {{ $notification->data['message'] ?? 'New notification' }}
                                    </p>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </a>
                        @empty
                            <div class="dropdown-item text-center py-3">
                                <span class="text-muted">No notifications</span>
                            </div>
                        @endforelse
                    </div>
                    
                    <div class="text-center py-2 border-top">
                        <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-link text-decoration-none">View all notifications</a>
                    </div>
                </div>
            </div>
            @endauth
            <div class="text-light me-2 text-end">
                <div class="fw-bold" style="font-size: 0.8em;">{{ session('name') }}</div>
                <div style="font-size: 0.8em;">{{ session('user_role') }}</div>
            </div>
            <img src="{{ asset('images/default-user.jpg') }}" alt="Profile Picture" class="rounded-circle" width="40" height="40">
        </div>
      </div>
    </nav>
    
    <div class="offcanvas offcanvas-start bg-dark" tabindex="-1" id="staticBackdrop" aria-labelledby="staticBackdropLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="staticBackdropLabel" style="color: fff;">Menu</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
    

        <ul class="sidebar-nav">
            @php
                $role = strtolower(session('user_role'));
            @endphp

            <!-- Always visible -->
            <li class="sidebar-item">
                <a href="{{ route('dashboard.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                    <i class="bi bi-house"></i>
                    <span class="ms-2">Dashboard</span>
                </a>
            </li>

            <!-- All roles -->
            @if(in_array($role, ['admin', 'manager', 'staff']))
                <li class="sidebar-item">
                    <a href="{{ route('users.my-profile') }}" class="sidebar-link {{ request()->routeIs('users.my-profile') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i>
                        <span class="ms-2">My Profile</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('notifications.index') }}" class="sidebar-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                        <i class="bi bi-bell"></i>
                        <span class="ms-2">Notifications</span>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ auth()->user()->unreadNotifications->count() }}</span>
                        @endif
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('inventory.index') }}" class="sidebar-link {{ request()->routeIs('inventory.index', 'inventory.create', 'inventory.edit', 'inventory.show') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i>
                        <span class="ms-2">Inventory</span>
                    </a>
                </li>

                @if(in_array($role, ['admin', 'manager']))
                <li class="sidebar-item">
                    <a href="{{ route('asset-requests.index') }}" class="sidebar-link {{ request()->routeIs('asset-requests.index', 'asset-requests.show') ? 'active' : '' }}">
                        <i class="bi bi-inbox"></i>
                        <span class="ms-2">Asset Requests</span>
                    </a>
                </li>
                @else
                <li class="sidebar-item">
                    <a href="{{ route('asset-requests.my-requests') }}" class="sidebar-link {{ request()->routeIs('asset-requests.my-requests') ? 'active' : '' }}">
                        <i class="bi bi-inbox"></i>
                        <span class="ms-2">My Requests</span>
                    </a>
                </li>
                @endif
            @endif

            <!-- Settings: Admin only -->
            @if($role === 'admin')
                <li class="sidebar-item">
                    <a href="#settingsCollapse" class="sidebar-link d-flex align-items-center" data-bs-toggle="collapse"
                        role="button"
                        aria-expanded="{{ request()->routeIs('customfields.index') || request()->routeIs('categories.index') || request()->routeIs('departments.index') || request()->routeIs('asset-types.index') || request()->routeIs('locations.index') ? 'true' : 'false' }}"
                        aria-controls="settingsCollapse">
                        <i class="bi bi-gear"></i>
                        <span class="ms-2">Settings</span>
                        <i class="bi bi-chevron-left ms-auto chevron-icon"></i>
                    </a>
                    <div class="collapse sidebar-collapse {{ request()->routeIs('customfields.index') || request()->routeIs('categories.index') || request()->routeIs('departments.index') || request()->routeIs('asset-types.index') || request()->routeIs('locations.index') ? 'show' : '' }}" id="settingsCollapse">
                        <ul class="sidebar-submenu">
                            <!-- Admin only submenu -->
                            <li class="sidebar-item">
                                <a href="{{ route('asset-types.index') }}" class="sidebar-link {{ request()->routeIs('asset-types.index', 'asset-types.create', 'asset-types.edit') ? 'active' : '' }}">
                                    <i class="bi bi-tags"></i>
                                    <span class="ms-2">Asset Types</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('customfields.index') }}" class="sidebar-link {{ request()->routeIs('customfields.index', 'customfields.create', 'customfields.edit') ? 'active' : '' }}">
                                    <i class="bi bi-wrench"></i>
                                    <span class="ms-2">Custom Fields</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.index', 'categories.create', 'categories.edit') ? 'active' : '' }}">
                                    <i class="bi bi-folder"></i>
                                    <span class="ms-2">Categories</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('departments.index') }}" class="sidebar-link {{ request()->routeIs('departments.index', 'department.create', 'department.edit') ? 'active' : '' }}">
                                    <i class="bi bi-building"></i>
                                    <span class="ms-2">Departments</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('locations.index') }}" class="sidebar-link {{ request()->routeIs('locations.index', 'locations.create', 'locations.edit') ? 'active' : '' }}">
                                    <i class="bi bi-geo-alt"></i>
                                    <span class="ms-2">Locations</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.index', 'users.create', 'users.edit', 'users.view ') ? 'active' : '' }}">
                                    <i class="bi bi-people"></i>
                                    <span class="ms-2">Users</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="#" class="sidebar-link">
                                    <i class="bi bi-sliders"></i>
                                    <span class="ms-2">Appearance</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            <!-- Reports: Admin and Manager only -->
            @if($role === 'admin' || $role === 'manager')
                <li class="sidebar-item">
                    <a href="#settingsACollapse" class="sidebar-link d-flex align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="settingsACollapse">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span class="ms-2">Reports</span>
                        <i class="bi bi-chevron-left ms-auto chevron-icon"></i>
                    </a>
                    <div class="collapse sidebar-collapse" id="settingsACollapse">
                        <ul class="sidebar-submenu">
                            <li class="sidebar-item">
                                <a href="{{ route('logs.activity') }}" class="sidebar-link {{ request()->routeIs('logs.activity') ? 'active' : '' }}">
                                    <i class="bi bi-activity"></i>
                                    <span class="ms-2">Activity Logs</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="#" class="sidebar-link">
                                    <i class="bi bi-bootstrap-fill"></i>
                                    <span class="ms-2">Sample</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
        </ul>
    
        <div class="sidebar-footer">
            <a href="#" class="sidebar-link" id="logout-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-in-left"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    
     <!-- Logout Confirmation Modal -->
     <div class="modal fade dark-modal" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true" >
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                        </div>
                        <div class="modal-body">
                            <p class="pt-4 pb-4">Are you sure you want to log out?</p>
                            <!-- Align buttons to the right -->
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirm-logout-btn">Logout</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>