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
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-bold">Component Details</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('components.index') }}">Components</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Component Details</li>
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
                    <a href="{{ route('components.edit', $component) }}" class="btn btn-success me-2"><i class="bi bi-pencil-square me-2"></i>Edit</a>
                    <a href="{{ route('components.index') }}" class="btn btn-danger"><i class="bi bi-arrow-return-left me-2"></i>Back</a>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active px-4 py-3" id="component_profile" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true"><i class="bi bi-cpu me-2"></i>Component Profile</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="component_asset" data-bs-toggle="tab" data-bs-target="#asset" type="button" role="tab" aria-controls="asset" aria-selected="false"><i class="bi-box-seam me-2"></i>Associated Asset</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="component_custom_fields" data-bs-toggle="tab" data-bs-target="#custom_fields" type="button" role="tab" aria-controls="custom_fields" aria-selected="false"><i class="bi bi-card-list me-2"></i>Custom Fields</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="component_history" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false"><i class="bi bi-clock-history me-2"></i>History</button>
                            </li>
                        </ul>

                        <div class="tab-content mt-4" id="myTabContent">
                            <!-- Component Profile Tab -->
                            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="component_profile">
                                <div class="row align-items-center">
                                    <!-- Component Image (Left) -->
                                    <div class="col-md-3 text-center">
                                        <img src="{{ asset('images/component-default.png') }}" alt="Component Image" class="img-fluid rounded shadow mb-3" width="150">
                                        <h4 class="mt-3 fw-bold">{{ $component->component_name }}</h4>
                                        <p class="text-muted">{{ $component->category->category ?? 'N/A' }}</p>
                                    </div>
                                    <!-- Component Information (Right) -->
                                    <div class="col-md-9">
                                        <table class="table table-hover table-striped">
                                            <tr>
                                                <th width="200">Serial Number</th>
                                                <td>{{ $component->serial_no }}</td>
                                            </tr>
                                            <tr>
                                                <th>Model Number</th>
                                                <td>{{ $component->model_no }}</td>
                                            </tr>
                                            <tr>
                                                <th>Manufacturer</th>
                                                <td>{{ $component->manufacturer }}</td>
                                            </tr>
                                            <tr>
                                                <th>Assigned To</th>
                                                <td>
                                                    @if($component->user)
                                                        <a href="{{ route('users.view', $component->user->id) }}">
                                                            {{ $component->user->first_name }} {{ $component->user->last_name }}
                                                            <span class="text-muted">({{ $component->user->department->name ?? 'No Department' }})</span>
                                                        </a>
                                                    @else
                                                        <span class="badge bg-secondary">Not assigned</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Date Purchased</th>
                                                <td>{{ $component->date_purchased ? $component->date_purchased->format('F d, Y') : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Purchased From</th>
                                                <td>{{ $component->purchased_from }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if($component->log_note)
                                <div class="card mt-4 shadow-sm">
                                    <div class="card-header bg-white">
                                        <h5 class="card-title m-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Log Note</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ $component->log_note }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Associated Asset Tab -->
                            <div class="tab-pane fade" id="asset" role="tabpanel" aria-labelledby="component_asset">
                                @if($component->inventory)
                                    <div class="row align-items-center">
                                        <!-- Asset Image (Left) -->
                                        <div class="col-md-3 text-center">
                                            @if($component->inventory->image_path)
                                                <img src="{{ asset('storage/' . $component->inventory->image_path) }}" alt="{{ $component->inventory->item_name }}" class="img-fluid rounded shadow mb-3" style="max-width: 200px; max-height: 200px;">
                                            @else
                                                <img src="{{ asset('images/default-asset.png') }}" alt="Default Asset" class="img-fluid rounded shadow mb-3" width="150">
                                            @endif
                                            <h4 class="mt-3 fw-bold">{{ $component->inventory->item_name }}</h4>
                                            <p class="text-muted">{{ $component->inventory->category->category ?? 'N/A' }}</p>
                                            <span class="badge bg-{{ $component->inventory->status == 'Active' ? 'success' : 'warning' }} px-3 py-2">
                                                {{ $component->inventory->status }}
                                            </span>
                                        </div>
                                        <!-- Asset Information (Right) -->
                                        <div class="col-md-9">
                                            <table class="table table-hover table-striped">
                                                <tr>
                                                    <th width="200">Asset Tag</th>
                                                    <td>{{ $component->inventory->asset_tag }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Serial Number</th>
                                                    <td>{{ $component->inventory->serial_no }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Model Number</th>
                                                    <td>{{ $component->inventory->model_no }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Manufacturer</th>
                                                    <td>{{ $component->inventory->manufacturer }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Department</th>
                                                    <td>{{ $component->inventory->department->name ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Assigned To</th>
                                                    <td>
                                                        @if($component->inventory->user)
                                                            <a href="{{ route('users.view', $component->inventory->user->id) }}">
                                                                {{ $component->inventory->user->first_name }} {{ $component->inventory->user->last_name }}
                                                            </a>
                                                        @else
                                                            <span class="badge bg-secondary">Not assigned</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Date Purchased</th>
                                                    <td>{{ \Carbon\Carbon::parse($component->inventory->date_purchased)->format('F d, Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Actions</th>
                                                    <td>
                                                        <a href="{{ route('inventory.show', $component->inventory->id) }}" class="btn btn-sm btn-dark">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('inventory.edit', $component->inventory->id) }}" class="btn btn-sm btn-success">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        This component is not associated with any asset.
                                    </div>
                                @endif
                            </div>

                            <!-- Custom Fields Tab -->
                            <div class="tab-pane fade" id="custom_fields" role="tabpanel" aria-labelledby="component_custom_fields">
                                @if(!empty($component->custom_fields) && count((array)$component->custom_fields) > 0)
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Field Name</th>
                                                <th>Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach((array)$component->custom_fields as $field_name => $value)
                                                <tr>
                                                    <th width="200">{{ $field_name }}</th>
                                                    <td>
                                                        @if(is_array($value))
                                                            @if(isset($value['path']) && isset($value['original_name']))
                                                                <a href="{{ asset('storage/' . $value['path']) }}" target="_blank">
                                                                    {{ $value['original_name'] }}
                                                                </a>
                                                            @else
                                                                {{ json_encode($value) }}
                                                            @endif
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        No custom fields have been defined for this component.
                                    </div>
                                @endif
                            </div>

                            <!-- History Tab -->
                            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="component_history">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    No history records found for this component.
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