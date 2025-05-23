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
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0 fw-bold">Categories</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Categories</li>
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

                <div class="d-flex justify-content-end mb-3 action-buttons">
                    <a href="{{ route('categories.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg me-2"></i>Add Category</a>
                </div>

                <!-- Category Filters -->
                <div class="card shadow mb-3">
                    <div class="card-header bg-white">
                        <h5 class="m-0 fw-bold">Filter Categories</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('categories.index') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white"><i class="bi bi-filter"></i></span>
                                    <select name="type" id="filter-type" class="form-select">
                                        <option value="">All Types</option>
                                        @foreach($categoryTypes as $type)
                                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" id="search" class="form-control" placeholder="Search category name..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-center">
                                <button type="submit" class="btn btn-dark me-2">Apply Filters</button>
                                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Categories Table -->
                <div class="card shadow">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold">Categories List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Category Name</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>

                                @php $i = 1; @endphp
                                @forelse ($categories as $item)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $item->category }}</td>
                                    <td>
                                        @if($item->type)
                                            <span class="badge 
                                                @if($item->type == 'Asset') bg-success 
                                                @elseif($item->type == 'Accessory') bg-info 
                                                @elseif($item->type == 'Component') bg-warning 
                                                @elseif($item->type == 'Consumable') bg-danger 
                                                @elseif($item->type == 'License') bg-secondary 
                                                @else text-muted @endif">
                                                {{ $item->type }}
                                            </span>
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($item->desc, 30) }}</td>
                                    <td><span class="badge {{ $item->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $item->status }}</span></td>

                                    <td>
                                        <div class="d-flex">
                                            <button type="button" class="btn btn-sm btn-dark me-2 view-details-btn"
                                                data-id="{{ $item->id }}" data-name="{{ $item->category }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <a href="{{ route('categories.edit', $item->id) }}" class="btn btn-sm btn-success me-2">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                    
                                            <button type="button" class="btn btn-sm btn-secondary archive-btn"
                                                data-id="{{ $item->id }}" data-name="{{ $item->category }}"
                                                data-bs-toggle="modal" data-bs-target="#archiveCategoryModal">
                                                <i class="bi bi-archive"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No category found</td>
                                </tr>
                                @endforelse

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="viewDetailsModalLabel">Custom Fields</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Field Name</th>
                                <th scope="col">Type</th>
                                <th scope="col">Required</th>
                            </tr>
                        </thead>
                        <tbody id="customFieldsTable">
                            <!-- Custom fields will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div class="modal fade" id="archiveCategoryModal" tabindex="-1" aria-labelledby="archiveCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="archiveCategoryModalLabel">Confirm Archive</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to archive <strong id="categoryName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="archiveCategoryForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Archive</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to Handle Archive Modal -->
<script>
   document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.archive-btn').forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-id');
            const categoryName = this.getAttribute('data-name');

            document.getElementById('categoryName').textContent = categoryName;
            document.getElementById('archiveCategoryForm').action = `/categories/archive-category/${categoryId}`;
        });
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.view-details-btn').forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-id');

            // Fetch custom fields via AJAX
            fetch(`/categories/get-custom-fields/${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    const customFieldsTable = document.getElementById('customFieldsTable');
                    customFieldsTable.innerHTML = ''; // Clear previous content

                    if (data.length > 0) {
                        data.forEach(field => {
                            let row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${field.name}</td>
                                <td><span class="badge bg-dark">${field.type}</span></td>
                                <td><span class="badge ${field.is_required ? 'bg-success' : 'bg-warning'}">${field.is_required ? 'Yes' : 'No'}</span></td>
                            `;
                            customFieldsTable.appendChild(row);
                        });
                    } else {
                        customFieldsTable.innerHTML = `
                            <tr>
                                <td colspan="3" class="text-center text-muted">No custom fields available</td>
                            </tr>
                        `;
                    }

                    // Show the modal
                    const viewModal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
                    viewModal.show();
                })
                .catch(error => {
                    console.error('Error fetching custom fields:', error);
                });
        });
    });
});
</script>

@endsection