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
    .component-card {
        transition: all 0.3s ease;
    }
    .component-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    .view-toggle .btn {
        padding: 0.25rem 0.5rem;
    }
    /* Modal backdrop styling */
    .modal-backdrop {
        opacity: 0.5 !important;
    }
    /* Add dark overlay class */
    .dark-modal .modal-backdrop {
        opacity: 0.8 !important;
        background-color: #000;
    }
    /* Body class when modal is open */
    body.modal-open-dark::after {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.7);
        z-index: 1040;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0 fw-bold">Asset Details</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Asset Details</li>
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
                    <a href="{{ route('inventory.edit', $inventoryItem->id) }}" class="btn btn-success me-2">
                        <i class="bi bi-pencil-square me-2"></i>Edit Asset
                    </a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-danger">
                        <i class="bi bi-arrow-return-left me-2"></i>Back
                    </a>
                </div>

                <!-- Asset Details Card -->
                <div class="card shadow">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="assetTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active px-4 py-3" id="asset_profile" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                                    <i class="bi bi-box-seam me-2"></i>Asset
                                </button>
                            </li>
                            @if($inventoryItem->custom_fields && count($inventoryItem->custom_fields) > 0)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="asset_custom_fields" data-bs-toggle="tab" data-bs-target="#custom_fields" type="button" role="tab" aria-controls="custom_fields" aria-selected="false">
                                    <i class="bi bi-list-check me-2"></i>Custom Fields
                                </button>
                            </li>
                            @endif
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="asset_history" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">
                                    <i class="bi bi-clock-history me-2"></i>History
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content mt-4" id="assetTabsContent">
                            <!-- Asset Profile Tab -->
                            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="asset_profile">
                                <div class="row align-items-center">
                                    <!-- Asset Image (Left) -->
                                    <div class="col-md-3 text-center">
                                        @if($inventoryItem->image_path)
                                            <img src="{{ asset('storage/' . $inventoryItem->image_path) }}" alt="{{ $inventoryItem->item_name }}" class="img-fluid rounded shadow mb-3" style="max-width: 200px; max-height: 200px;">
                                        @else
                                            <img src="{{ asset('images/default-asset.png') }}" alt="Default Asset" class="img-fluid rounded shadow mb-3" width="150">
                                        @endif
                                        <h4 class="mt-3 fw-bold">{{ $inventoryItem->item_name }}</h4>
                                        <p class="text-muted">{{ $inventoryItem->category->category ?? 'N/A' }}</p>
                                        <span class="badge bg-{{ $inventoryItem->status == 'Active' ? 'success' : 'warning' }} px-3 py-2">
                                            {{ $inventoryItem->status ?? 'Active' }}
                                        </span>
                                    </div>
                                    <!-- Asset Information (Right) -->
                                    <div class="col-md-9">
                                        <table class="table table-striped table-hover">
                                            <tr>
                                                <th width="200">Asset Tag</th>
                                                <td>{{ $inventoryItem->asset_tag }}</td>
                                            </tr>
                                            <tr>
                                                <th>Serial Number</th>
                                                <td>{{ $inventoryItem->serial_no }}</td>
                                            </tr>
                                            <tr>
                                                <th>Model Number</th>
                                                <td>{{ $inventoryItem->model_no }}</td>
                                            </tr>
                                            <tr>
                                                <th>Category</th>
                                                <td>{{ $inventoryItem->category->category ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Department</th>
                                                <td>{{ $inventoryItem->department->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Assigned To</th>
                                                <td>
                                                    @if($inventoryItem->user)
                                                        <a href="{{ route('users.view', $inventoryItem->user->id) }}">
                                                            {{ $inventoryItem->user->first_name }} {{ $inventoryItem->user->last_name }}
                                                        </a>
                                                    @else
                                                        <span class="badge bg-secondary">Not assigned</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Date Purchased</th>
                                                <td>{{ \Carbon\Carbon::parse($inventoryItem->date_purchased)->format('F d, Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Purchased From</th>
                                                <td>{{ $inventoryItem->purchased_from }}</td>
                                            </tr>
                                        </table>

                                        @if($inventoryItem->log_note)
                                        <div class="card mt-4 shadow-sm">
                                            <div class="card-header bg-white">
                                                <h5 class="card-title m-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Asset Note</h5>
                                            </div>
                                            <div class="card-body">
                                                <p>{{ $inventoryItem->log_note }}</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Fields Tab -->
                            @if($inventoryItem->custom_fields && count($inventoryItem->custom_fields) > 0)
                            <div class="tab-pane fade" id="custom_fields" role="tabpanel" aria-labelledby="asset_custom_fields">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4 fw-bold">Custom Fields</h5>
                                        <div class="row">
                                            @foreach($allCustomFields as $field)
                                                @if(isset($inventoryItem->custom_fields[$field->name]))
                                                <div class="col-md-6 mb-3">
                                                    <div class="d-flex">
                                                        <div class="fw-bold text-muted me-2" style="width: 150px;">{{ $field->name }}:</div>
                                                        <div>
                                                            @php
                                                                $fieldValue = $inventoryItem->custom_fields[$field->name];
                                                            @endphp
                                                            
                                                            @if(is_array($fieldValue))
                                                                @if(isset($fieldValue['original_name']))
                                                                    <a href="{{ asset('storage/' . $fieldValue['path']) }}" target="_blank">
                                                                        {{ $fieldValue['original_name'] }}
                                                                    </a>
                                                                @else
                                                                    {{ implode(', ', $fieldValue) }}
                                                                @endif
                                                            @else
                                                                {{ $fieldValue }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- History Tab -->
                            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="asset_history">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4 fw-bold">Asset History</h5>
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Asset history tracking will be implemented in a future update.
                                        </div>
                                    </div>
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