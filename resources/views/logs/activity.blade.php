@extends('layouts.app')

@section('content')

<style>
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    .card-header {
        background-color: white;
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.35rem;
    }
    .table thead th {
        border-bottom: 2px solid #e3e6f0;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }
    
    /* Custom Pagination Styles */
    .custom-pagination {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-top: 1.5rem;
        gap: 8px;
    }
    .page-item {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border-radius: 4px;
        font-weight: 500;
        text-decoration: none;
        background-color: #fff;
        color: #333;
        border: 1px solid #e3e6f0;
        transition: all 0.2s ease;
    }
    .page-link:hover {
        background-color: #f8f9fc;
        color: #000;
        z-index: 2;
    }
    .page-item.active .page-link {
        background-color: #212529;
        color: white;
        border-color: #212529;
    }
    .page-item.disabled .page-link {
        color: #c2c7d0;
        pointer-events: none;
        background-color: #fff;
        border-color: #e9ecef;
    }
    .page-ellipsis {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
    }
</style>

<div class="container">
    <!-- Content Header -->
    <div class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-bold">Activity Logs</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Activity Logs</li>
                </ol>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <!-- Search and Filters Card -->
            <div class="card shadow mb-3">
                <div class="card-body">
                    <form action="{{ route('logs.activity') }}" method="GET" class="row g-3">
                        <!-- Search Input -->
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Search logs..." value="{{ $search ?? '' }}">
                            </div>
                        </div>
                        
                        <!-- User Filter -->
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white"><i class="bi bi-person"></i></span>
                                <select name="user" class="form-select">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user }}" {{ isset($user_filter) && $user_filter == $user ? 'selected' : '' }}>{{ $user }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Action Type Filter -->
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white"><i class="bi bi-activity"></i></span>
                                <select name="action" class="form-select">
                                    <option value="">All Actions</option>
                                    @foreach($actionTypes as $type)
                                        <option value="{{ $type }}" {{ isset($action_filter) && $action_filter == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Date Filter -->
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white"><i class="bi bi-calendar"></i></span>
                                <select name="date" class="form-select">
                                    <option value="">All Dates</option>
                                    <option value="today" {{ isset($date_filter) && $date_filter == 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="this_week" {{ isset($date_filter) && $date_filter == 'this_week' ? 'selected' : '' }}>This Week</option>
                                    <option value="this_month" {{ isset($date_filter) && $date_filter == 'this_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="this_year" {{ isset($date_filter) && $date_filter == 'this_year' ? 'selected' : '' }}>This Year</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-dark me-2">Apply Filters</button>
                            <a href="{{ route('logs.activity') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Logs Table Card -->
            <div class="card shadow">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold">System Activity History</h5>
                    <div class="text-muted">
                        Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} results
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date & Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log['datetime'] }}</td>
                                    <td>{{ $log['user'] }}</td>
                                    <td>{{ $log['message'] }}</td>
                                    <td><span class="badge bg-dark">{{ $log['action_type'] }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No activity logs found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Custom Pagination -->
                    <div class="custom-pagination">
                        <!-- Previous Page Link -->
                        @if ($logs->onFirstPage())
                            <div class="page-item disabled">
                                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                            </div>
                        @else
                            <div class="page-item">
                                <a class="page-link" href="{{ $logs->previousPageUrl() }}" rel="prev"><i class="bi bi-chevron-left"></i></a>
                            </div>
                        @endif

                        <!-- Pagination Elements -->
                        @php
                            $currentPage = $logs->currentPage();
                            $lastPage = $logs->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $startPage + 4);
                            
                            if ($endPage - $startPage < 4 && $startPage > 1) {
                                $startPage = max(1, $endPage - 4);
                            }
                        @endphp

                        <!-- First Page Link (if not in first few pages) -->
                        @if ($startPage > 1)
                            <div class="page-item">
                                <a class="page-link" href="{{ $logs->url(1) }}">1</a>
                            </div>
                            @if ($startPage > 2)
                                <div class="page-ellipsis">...</div>
                            @endif
                        @endif

                        <!-- Page Links -->
                        @for ($page = $startPage; $page <= $endPage; $page++)
                            <div class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                                <a class="page-link" href="{{ $logs->url($page) }}">{{ $page }}</a>
                            </div>
                        @endfor

                        <!-- Last Page Link (if not in last few pages) -->
                        @if ($endPage < $lastPage)
                            @if ($endPage < $lastPage - 1)
                                <div class="page-ellipsis">...</div>
                            @endif
                            <div class="page-item">
                                <a class="page-link" href="{{ $logs->url($lastPage) }}">{{ $lastPage }}</a>
                            </div>
                        @endif

                        <!-- Next Page Link -->
                        @if ($logs->hasMorePages())
                            <div class="page-item">
                                <a class="page-link" href="{{ $logs->nextPageUrl() }}" rel="next"><i class="bi bi-chevron-right"></i></a>
                            </div>
                        @else
                            <div class="page-item disabled">
                                <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 