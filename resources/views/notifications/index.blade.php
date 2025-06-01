@extends('layouts.app')

@section('content')
<style>
    .notification-item {
        transition: all 0.2s;
        border-left: 4px solid transparent;
    }
    .notification-item:hover {
        background-color: #f8f9fa;
    }
    .notification-item.unread {
        border-left: 4px solid #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }
    .notification-time {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .notification-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    .notification-message {
        margin-bottom: 0;
    }
    .notification-badge {
        font-size: 0.7rem;
    }
    .notification-actions {
        opacity: 0.2;
        transition: opacity 0.2s;
    }
    .notification-item:hover .notification-actions {
        opacity: 1;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Notifications</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Notifications</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <!-- Alert Box for Success -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Alert Box for Errors-->
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-diamond me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold">Your Notifications</h5>
                        @if(count($notifications) > 0)
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-check-all me-1"></i> Mark All as Read
                            </button>
                        </form>
                        @endif
                    </div>
                    <div class="card-body">
                        @if(count($notifications) > 0)
                            <div class="list-group">
                                @foreach($notifications as $notification)
                                    <div class="list-group-item notification-item d-flex align-items-start p-3 {{ $notification->read_at ? '' : 'unread' }}">
                                        <div class="me-3">
                                            @if(isset($notification->data['message']) && str_contains($notification->data['message'], 'Low quantity'))
                                                <div class="notification-icon bg-warning text-white rounded-circle p-2">
                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                </div>
                                            @elseif(isset($notification->data['message']) && str_contains($notification->data['message'], 'request'))
                                                <div class="notification-icon bg-info text-white rounded-circle p-2">
                                                    <i class="bi bi-box-seam-fill"></i>
                                                </div>
                                            @else
                                                <div class="notification-icon bg-primary text-white rounded-circle p-2">
                                                    <i class="bi bi-bell-fill"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="notification-title">
                                                    @if(isset($notification->data['message']))
                                                        {{ $notification->data['message'] }}
                                                    @else
                                                        New Notification
                                                    @endif
                                                    
                                                    @if(!$notification->read_at)
                                                        <span class="badge bg-primary notification-badge ms-2">New</span>
                                                    @endif
                                                </h6>
                                                <small class="notification-time">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            
                                            <p class="notification-message">
                                                @if(isset($notification->data['item_name']))
                                                    <strong>Item:</strong> {{ $notification->data['item_name'] }}
                                                @endif
                                                
                                                @if(isset($notification->data['quantity']) && isset($notification->data['min_quantity']))
                                                    <br><strong>Quantity:</strong> {{ $notification->data['quantity'] }} 
                                                    (Min: {{ $notification->data['min_quantity'] }})
                                                @endif
                                                
                                                @if(isset($notification->data['user_name']))
                                                    <br><strong>Requested by:</strong> {{ $notification->data['user_name'] }}
                                                @endif
                                                
                                                @if(isset($notification->data['date_needed']))
                                                    <br><strong>Needed by:</strong> {{ \Carbon\Carbon::parse($notification->data['date_needed'])->format('M d, Y') }}
                                                @endif
                                            </p>
                                            
                                            <div class="d-flex mt-2">
                                                @if(isset($notification->data['inventory_id']))
                                                    <a href="{{ route('inventory.show', $notification->data['inventory_id']) }}" class="btn btn-sm btn-outline-secondary me-2">
                                                        <i class="bi bi-eye me-1"></i> View Item
                                                    </a>
                                                @endif
                                                
                                                @if(isset($notification->data['request_id']))
                                                    <a href="{{ route('asset-requests.show', $notification->data['request_id']) }}" class="btn btn-sm btn-outline-secondary me-2">
                                                        <i class="bi bi-file-earmark-text me-1"></i> View Request
                                                    </a>
                                                @endif
                                                
                                                @if(!$notification->read_at)
                                                    <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="notification-actions">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-check2 me-1"></i> Mark as Read
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4">
                                {{ $notifications->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">No Notifications</h5>
                                <p class="text-muted">You don't have any notifications yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 