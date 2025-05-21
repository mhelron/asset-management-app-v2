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
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-bold">Components</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Components</li>
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
                    <a href="{{ route('components.create') }}" class="btn btn-dark">
                        <i class="bi bi-plus-lg me-2"></i>Add Component
                    </a>
                </div>
                <!-- Components Card -->
                <div class="card shadow">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold"><i class="bi bi-cpu me-2"></i>Components List</h5>
                    </div>
                    <div class="card-body">
                        <!-- Table View -->
                        <div id="table-view" class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Component Name</th>
                                        <th>Category</th>
                                        <th>Serial No</th>
                                        <th>Manufacturer</th>
                                        <th>Assigned To</th>
                                        <th>Associated Asset</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1; @endphp
                                    @forelse ($components as $component)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td><strong><a href="{{ route('components.show', $component) }}" class="text-decoration-none">{{ $component->component_name }}</a></strong></td>
                                        <td>{{ $component->category->category ?? 'N/A' }}</td>
                                        <td>{{ $component->serial_no }}</td>
                                        <td>{{ $component->manufacturer }}</td>
                                        <td>
                                            @if($component->user)
                                                <span class="badge bg-success">
                                                    {{ $component->user->first_name }} {{ $component->user->last_name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Not assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($component->inventory)
                                                <span class="badge bg-info">
                                                    {{ $component->inventory->item_name }} 
                                                    ({{ $component->inventory->asset_tag }})
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('components.show', $component) }}" class="btn btn-sm btn-dark me-2" title="View Component"><i class="bi bi-eye"></i></a>
                                                <a href="{{ route('components.edit', $component) }}" class="btn btn-sm btn-success me-2" title="Edit Component"><i class="bi bi-pencil-square"></i></a>
                                                <button type="button" class="btn btn-sm btn-secondary archive-btn"
                                                    data-id="{{ $component->id }}" 
                                                    data-name="{{ $component->component_name }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#archiveComponentModal"
                                                    title="Archive Component">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-3">No components found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Grid View -->
                        <div id="grid-view" class="row" style="display: none;">
                            @forelse($components as $component)
                            <div class="col-md-4 col-lg-3 mb-4">
                                <div class="card h-100 component-card shadow-sm">
                                    <div class="card-body text-center">
                                        <img src="{{ asset('images/component-default.png') }}" alt="Component" class="img-fluid rounded mb-3" width="80">
                                        <h5 class="card-title">
                                            <a href="{{ route('components.show', $component) }}" class="text-decoration-none">{{ $component->component_name }}</a>
                                        </h5>
                                        <p class="card-text text-muted small mb-2">{{ $component->category->category ?? 'N/A' }}</p>
                                        <p class="card-text small"><strong>SN:</strong> {{ $component->serial_no }}</p>
                                        <div class="mt-2">
                                            @if($component->user)
                                                <span class="badge bg-success">
                                                    {{ $component->user->first_name }} {{ $component->user->last_name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Not assigned</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0 d-flex justify-content-center">
                                        <a href="{{ route('components.show', $component) }}" class="btn btn-sm btn-dark me-2" title="View Component"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('components.edit', $component) }}" class="btn btn-sm btn-success me-2" title="Edit Component"><i class="bi bi-pencil-square"></i></a>
                                        <button type="button" class="btn btn-sm btn-secondary archive-btn"
                                            data-id="{{ $component->id }}" 
                                            data-name="{{ $component->component_name }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#archiveComponentModal"
                                            title="Archive Component">
                                            <i class="bi bi-archive"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>No components found
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
<div class="modal fade" id="archiveComponentModal" tabindex="-1" aria-labelledby="archiveComponentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="archiveComponentModalLabel">Confirm Archive</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to archive <strong id="componentName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="archiveComponentForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Archive</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for View Toggle and Archive Modal -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Archive Modal Handlers
    document.querySelectorAll('.archive-btn').forEach(button => {
        button.addEventListener('click', function() {
            const componentId = this.getAttribute('data-id');
            const componentName = this.getAttribute('data-name');
            document.getElementById('componentName').textContent = componentName;
            document.getElementById('archiveComponentForm').action = `/components/archive-component/${componentId}`;
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
            localStorage.setItem('componentsViewPreference', 'table');
        });
        
        gridViewBtn.addEventListener('click', function() {
            gridView.style.display = 'flex';
            tableView.style.display = 'none';
            gridViewBtn.classList.add('active');
            tableViewBtn.classList.remove('active');
            
            // Save preference in local storage
            localStorage.setItem('componentsViewPreference', 'grid');
        });
        
        // Check local storage for saved preference
        const savedViewPreference = localStorage.getItem('componentsViewPreference');
        if (savedViewPreference === 'grid') {
            gridViewBtn.click();
        } else {
            tableViewBtn.click();
        }
    }
});
</script>
@endsection 