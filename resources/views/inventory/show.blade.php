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
            <div class="col-sm-6">
                <h1 class="m-0 fw-bold">Asset Details</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Assets</a></li>
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
                    <a href="{{ route('inventory.edit', $inventory->id) }}" class="btn btn-success me-2">
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
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="asset_components" data-bs-toggle="tab" data-bs-target="#components" type="button" role="tab" aria-controls="components" aria-selected="false">
                                    <i class="bi bi-cpu me-2"></i>Components
                                </button>
                            </li>
                            @if($inventory->custom_fields && count($inventory->custom_fields) > 0)
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
                                        @if($inventory->image_path)
                                            <img src="{{ asset('storage/' . $inventory->image_path) }}" alt="{{ $inventory->item_name }}" class="img-fluid rounded shadow mb-3" style="max-width: 200px; max-height: 200px;">
                                        @else
                                            <img src="{{ asset('images/default-asset.png') }}" alt="Default Asset" class="img-fluid rounded shadow mb-3" width="150">
                                        @endif
                                        <h4 class="mt-3 fw-bold">{{ $inventory->item_name }}</h4>
                                        <p class="text-muted">{{ $inventory->category->category ?? 'N/A' }}</p>
                                        <span class="badge bg-{{ $inventory->status == 'Active' ? 'success' : 'warning' }} px-3 py-2">
                                            {{ $inventory->status }}
                                        </span>
                                    </div>
                                    <!-- Asset Information (Right) -->
                                    <div class="col-md-9">
                                        <table class="table table-striped table-hover">
                                            <tr>
                                                <th width="200">Asset Tag</th>
                                                <td>{{ $inventory->asset_tag }}</td>
                                            </tr>
                                            <tr>
                                                <th>Serial Number</th>
                                                <td>{{ $inventory->serial_no }}</td>
                                            </tr>
                                            <tr>
                                                <th>Model Number</th>
                                                <td>{{ $inventory->model_no }}</td>
                                            </tr>
                                            <tr>
                                                <th>Manufacturer</th>
                                                <td>{{ $inventory->manufacturer }}</td>
                                            </tr>
                                            <tr>
                                                <th>Department</th>
                                                <td>{{ $inventory->department->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Assigned To</th>
                                                <td>
                                                    @if($inventory->user)
                                                        <a href="{{ route('users.view', $inventory->user->id) }}">
                                                            {{ $inventory->user->first_name }} {{ $inventory->user->last_name }}
                                                        </a>
                                                    @else
                                                        <span class="badge bg-secondary">Not assigned</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Date Purchased</th>
                                                <td>{{ \Carbon\Carbon::parse($inventory->date_purchased)->format('F d, Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Purchased From</th>
                                                <td>{{ $inventory->purchased_from }}</td>
                                            </tr>
                                        </table>

                                        @if($inventory->log_note)
                                        <div class="card mt-4 shadow-sm">
                                            <div class="card-header bg-white">
                                                <h5 class="card-title m-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Log Note</h5>
                                            </div>
                                            <div class="card-body">
                                                <p>{{ $inventory->log_note }}</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Components Tab -->
                            <div class="tab-pane fade" id="components" role="tabpanel" aria-labelledby="asset_components">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0 fw-bold">Associated Components</h5>
                                    <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#addComponentModal">
                                        <i class="bi bi-plus-lg me-2"></i>Add Component
                                    </button>
                                </div>
                                
                                @if($inventory->components && $inventory->components->count() > 0)
                                <!-- Table View -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Component Name</th>
                                                <th>Category</th>
                                                <th>Serial No</th>
                                                <th>Model No</th>
                                                <th>Manufacturer</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inventory->components as $index => $component)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong><a href="{{ route('components.show', $component->id) }}" class="text-decoration-none">{{ $component->component_name }}</a></strong></td>
                                                <td>{{ $component->category->category ?? 'N/A' }}</td>
                                                <td>{{ $component->serial_no }}</td>
                                                <td>{{ $component->model_no }}</td>
                                                <td>{{ $component->manufacturer }}</td>
                                                <td>
                                                    <a href="{{ route('components.show', $component->id) }}" class="btn btn-sm btn-dark me-1">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('components.edit', $component->id) }}" class="btn btn-sm btn-success">
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
                                    No components are associated with this asset yet. Click "Add Component" to add one.
                                </div>
                                @endif
                            </div>

                            <!-- Custom Fields Tab -->
                            @if($inventory->custom_fields && count($inventory->custom_fields) > 0)
                            <div class="tab-pane fade" id="custom_fields" role="tabpanel" aria-labelledby="asset_custom_fields">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="200">Field Name</th>
                                                <th>Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inventory->custom_fields as $key => $value)
                                            <tr>
                                                <th>{{ $key }}</th>
                                                <td>
                                                    @if(is_array($value))
                                                        @if(isset($value['path']))
                                                            <a href="{{ asset('storage/' . $value['path']) }}" target="_blank">
                                                                {{ $value['original_name'] ?? 'View File' }}
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
                                </div>
                            </div>
                            @endif

                            <!-- History Tab -->
                            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="asset_history">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    No history records found for this asset.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for View Toggle -->
<script>
    // Remove view toggle script since we removed the toggle buttons
</script>

@endsection 

<!-- Add Component Modal -->
<div class="modal fade" id="addComponentModal" tabindex="-1" aria-labelledby="addComponentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addComponentModalLabel">Add Component to Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Available Components</h6>
                        <div>
                            <a href="{{ route('components.create') }}" class="btn btn-sm btn-dark">
                                <i class="bi bi-plus-lg me-1"></i>Add New Component
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive" id="availableComponentsTable">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Component Name</th>
                                    <th>Category</th>
                                    <th>Serial No</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center">Loading components...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Message Modal -->
<div class="modal fade dark-modal" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
            </div>
            <div class="modal-body">
                <p class="pt-4 pb-4 text-center" id="successMessage">Component associated successfully!</p>
                <!-- Align buttons to the right -->
                <div class="text-end">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-dark" id="successModalRefreshBtn">Okay</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Component Modal -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Debug log to verify script is loaded
    console.log('Component modal script loaded');
    
    // Setup success modal with dark backdrop
    const successModalEl = document.getElementById('successModal');
    const successModal = new bootstrap.Modal(successModalEl, {
        backdrop: 'static'
    });
    
    // Add a class to body when modal is shown to ensure darker backdrop
    successModalEl.addEventListener('show.bs.modal', function () {
        document.body.classList.add('modal-open-dark');
    });
    
    successModalEl.addEventListener('hidden.bs.modal', function () {
        document.body.classList.remove('modal-open-dark');
    });
    
    document.getElementById('successModalRefreshBtn').addEventListener('click', function() {
        window.location.reload();
    });
    
    // Load available components when modal is shown
    document.getElementById('addComponentModal').addEventListener('shown.bs.modal', function() {
        console.log('Modal shown, loading components...');
        loadAvailableComponents();
    });
    
    // Function to load available components (not associated with any asset)
    function loadAvailableComponents() {
        // Show loading indicator
        const tableBody = document.querySelector('#availableComponentsTable tbody');
        tableBody.innerHTML = '<tr><td colspan="4" class="text-center"><div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Loading components...</td></tr>';
        
        // Make the API request with full URL
        const url = window.location.origin + '/components/available';
        console.log('Fetching components from:', url);
        
        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Components loaded:', data);
                
                if (!data || data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="4" class="text-center">No available components found. <a href="{{ route("components.create") }}">Add a new component</a>.</td></tr>';
                    return;
                }
                
                tableBody.innerHTML = '';
                data.forEach(component => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${component.component_name}</td>
                        <td>${component.category ? component.category.category : 'N/A'}</td>
                        <td>${component.serial_no}</td>
                        <td>
                            <form method="POST" action="${window.location.origin}/components/associate/${component.id}" class="associate-form">
                                @csrf
                                <input type="hidden" name="inventory_id" value="{{ $inventory->id }}">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-link me-1"></i>Associate
                                </button>
                            </form>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                
                // Add event listeners to associate forms
                document.querySelectorAll('.associate-form').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const submitBtn = this.querySelector('button[type="submit"]');
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div> Associating...';
                        
                        // Submit the form using fetch to handle the response
                        fetch(this.action, {
                            method: 'POST',
                            body: new FormData(this),
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Association response:', data);
                            if (data.success) {
                                // Hide the component modal
                                const componentModal = bootstrap.Modal.getInstance(document.getElementById('addComponentModal'));
                                componentModal.hide();
                                
                                // Show success message in the success modal
                                document.getElementById('successMessage').textContent = 'Component associated successfully! Click the button below to refresh the page.';
                                successModal.show();
                            } else {
                                alert('Error: ' + data.message);
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = '<i class="bi bi-link me-1"></i>Associate';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred. Please try again.');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="bi bi-link me-1"></i>Associate';
                        });
                    });
                });
            })
            .catch(error => {
                console.error('Error loading components:', error);
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading components: ${error.message}.<br>
                    <a href="{{ route('components.create') }}" class="btn btn-sm btn-primary mt-2">Create new component</a>
                </td></tr>`;
            });
    }
});
</script> 