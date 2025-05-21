@extends('layouts.app')

@section('content')

<style>
    .nav-link{
        color:black;
    }
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    .card-header {
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.35rem;
        background-color: #fff;
    }
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="m-0 fw-bold">Accessory Details</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('accessory.index') }}">Accessories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Accessory Details</li>
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
                    <a href="{{ route('accessory.edit', $accessory) }}" class="btn btn-success me-2"><i class="bi bi-pencil-square me-2"></i>Edit</a>
                    <a href="{{ route('accessory.index') }}" class="btn btn-danger"><i class="bi bi-arrow-return-left me-2"></i>Back</a>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="accessory_profile" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true"><i class="bi bi-usb-symbol me-2"></i>Accessory Profile</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="accessory_user" data-bs-toggle="tab" data-bs-target="#user" type="button" role="tab" aria-controls="user" aria-selected="false"><i class="bi bi-person me-2"></i>Assigned User</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="accessory_history" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false"><i class="bi bi-clock-history me-2"></i>History</button>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="myTabContent">
                            <!-- Accessory Profile Tab -->
                            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="accessory_profile">
                                <div class="row align-items-center">
                                    <!-- Accessory Image (Left) -->
                                    <div class="col-md-3 text-center">
                                        <img src="{{ asset('images/accessory-default.png') }}" alt="Accessory Image" class="img-fluid rounded" width="150">
                                        <h4 class="mt-3">{{ $accessory->accessory_name }}</h4>
                                        <p class="text-muted">{{ $accessory->category }}</p>
                                    </div>
                                    <!-- Accessory Information (Right) -->
                                    <div class="col-md-9">
                                        <table class="table table-striped">
                                            <tr>
                                                <th>Serial Number</th>
                                                <td>{{ $accessory->serial_no }}</td>
                                            </tr>
                                            <tr>
                                                <th>Model Number</th>
                                                <td>{{ $accessory->model_no }}</td>
                                            </tr>
                                            <tr>
                                                <th>Manufacturer</th>
                                                <td>{{ $accessory->manufacturer }}</td>
                                            </tr>
                                            <tr>
                                                <th>Department</th>
                                                <td>
                                                    @if($accessory->department_id && $accessory->department)
                                                        {{ $accessory->department->name }}
                                                    @else
                                                        <span class="badge bg-secondary">Not assigned</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Assigned To</th>
                                                <td>
                                                    @if($accessory->users_id && $accessory->user)
                                                        <a href="{{ route('users.view', $accessory->user->id) }}">
                                                            {{ $accessory->user->first_name }} {{ $accessory->user->last_name }}
                                                            <span class="text-muted">({{ $accessory->user->department->name ?? 'No Department' }})</span>
                                                        </a>
                                                    @else
                                                        <span class="badge bg-secondary">Not assigned</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Date Purchased</th>
                                                <td>{{ $accessory->date_purchased ? \Carbon\Carbon::parse($accessory->date_purchased)->format('F d, Y') : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Purchased From</th>
                                                <td>{{ $accessory->purchased_from }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if($accessory->log_note)
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="card-title">Log Note</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ $accessory->log_note }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Assigned User Tab -->
                            <div class="tab-pane fade" id="user" role="tabpanel" aria-labelledby="accessory_user">
                                @if($accessory->users_id && $accessory->user)
                                    <div class="row align-items-center">
                                        <!-- User Image (Left) -->
                                        <div class="col-md-3 text-center">
                                            <img src="{{ asset('images/default-user.jpg') }}" alt="User" class="img-fluid rounded-circle" width="150">
                                            <h4 class="mt-3">{{ $accessory->user->first_name }} {{ $accessory->user->last_name }}</h4>
                                            <p class="text-muted">{{ $accessory->user->email }}</p>
                                        </div>
                                        <!-- User Information (Right) -->
                                        <div class="col-md-9">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th>Full Name</th>
                                                    <td>{{ $accessory->user->first_name }} {{ $accessory->user->last_name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Email</th>
                                                    <td>{{ $accessory->user->email }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Role</th>
                                                    <td>{{ $accessory->user->user_role }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Department</th>
                                                    <td>{{ $accessory->user->department->name ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Actions</th>
                                                    <td>
                                                        <a href="{{ route('users.view', $accessory->user->id) }}" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-eye me-2"></i>View User Profile
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        This accessory is not assigned to any user.
                                    </div>
                                @endif
                            </div>

                            <!-- History Tab -->
                            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="accessory_history">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    No history records found for this accessory.
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
