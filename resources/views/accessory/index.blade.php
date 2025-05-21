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
    .accessory-card {
        transition: all 0.3s ease;
    }
    .accessory-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    .view-toggle .btn {
        padding: 0.25rem 0.5rem;
    }
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-bold">Accessories</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Accessories</li>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="view-toggle btn-group">
                        <button type="button" class="btn btn-sm btn-outline-dark active" id="table-view-btn">
                            <i class="bi bi-table"></i> Table
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-dark" id="grid-view-btn">
                            <i class="bi bi-grid-3x3-gap"></i> Grid
                        </button>
                    </div>
                    <a href="{{ route('accessory.create') }}" class="btn btn-dark">
                        <i class="bi bi-plus-lg me-2"></i>Add Accessory
                    </a>
                </div>
                <!-- Accessories Card -->
                <div class="card shadow">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold">Accessories List</h5>
                    </div>
                    <div class="card-body">
                        <!-- Table View -->
                        <div id="table-view" class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Accessory Name</th>
                                        <th>Category</th>
                                        <th>Department</th>
                                        <th>Serial No</th>
                                        <th>Manufacturer</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1; @endphp
                                    @forelse ($accessory as $item)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $item->accessory_name }}</td>
                                        <td><span class="badge bg-dark">{{ $item->category }}</span></td>
                                        <td>{{ $item->department->name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-secondary">{{ $item->serial_no }}</span></td>
                                        <td>{{ $item->manufacturer }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('accessory.show', $item->id) }}" class="btn btn-sm btn-dark me-2">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('accessory.edit', $item->id) }}" class="btn btn-sm btn-success me-2">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-secondary archive-btn"
                                                    data-id="{{ $item->id }}" 
                                                    data-name="{{ $item->accessory_name }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#archiveAccessoryModal">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No accessories found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Grid View -->
                        <div id="grid-view" class="row" style="display: none;">
                            @forelse ($accessory as $item)
                            <div class="col-md-4 col-lg-3 mb-4">
                                <div class="card h-100 accessory-card shadow-sm">
                                    <div class="card-body text-center">
                                        <img src="{{ asset('images/accessory-default.png') }}" alt="Accessory" class="img-fluid rounded mb-3" width="80">
                                        <h5 class="card-title">
                                            <a href="{{ route('accessory.show', $item->id) }}" class="text-decoration-none">{{ $item->accessory_name }}</a>
                                        </h5>
                                        <p class="card-text small mb-1"><span class="badge bg-dark">{{ $item->category }}</span></p>
                                        <p class="card-text text-muted small mb-2">{{ $item->department->name ?? 'N/A' }}</p>
                                        <p class="card-text small"><strong>SN:</strong> {{ $item->serial_no }}</p>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0 d-flex justify-content-center">
                                        <a href="{{ route('accessory.show', $item->id) }}" class="btn btn-sm btn-dark me-2">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('accessory.edit', $item->id) }}" class="btn btn-sm btn-success me-2">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-secondary archive-btn"
                                            data-id="{{ $item->id }}" 
                                            data-name="{{ $item->accessory_name }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#archiveAccessoryModal">
                                            <i class="bi bi-archive"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>No accessories found
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
<div class="modal fade" id="archiveAccessoryModal" tabindex="-1" aria-labelledby="archiveAccessoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="archiveAccessoryModalLabel">Confirm Archive</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to archive <strong id="accessoryName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="archiveAccessoryForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Archive</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to Handle Archive Modal and View Toggle -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Archive Modal Handlers
    document.querySelectorAll('.archive-btn').forEach(button => {
        button.addEventListener('click', function() {
            const accessoryId = this.getAttribute('data-id');
            const accessoryName = this.getAttribute('data-name');
            document.getElementById('accessoryName').textContent = accessoryName;
            document.getElementById('archiveAccessoryForm').action = `/accessories/archive-accessory/${accessoryId}`;
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
            
            // Save preference in local storage
            localStorage.setItem('accessoriesViewPreference', 'table');
        });
        
        gridViewBtn.addEventListener('click', function() {
            gridView.style.display = 'flex';
            tableView.style.display = 'none';
            gridViewBtn.classList.add('active');
            tableViewBtn.classList.remove('active');
            
            // Save preference in local storage
            localStorage.setItem('accessoriesViewPreference', 'grid');
        });
        
        // Check local storage for saved preference
        const savedViewPreference = localStorage.getItem('accessoriesViewPreference');
        if (savedViewPreference === 'grid') {
            gridViewBtn.click();
        } else {
            tableViewBtn.click();
        }
    }
});
</script>
@endsection 