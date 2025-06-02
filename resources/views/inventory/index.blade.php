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
    .asset-card {
        transition: all 0.3s ease;
    }
    .asset-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    .view-toggle .btn {
        padding: 0.25rem 0.5rem;
    }
</style>

<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0 fw-bold">Inventory</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Inventory</li>
                </ol>
            </div>
        </div>  
    </div>
</div>

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
                
                <div class="d-flex justify-content-between align-items-center mb-3 action-buttons">
                    <div class="view-toggle btn-group">
                        <button type="button" class="btn btn-sm btn-outline-dark active" id="table-view-btn">
                            <i class="bi bi-table"></i> Table
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-dark" id="grid-view-btn">
                            <i class="bi bi-grid-3x3-gap"></i> Grid
                        </button>
                    </div>
                    <a href="{{ route('inventory.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg me-2"></i>Add Item</a>
                </div>

                <div class="card shadow">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold">Inventory</h5>
                    </div>
                    <div class="card-body">
                        <!-- Table View -->
                        <div id="table-view" class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Asset Name</th>
                                        <th>Image</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Owner</th>
                                        <th>Location</th>
                                        <th>Asset Tag</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($inventory as $index => $item)
                                    <tr class="align-middle">
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong><a href="{{ route('inventory.show', $item->id) }}" class="text-decoration-none">{{ $item->item_name }}</a></strong></td>
                                        <td>
                                            @if($item->image_path)
                                                <img src="{{ asset('storage/' . $item->image_path) }}" alt="Asset Image" width="60" height="60" style="object-fit: cover;" class="rounded">
                                            @else
                                                <span class="text-muted">No image</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->assetType->name ?? 'N/A' }}</td>
                                        <td>{{ $item->category->category ?? 'N/A' }}</td>
                                        <td>
                                            @if($item->users_id && $item->user)
                                            <span class="badge bg-secondary">{{ $item->user->first_name }} {{ $item->user->last_name }}</span>
                                            @elseif($item->department_id && $item->department)
                                                <span class="badge bg-secondary">{{ $item->department->name }}</span>
                                            @elseif($item->location_id && $item->location)
                                                <span class="badge bg-secondary">{{ $item->location->name }}</span>
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->location_id && $item->location)
                                                {{ $item->location->name }}
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $item->asset_tag }}</span></td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('inventory.show', $item->id) }}" class="btn btn-sm btn-dark me-1" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-sm btn-success me-1" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-warning me-1 transfer-btn" 
                                                    data-bs-toggle="modal" data-bs-target="#transferModal" 
                                                    data-id="{{ $item->id }}" data-name="{{ $item->item_name }}" title="Transfer">
                                                    <i class="bi bi-arrow-left-right"></i>
                                                </button>
                                                @if($item->is_requestable)
                                                <button type="button" class="btn btn-sm btn-info me-1 request-btn" 
                                                    data-bs-toggle="modal" data-bs-target="#requestModal" 
                                                    data-id="{{ $item->id }}" data-name="{{ $item->item_name }}" title="Request">
                                                    <i class="bi bi-hand-index-thumb"></i>
                                                </button>
                                                @endif
                                                <button type="button" class="btn btn-sm btn-danger archive-btn" 
                                                    data-bs-toggle="modal" data-bs-target="#archiveModal" 
                                                    data-id="{{ $item->id }}" data-name="{{ $item->item_name }}" title="Archive">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="mb-3 text-muted">
                                                <i class="bi bi-inbox-fill fs-2"></i>
                                                <p class="mt-2">No assets found in your inventory</p>
                                            </div>
                                            <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                                                <i class="bi bi-plus-lg me-2"></i>Add Your First Asset
                                            </a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Grid View -->
                        <div id="grid-view" class="row" style="display: none;">
                            @forelse ($inventory as $item)
                            <div class="col-md-4 col-lg-3 mb-4">
                                <div class="card h-100 asset-card shadow-sm">
                                    <div class="card-body text-center">
                                        @if($item->image_path)
                                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="Asset Image" class="img-fluid rounded mb-3" style="height: 120px; object-fit: contain;">
                                        @else
                                            <img src="{{ asset('images/default-asset.png') }}" alt="Default Asset" class="img-fluid rounded mb-3" width="80">
                                        @endif
                                        <h5 class="card-title">
                                            <a href="{{ route('inventory.show', $item->id) }}" class="text-decoration-none">{{ $item->item_name }}</a>
                                        </h5>
                                        <p class="card-text text-muted small mb-2">{{ $item->assetType->name ?? 'N/A' }} / {{ $item->category->category ?? 'N/A' }}</p>
                                        <p class="card-text small mb-1"><strong>Tag:</strong> {{ $item->asset_tag }}</p>
                                        <p class="card-text small mb-1"><strong>Owner:</strong> 
                                            @if($item->users_id && $item->user)
                                                {{ $item->user->first_name }} {{ $item->user->last_name }}
                                            @elseif($item->department_id && $item->department)
                                                {{ $item->department->name }}
                                            @elseif($item->location_id && $item->location)
                                                {{ $item->location->name }}
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </p>
                                        <p class="card-text small"><strong>Location:</strong> 
                                            @if($item->location_id && $item->location)
                                                {{ $item->location->name }}
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0 d-flex justify-content-center">
                                        <a href="{{ route('inventory.show', $item->id) }}" class="btn btn-sm btn-dark me-2">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-sm btn-success me-2">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-primary me-2 transfer-btn" 
                                            data-bs-toggle="modal" data-bs-target="#transferModal" 
                                            data-id="{{ $item->id }}" data-name="{{ $item->item_name }}">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </button>
                                        @if($item->is_requestable)
                                        <button type="button" class="btn btn-sm btn-info me-2 request-btn" 
                                            data-bs-toggle="modal" data-bs-target="#requestModal" 
                                            data-id="{{ $item->id }}" data-name="{{ $item->item_name }}">
                                            <i class="bi bi-hand-index-thumb"></i>
                                        </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger archive-btn" 
                                            data-bs-toggle="modal" data-bs-target="#archiveModal" 
                                            data-id="{{ $item->id }}" data-name="{{ $item->item_name }}">
                                            <i class="bi bi-archive"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center py-5">
                                    <i class="bi bi-inbox-fill fs-2 d-block mb-3"></i>
                                    <p>No assets found in your inventory</p>
                                    <a href="{{ route('inventory.create') }}" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-lg me-2"></i>Add Your First Asset
                                    </a>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="archiveModalLabel">Confirm Archive</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to archive <strong id="inventoryName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="archiveInventoryForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Archive</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Asset Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="transferModalLabel">Transfer Asset</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="transferAssetForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p>Transfer <strong id="transferAssetName"></strong> to:</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Transfer To</label>
                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="transfer_to_type" id="transfer_to_user" value="user" autocomplete="off" checked>
                            <label class="btn btn-outline-primary" for="transfer_to_user">User</label>
                            
                            <input type="radio" class="btn-check" name="transfer_to_type" id="transfer_to_department" value="department" autocomplete="off">
                            <label class="btn btn-outline-primary" for="transfer_to_department">Department</label>
                            
                            <input type="radio" class="btn-check" name="transfer_to_type" id="transfer_to_location" value="location" autocomplete="off">
                            <label class="btn btn-outline-primary" for="transfer_to_location">Location</label>
                        </div>
                        
                        <div id="transfer_user_container">
                            <select name="users_id" id="transfer_user_id" class="form-select">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div id="transfer_department_container" style="display: none;">
                            <select name="department_id" id="transfer_department_id" class="form-select">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div id="transfer_location_container" style="display: none;">
                            <select name="location_id" id="transfer_location_id" class="form-select">
                                <option value="">Select Location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="transfer_note" class="form-label">Note (Optional)</label>
                        <textarea class="form-control" id="transfer_note" name="transfer_note" rows="3" placeholder="Add a note about this transfer"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Transfer Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Request Asset Modal -->
