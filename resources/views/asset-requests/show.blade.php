@extends('layouts.app')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Asset Request Details</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('asset-requests.index') }}">Asset Requests</a></li>
                    <li class="breadcrumb-item active">Request #{{ $assetRequest->id }}</li>
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

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('asset-requests.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Requests
                    </a>
                </div>

                <div class="row">
                    <!-- Request Details -->
                    <div class="col-md-8">
                        <div class="card shadow h-100">
                            <div class="card-header bg-white">
                                <h5 class="card-title m-0 fw-bold">Request Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Request ID</h6>
                                        <p>{{ $assetRequest->id }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Status</h6>
                                        <p>
                                            @if($assetRequest->status == 'Pending')
                                                <span class="badge bg-warning px-3 py-2">Pending</span>
                                            @elseif($assetRequest->status == 'Approved')
                                                <span class="badge bg-success px-3 py-2">Approved</span>
                                            @elseif($assetRequest->status == 'Rejected')
                                                <span class="badge bg-danger px-3 py-2">Rejected</span>
                                            @elseif($assetRequest->status == 'Completed')
                                                <span class="badge bg-primary px-3 py-2">Completed</span>
                                            @else
                                                <span class="badge bg-secondary px-3 py-2">{{ $assetRequest->status }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Requested By</h6>
                                        <p>
                                            <a href="{{ route('users.view', $assetRequest->user->id) }}">
                                                {{ $assetRequest->user->first_name }} {{ $assetRequest->user->last_name }}
                                            </a>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Department</h6>
                                        <p>{{ $assetRequest->user->department->name ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Date Requested</h6>
                                        <p>{{ $assetRequest->created_at->format('F d, Y h:i A') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Date Needed</h6>
                                        <p>{{ \Carbon\Carbon::parse($assetRequest->date_needed)->format('F d, Y') }}</p>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="fw-bold">Reason for Request</h6>
                                        <p>{{ $assetRequest->reason }}</p>
                                    </div>
                                </div>

                                @if($assetRequest->status_note)
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="fw-bold">Status Note</h6>
                                        <p>{{ $assetRequest->status_note }}</p>
                                    </div>
                                </div>
                                @endif

                                @if($assetRequest->processed_by)
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Processed By</h6>
                                        <p>
                                            <a href="{{ route('users.view', $assetRequest->processor->id) }}">
                                                {{ $assetRequest->processor->first_name }} {{ $assetRequest->processor->last_name }}
                                            </a>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Processed At</h6>
                                        <p>{{ $assetRequest->processed_at ? $assetRequest->processed_at->format('F d, Y h:i A') : 'N/A' }}</p>
                                    </div>
                                </div>
                                @endif

                                @if($assetRequest->status == 'Pending')
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="fw-bold mb-3">Update Request Status</h5>
                                        <form action="{{ route('asset-requests.update-status', $assetRequest->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="Approved">Approve</option>
                                                    <option value="Rejected">Reject</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="status_note" class="form-label">Status Note</label>
                                                <textarea class="form-control" id="status_note" name="status_note" rows="3" placeholder="Add a note about your decision"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Update Status</button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Asset Information -->
                    <div class="col-md-4">
                        <div class="card shadow h-100">
                            <div class="card-header bg-white">
                                <h5 class="card-title m-0 fw-bold">Requested Asset</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    @if($assetRequest->inventory->image_path)
                                        <img src="{{ asset('storage/' . $assetRequest->inventory->image_path) }}" alt="{{ $assetRequest->inventory->item_name }}" class="img-fluid rounded shadow mb-3" style="max-width: 100%; max-height: 150px;">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                                            <i class="bi bi-box-seam fs-1 text-secondary"></i>
                                        </div>
                                    @endif
                                </div>

                                <h5 class="fw-bold">{{ $assetRequest->inventory->item_name }}</h5>
                                <p class="text-muted">{{ $assetRequest->inventory->category->category ?? 'N/A' }}</p>

                                <ul class="list-group list-group-flush mt-3">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Asset Tag</span>
                                        <span class="fw-bold">{{ $assetRequest->inventory->asset_tag }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Serial Number</span>
                                        <span class="fw-bold">{{ $assetRequest->inventory->serial_no }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Model</span>
                                        <span class="fw-bold">{{ $assetRequest->inventory->model_no }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Manufacturer</span>
                                        <span class="fw-bold">{{ $assetRequest->inventory->manufacturer }}</span>
                                    </li>
                                    @if($assetRequest->inventory->has_quantity)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Available Quantity</span>
                                        <span class="fw-bold">{{ $assetRequest->inventory->quantity }}</span>
                                    </li>
                                    @endif
                                </ul>

                                <div class="d-grid gap-2 mt-3">
                                    <a href="{{ route('inventory.show', $assetRequest->inventory->id) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> View Asset Details
                                    </a>
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