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
    .nav-tabs .nav-link {
        color: #5a5c69;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #212529;
        font-weight: 600;
        border-bottom: 3px solid #212529;
    }
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-bold">User Profile</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User Profile</li>
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

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('users.index') }}" class="btn btn-danger"><i class="bi bi-arrow-return-left me-2"></i>Back</a>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active px-4 py-3" id="user_profile" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false"><i class="bi bi-person-circle me-2"></i>Profile</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="user_assets" data-bs-toggle="tab" data-bs-target="#asset" type="button" role="tab" aria-controls="asset" aria-selected="false"><i class="bi-box-seam me-2"></i>Assets</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="user_accessories" data-bs-toggle="tab" data-bs-target="#accessories" type="button" role="tab" aria-controls="accessories" aria-selected="false"><i class="bi bi-headphones me-2"></i>Accessories</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="user_history" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false"><i class="bi bi-clock-history me-2"></i>History</button>
                            </li>
                        </ul>

                        <div class="tab-content mt-4" id="myTabContent">
                            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="user_profile">
                                <div class="row align-items-center">
                                    <!-- Profile Picture (Left) -->
                                    <div class="col-md-3 text-center">
                                        <img src="{{ asset('images/default-user.jpg') }}" alt="User Avatar" class="img-fluid rounded-circle shadow" width="150">
                                        <h4 class="mt-3 fw-bold">{{ $user->first_name }} {{ $user->last_name }}</h4>
                                        <p class="text-muted">{{ $user->email }}</p>
                                        <span class="badge bg-dark px-3 py-2 mb-3">{{ $user->user_role }}</span>
                                    </div>
                                    <!-- User Information (Right) -->
                                    <div class="col-md-9">
                                        <table class="table table-striped table-hover">
                                            <tr>
                                                <th width="150">Full Name</th>
                                                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td>{{ $user->email }}</td>
                                            </tr>
                                            <tr>
                                                <th>Role</th>
                                                <td>{{ $user->user_role }}</td>
                                            </tr>
                                            <tr>
                                                <th>Department</th>
                                                <td>{{ $user->department->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Created At</th>
                                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Updated At</th>
                                                <td>{{ $user->updated_at->format('d M Y') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade " id="asset" role="tabpanel" aria-labelledby="user_assets">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0 fw-bold">Assigned Assets</h5>
                                    <a href="{{ route('inventory.create') }}" class="btn btn-sm btn-dark">
                                        <i class="bi bi-plus-lg me-2"></i>Add Asset
                                    </a>
                                </div>
                                @if($user->assets && $user->assets->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Asset Name</th>
                                                <th>Asset Tag</th>
                                                <th>Category</th>
                                                <th>Serial No</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->assets as $index => $asset)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong><a href="{{ route('inventory.show', $asset->id) }}" class="text-decoration-none">{{ $asset->item_name }}</a></strong></td>
                                                <td>{{ $asset->asset_tag }}</td>
                                                <td>{{ $asset->category->category ?? 'N/A' }}</td>
                                                <td>{{ $asset->serial_no }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $asset->status == 'Active' ? 'success' : 'warning' }}">
                                                        {{ $asset->status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('inventory.show', $asset->id) }}" class="btn btn-sm btn-dark">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('inventory.edit', $asset->id) }}" class="btn btn-sm btn-success">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    No assets assigned to this user yet.
                                </div>
                                @endif
                            </div>

                            <div class="tab-pane fade " id="accessories" role="tabpanel" aria-labelledby="user_accessories">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    No accessories assigned to this user.
                                </div>
                            </div>

                            <div class="tab-pane fade " id="history" role="tabpanel" aria-labelledby="user_history">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    No history records found for this user.
                                </div>
                            </div>
                        </div>                       
                    </div>
                </div>       
            </div>
        </div>
    </div>
</div>

@endsection