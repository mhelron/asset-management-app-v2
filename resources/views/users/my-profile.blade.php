@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #f2f5f9;
    }
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    .card-header {
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.35rem;
        background-color: white;
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
    .stat-card-icon {
        border-radius: 50%;
        padding: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
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
    .profile-info {
        padding: 1.25rem;
    }
    .profile-info .border-bottom {
        border-color: #e3e6f0 !important;
    }
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-bold pb-4">My Profile</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container">
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

        <div class="row">
            <!-- Profile Information Card -->
            <div class="col-md-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i>User Information</h5>
                        <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="bi bi-pencil-square me-2"></i>Edit
                        </button>
                    </div>
                    <div class="card-body profile-info">
                        <div class="text-center mb-4">
                            <img src="{{ asset($user->profile_picture ?? 'images/default-user.jpg') }}" alt="Profile Picture" class="img-fluid rounded-circle shadow mb-3" width="120">
                            <h4 class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</h4>
                            <span class="badge bg-dark px-3 py-2">{{ $user->user_role }}</span>
                        </div>
                        <div class="d-flex flex-column">
                            <div class="mb-3 pb-3 border-bottom">
                                <span class="text-muted d-block">Email Address</span>
                                <span class="fw-bold">{{ $user->email }}</span>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <span class="text-muted d-block">Department</span>
                                <span class="fw-bold">{{ $user->department->name ?? 'N/A' }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted d-block">Member Since</span>
                                <span class="fw-bold">{{ $user->created_at->format('F d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asset Statistics Card -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2"></i>Asset Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="p-3 rounded-3 bg-light text-center border">
                                    <h2 class="mb-1">{{ $user->assets->count() }}</h2>
                                    <p class="text-muted mb-0">Assigned Assets</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 rounded-3 bg-light text-center border">
                                    <h2 class="mb-1">{{ $user->components->count() }}</h2>
                                    <p class="text-muted mb-0">Assigned Components</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 rounded-3 bg-light text-center border">
                                    <h2 class="mb-1">0</h2>
                                    <p class="text-muted mb-0">Assigned Accessories</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Assigned Assets Tab Section -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-list-check me-2"></i>My Assignments</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active px-4 py-3" id="assets-tab" data-bs-toggle="tab" data-bs-target="#assets-tab-pane" type="button" role="tab" aria-controls="assets-tab-pane" aria-selected="true">Assets</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="components-tab" data-bs-toggle="tab" data-bs-target="#components-tab-pane" type="button" role="tab" aria-controls="components-tab-pane" aria-selected="false">Components</button>
                            </li>
                        </ul>
                        <div class="tab-content mt-4" id="myTabContent">
                            <!-- Assets Tab -->
                            <div class="tab-pane fade show active" id="assets-tab-pane" role="tabpanel" aria-labelledby="assets-tab" tabindex="0">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Asset</th>
                                                <th>Category</th>
                                                <th>Serial Number</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($user->assets as $asset)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('inventory.show', $asset->id) }}" class="text-decoration-none">{{ $asset->name }}</a>
                                                </td>
                                                <td>{{ $asset->category->name ?? 'N/A' }}</td>
                                                <td>{{ $asset->serial_number }}</td>
                                                <td>
                                                    <span class="badge bg-success">Assigned</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-3">No assets assigned yet</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Components Tab -->
                            <div class="tab-pane fade" id="components-tab-pane" role="tabpanel" aria-labelledby="components-tab" tabindex="0">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Component</th>
                                                <th>Category</th>
                                                <th>Serial Number</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($user->components as $component)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('components.show', $component->id) }}" class="text-decoration-none">{{ $component->name }}</a>
                                                </td>
                                                <td>{{ $component->category->name ?? 'N/A' }}</td>
                                                <td>{{ $component->serial_number }}</td>
                                                <td>
                                                    <span class="badge bg-success">Assigned</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-3">No components assigned yet</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.update-my-profile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}">
                    </div>
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                        <small class="text-muted">Upload a square image for best results (JPG, PNG - max 2MB)</small>
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" class="form-control" value="{{ $user->department->name ?? 'N/A' }}" readonly>
                        <small class="text-muted">Department can only be changed by an administrator.</small>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                        <small class="text-muted">Password must contain at least 8 characters, including uppercase, number and special character.</small>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 