<div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="requestModalLabel">Request Asset</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="requestAssetForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p>Request <strong id="requestAssetName"></strong></p>
                    
                    <div class="mb-3">
                        <label for="request_reason" class="form-label">Reason for Request <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="request_reason" name="request_reason" rows="3" placeholder="Explain why you need this asset" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="request_date_needed" class="form-label">Date Needed <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="request_date_needed" name="request_date_needed" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Archive Modal Handlers
        document.querySelectorAll('.archive-btn').forEach(button => {
            button.addEventListener('click', function() {
                const inventoryId = this.getAttribute('data-id');
                const inventoryName = this.getAttribute('data-name');
    
                document.getElementById('inventoryName').textContent = inventoryName;
                document.getElementById('archiveInventoryForm').action = `/inventory/archive-item/${inventoryId}`;
            });
        });
        
        // Transfer Modal Handlers
        document.querySelectorAll('.transfer-btn').forEach(button => {
            button.addEventListener('click', function() {
                const inventoryId = this.getAttribute('data-id');
                const inventoryName = this.getAttribute('data-name');
    
                document.getElementById('transferAssetName').textContent = inventoryName;
                document.getElementById('transferAssetForm').action = `/inventory/transfer/${inventoryId}`;
            });
        });
        
        // Request Modal Handlers
        document.querySelectorAll('.request-btn').forEach(button => {
            button.addEventListener('click', function() {
                const inventoryId = this.getAttribute('data-id');
                const inventoryName = this.getAttribute('data-name');
    
                document.getElementById('requestAssetName').textContent = inventoryName;
                document.getElementById('requestAssetForm').action = `/inventory/request/${inventoryId}`;
                
                // Set default date needed to tomorrow
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                document.getElementById('request_date_needed').valueAsDate = tomorrow;
            });
        });
        
        // Transfer Type Radio Button Handlers
        document.querySelectorAll('input[name="transfer_to_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Hide all containers
                document.getElementById('transfer_user_container').style.display = 'none';
                document.getElementById('transfer_department_container').style.display = 'none';
                document.getElementById('transfer_location_container').style.display = 'none';
                
                // Show the selected container
                if (this.value === 'user') {
                    document.getElementById('transfer_user_container').style.display = 'block';
                } else if (this.value === 'department') {
                    document.getElementById('transfer_department_container').style.display = 'block';
                } else if (this.value === 'location') {
                    document.getElementById('transfer_location_container').style.display = 'block';
                }
            });
        });
        
        // View Toggle Handlers
        const tableViewBtn = document.getElementById('table-view-btn');
        const gridViewBtn = document.getElementById('grid-view-btn');
        const tableView = document.getElementById('table-view');
        const gridView = document.getElementById('grid-view');
        
        if (tableViewBtn && gridViewBtn && tableView && gridView) {
            tableViewBtn.addEventListener('click', function() {
                tableView.style.display = 'block';
                gridView.style.display = 'none';
                tableViewBtn.classList.add('active');
                gridViewBtn.classList.remove('active');
                
                // Save preference in localStorage
                localStorage.setItem('inventoryViewMode', 'table');
            });
            
            gridViewBtn.addEventListener('click', function() {
                tableView.style.display = 'none';
                gridView.style.display = 'flex';
                gridViewBtn.classList.add('active');
                tableViewBtn.classList.remove('active');
                
                // Save preference in localStorage
                localStorage.setItem('inventoryViewMode', 'grid');
            });
            
            // Load saved preference
            const savedViewMode = localStorage.getItem('inventoryViewMode');
            if (savedViewMode === 'grid') {
                gridViewBtn.click();
            }
        }
    });
</script>

@endsection