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
                    <a href="{{ route('inventory.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg me-2"></i>Add Asset</a>
                </div>

                <div class="card shadow">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold">Asset Inventory</h5>
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
                                        <th>Model No.</th>
                                        <th>Serial No.</th>
                                        <th>Asset Tag</th>
                                        <th>Purchase Date</th>
                                        <th>Purchased From</th>
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
                                        <td>{{ $item->category->type ?? 'N/A' }}</td>
                                        <td>{{ $item->category->category ?? 'N/A' }}</td>
                                        <td>{{ $item->user->first_name ?? '' }} {{ $item->user->last_name ?? 'Unassigned' }}</td>
                                        <td>{{ $item->department->name ?? 'Unassigned' }}</td>
                                        <td>{{ $item->model_no }}</td>
                                        <td>{{ $item->serial_no }}</td>
                                        <td><span class="badge bg-secondary">{{ $item->asset_tag }}</span></td>
                                        <td>{{ date('M d, Y', strtotime($item->date_purchased)) }}</td>
                                        <td>{{ $item->purchased_from }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('inventory.show', $item->id) }}" class="btn btn-sm btn-dark me-1" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-sm btn-success me-1" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-secondary archive-btn" 
                                                    data-bs-toggle="modal" data-bs-target="#archiveModal" 
                                                    data-id="{{ $item->id }}" data-name="{{ $item->item_name }}" title="Archive">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="13" class="text-center py-4">
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
                                        <p class="card-text text-muted small mb-2">{{ $item->category->category ?? 'N/A' }}</p>
                                        <p class="card-text small mb-1"><strong>Tag:</strong> {{ $item->asset_tag }}</p>
                                        <p class="card-text small mb-1"><strong>SN:</strong> {{ $item->serial_no }}</p>
                                        <p class="card-text small"><strong>Location:</strong> {{ $item->department->name ?? 'Unassigned' }}</p>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0 d-flex justify-content-center">
                                        <a href="{{ route('inventory.show', $item->id) }}" class="btn btn-sm btn-dark me-2">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-sm btn-success me-2">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-secondary archive-btn" 
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