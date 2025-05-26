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
                
                <!-- Search and Filters Row -->
                <div class="card shadow mb-3">
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Search Input -->
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white"><i class="bi bi-search"></i></span>
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search by name, serial or tag..." value="{{ $search ?? '' }}">
                                </div>
                            </div>
                            
                            <!-- Category Filter -->
                            <div class="col-md-2">
                                <select id="categoryFilter" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ isset($category_filter) && $category_filter == $category->id ? 'selected' : '' }}>{{ $category->category }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Type Filter -->
                            <div class="col-md-2">
                                <select id="typeFilter" class="form-select">
                                    <option value="">All Types</option>
                                    @foreach($assetTypes as $type)
                                        <option value="{{ $type->id }}" {{ isset($type_filter) && $type_filter == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Department Filter -->
                            <div class="col-md-2">
                                <select id="departmentFilter" class="form-select">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ isset($department_filter) && $department_filter == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Owner Filter -->
                            <div class="col-md-2">
                                <select id="ownerFilter" class="form-select">
                                    <option value="">All Owners</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ isset($owner_filter) && $owner_filter == $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Date Added Filter -->
                            <div class="col-md-1">
                                <select id="dateFilter" class="form-select">
                                    <option value="">All Dates</option>
                                    <option value="today" {{ isset($date_filter) && $date_filter == 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="this_week" {{ isset($date_filter) && $date_filter == 'this_week' ? 'selected' : '' }}>This Week</option>
                                    <option value="this_month" {{ isset($date_filter) && $date_filter == 'this_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="this_year" {{ isset($date_filter) && $date_filter == 'this_year' ? 'selected' : '' }}>This Year</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
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
                        <div id="resultsSummary" class="text-muted">
                            Showing {{ count($inventory) }} result(s)
                        </div>
                    </div>
                    <div class="card-body" id="inventoryTableContainer">
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
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 60px;">
                                                    <i class="bi bi-box-seam text-secondary"></i>
                                                </div>
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
                        
                        <!-- Custom Pagination (Will be implemented in JavaScript for AJAX) -->
                        <div class="custom-pagination" id="paginationLinks">
                            <!-- Pagination will be dynamically added here -->
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
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded mx-auto mb-3" style="width: 120px; height: 120px;">
                                                <i class="bi bi-box-seam fs-1 text-secondary"></i>
                                            </div>
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
                console.log('Switching to table view');
                tableView.style.display = 'block';
                gridView.style.display = 'none';
                tableViewBtn.classList.add('active');
                gridViewBtn.classList.remove('active');
                
                // Save preference in localStorage
                try {
                    localStorage.setItem('inventoryViewMode', 'table');
                } catch (e) {
                    console.warn('Could not save view preference:', e);
                }
            });
            
            gridViewBtn.addEventListener('click', function() {
                console.log('Switching to grid view');
                tableView.style.display = 'none';
                gridView.style.display = 'flex';
                gridViewBtn.classList.add('active');
                tableViewBtn.classList.remove('active');
                
                // Save preference in localStorage
                try {
                    localStorage.setItem('inventoryViewMode', 'grid');
                } catch (e) {
                    console.warn('Could not save view preference:', e);
                }
            });
            
            // Load saved preference
            try {
                const savedViewMode = localStorage.getItem('inventoryViewMode');
                if (savedViewMode === 'grid') {
                    gridViewBtn.click();
                }
            } catch (e) {
                console.warn('Could not access saved view preference:', e);
            }
        }
    });
</script>

<!-- JavaScript for Live Search and Filters -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Search & Filter Elements
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const typeFilter = document.getElementById('typeFilter');
        const departmentFilter = document.getElementById('departmentFilter');
        const ownerFilter = document.getElementById('ownerFilter');
        const dateFilter = document.getElementById('dateFilter');
        const tableContainer = document.getElementById('inventoryTableContainer');
        const resultsSummary = document.getElementById('resultsSummary');
        
        // Debounce function to limit API calls
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // Keep track of last filter values to detect changes
        let lastSearch = searchInput ? searchInput.value : '';
        let lastCategory = categoryFilter ? categoryFilter.value : '';
        let lastType = typeFilter ? typeFilter.value : '';
        let lastDepartment = departmentFilter ? departmentFilter.value : '';
        let lastOwner = ownerFilter ? ownerFilter.value : '';
        let lastDate = dateFilter ? dateFilter.value : '';
        
        // Function to create custom pagination
        function createCustomPagination(data) {
            const currentPage = data.inventory.current_page;
            const lastPage = data.inventory.last_page;
            
            let paginationHtml = '<div class="custom-pagination">';
            
            // Previous button
            if (currentPage === 1) {
                paginationHtml += `
                    <div class="page-item disabled">
                        <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                    </div>`;
            } else {
                paginationHtml += `
                    <div class="page-item">
                        <a class="page-link" href="#" data-page="${currentPage - 1}"><i class="bi bi-chevron-left"></i></a>
                    </div>`;
            }
            
            // Calculate start and end page
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(lastPage, startPage + 4);
            
            // Adjust start page if we're near the end
            if (endPage - startPage < 4 && startPage > 1) {
                startPage = Math.max(1, endPage - 4);
            }
            
            // First page and ellipsis if needed
            if (startPage > 1) {
                paginationHtml += `
                    <div class="page-item">
                        <a class="page-link" href="#" data-page="1">1</a>
                    </div>`;
                if (startPage > 2) {
                    paginationHtml += `<div class="page-ellipsis">...</div>`;
                }
            }
            
            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                if (i === currentPage) {
                    paginationHtml += `
                        <div class="page-item active">
                            <span class="page-link">${i}</span>
                        </div>`;
                } else {
                    paginationHtml += `
                        <div class="page-item">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </div>`;
                }
            }
            
            // Last page and ellipsis if needed
            if (endPage < lastPage) {
                if (endPage < lastPage - 1) {
                    paginationHtml += `<div class="page-ellipsis">...</div>`;
                }
                paginationHtml += `
                    <div class="page-item">
                        <a class="page-link" href="#" data-page="${lastPage}">${lastPage}</a>
                    </div>`;
            }
            
            // Next button
            if (currentPage === lastPage) {
                paginationHtml += `
                    <div class="page-item disabled">
                        <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                    </div>`;
            } else {
                paginationHtml += `
                    <div class="page-item">
                        <a class="page-link" href="#" data-page="${currentPage + 1}"><i class="bi bi-chevron-right"></i></a>
                    </div>`;
            }
            
            paginationHtml += '</div>';
            
            return paginationHtml;
        }
        
        // Function to update the table content
        function updateTableContent(data) {
            // Create table HTML
            let tableHtml = `
                <div class="table-responsive" id="table-view">
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
                        <tbody>`;
            
            if (data.inventory.data.length > 0) {
                data.inventory.data.forEach((item, index) => {
                    const startIndex = (data.inventory.current_page - 1) * data.inventory.per_page;
                    tableHtml += `
                        <tr class="align-middle">
                            <td>${startIndex + index + 1}</td>
                            <td><strong><a href="/inventory/show-inventory/${item.id}" class="text-decoration-none">${item.item_name}</a></strong></td>
                            <td>
                                ${item.image_path 
                                    ? `<img src="/storage/${item.image_path}" alt="Asset Image" width="60" height="60" style="object-fit: cover;" class="rounded">`
                                    : `<div class="bg-light d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 60px;">
                                        <i class="bi bi-box-seam text-secondary"></i>
                                    </div>`}
                            </td>
                            <td>${item.asset_type ? item.asset_type.name : 'N/A'}</td>
                            <td>${item.category ? item.category.category : 'N/A'}</td>
                            <td>${item.user ? `${item.user.first_name} ${item.user.last_name}` : 'Unassigned'}</td>
                            <td>${item.department ? item.department.name : 'Unassigned'}</td>
                            <td>${item.model_no}</td>
                            <td>${item.serial_no}</td>
                            <td><span class="badge bg-secondary">${item.asset_tag}</span></td>
                            <td>${new Date(item.date_purchased).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</td>
                            <td>${item.purchased_from}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="/inventory/show-inventory/${item.id}" class="btn btn-sm btn-dark me-1" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="/inventory/edit-inventory/${item.id}" class="btn btn-sm btn-success me-1" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-secondary archive-btn" 
                                        data-bs-toggle="modal" data-bs-target="#archiveModal" 
                                        data-id="${item.id}" data-name="${item.item_name}" title="Archive">
                                        <i class="bi bi-archive"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                });
            } else {
                tableHtml += `
                    <tr>
                        <td colspan="13" class="text-center py-4">
                            <div class="mb-3 text-muted">
                                <i class="bi bi-inbox-fill fs-2"></i>
                                <p class="mt-2">No assets found in your inventory</p>
                            </div>
                            <a href="/inventory/create" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-2"></i>Add Your First Asset
                            </a>
                        </td>
                    </tr>`;
            }
            
            tableHtml += `</tbody></table></div>`;
            
            // Create grid view HTML
            let gridHtml = `<div id="grid-view" class="row" style="display: none;">`;
            
            if (data.inventory.data.length > 0) {
                data.inventory.data.forEach(item => {
                    gridHtml += `
                        <div class="col-md-4 col-lg-3 mb-4">
                            <div class="card h-100 asset-card shadow-sm">
                                <div class="card-body text-center">
                                    ${item.image_path 
                                        ? `<img src="/storage/${item.image_path}" alt="Asset Image" class="img-fluid rounded mb-3" style="height: 120px; object-fit: contain;">`
                                        : `<div class="bg-light d-flex align-items-center justify-content-center rounded mx-auto mb-3" style="width: 120px; height: 120px;">
                                            <i class="bi bi-box-seam fs-1 text-secondary"></i>
                                        </div>`}
                                    <h5 class="card-title">
                                        <a href="/inventory/show-inventory/${item.id}" class="text-decoration-none">${item.item_name}</a>
                                    </h5>
                                    <p class="card-text text-muted small mb-2">${item.category ? item.category.category : 'N/A'}</p>
                                    <p class="card-text small mb-1"><strong>Tag:</strong> ${item.asset_tag}</p>
                                    <p class="card-text small mb-1"><strong>SN:</strong> ${item.serial_no}</p>
                                    <p class="card-text small"><strong>Location:</strong> ${item.department ? item.department.name : 'Unassigned'}</p>
                                </div>
                                <div class="card-footer bg-transparent border-top-0 d-flex justify-content-center">
                                    <a href="/inventory/show-inventory/${item.id}" class="btn btn-sm btn-dark me-2">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="/inventory/edit-inventory/${item.id}" class="btn btn-sm btn-success me-2">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-secondary archive-btn" 
                                        data-bs-toggle="modal" data-bs-target="#archiveModal" 
                                        data-id="${item.id}" data-name="${item.item_name}">
                                        <i class="bi bi-archive"></i>
                                    </button>
                                </div>
                            </div>
                        </div>`;
                });
            } else {
                gridHtml += `
                    <div class="col-12">
                        <div class="alert alert-info text-center py-5">
                            <i class="bi bi-inbox-fill fs-2 d-block mb-3"></i>
                            <p>No assets found in your inventory</p>
                            <a href="/inventory/create" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-lg me-2"></i>Add Your First Asset
                            </a>
                        </div>
                    </div>`;
            }
            
            gridHtml += `</div>`;
            
            // Create custom pagination
            const paginationHtml = createCustomPagination(data);
            
            // Update DOM
            tableContainer.innerHTML = tableHtml + gridHtml + paginationHtml;
            
            // Update results summary
            resultsSummary.textContent = `Showing ${data.inventory.from || 0} to ${data.inventory.to || 0} of ${data.inventory.total} results`;
            
            // Reattach event listeners for archive buttons
            document.querySelectorAll('.archive-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const inventoryId = this.getAttribute('data-id');
                    const inventoryName = this.getAttribute('data-name');
                    
                    document.getElementById('inventoryName').textContent = inventoryName;
                    document.getElementById('archiveInventoryForm').action = `/inventory/archive-item/${inventoryId}`;
                });
            });
            
            // Reattach view toggle event listeners
            const tableViewBtn = document.getElementById('table-view-btn');
            const gridViewBtn = document.getElementById('grid-view-btn');
            const tableView = document.getElementById('table-view');
            const gridView = document.getElementById('grid-view');
            
            if (tableViewBtn && gridViewBtn && tableView && gridView) {
                tableViewBtn.addEventListener('click', function() {
                    console.log('Switching to table view');
                    tableView.style.display = 'block';
                    gridView.style.display = 'none';
                    tableViewBtn.classList.add('active');
                    gridViewBtn.classList.remove('active');
                    
                    // Save preference in localStorage
                    try {
                        localStorage.setItem('inventoryViewMode', 'table');
                    } catch (e) {
                        console.warn('Could not save view preference:', e);
                    }
                });
                
                gridViewBtn.addEventListener('click', function() {
                    console.log('Switching to grid view');
                    tableView.style.display = 'none';
                    gridView.style.display = 'flex';
                    gridViewBtn.classList.add('active');
                    tableViewBtn.classList.remove('active');
                    
                    // Save preference in localStorage
                    try {
                        localStorage.setItem('inventoryViewMode', 'grid');
                    } catch (e) {
                        console.warn('Could not save view preference:', e);
                    }
                });
                
                // Load saved preference
                try {
                    const savedViewMode = localStorage.getItem('inventoryViewMode');
                    if (savedViewMode === 'grid') {
                        gridViewBtn.click();
                    }
                } catch (e) {
                    console.warn('Could not access saved view preference:', e);
                }
            }
            
            // Reattach event listeners for pagination buttons
            document.querySelectorAll('.custom-pagination .page-link[data-page]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const pageParam = this.getAttribute('data-page');
                    if (!pageParam) return;
                    
                    // Create a new URL for the AJAX request
                    console.log('Pagination Route URL:', "{{ route('inventory.data') }}");
                    const ajaxUrl = new URL("/inventory/data", window.location.origin);
                    
                    // Add all current filters
                    if (lastSearch) ajaxUrl.searchParams.append('search', lastSearch);
                    if (lastCategory) ajaxUrl.searchParams.append('category', lastCategory);
                    if (lastType) ajaxUrl.searchParams.append('type', lastType);
                    if (lastDepartment) ajaxUrl.searchParams.append('department', lastDepartment);
                    if (lastOwner) ajaxUrl.searchParams.append('owner', lastOwner);
                    if (lastDate) ajaxUrl.searchParams.append('date_added', lastDate);
                    
                    // Add page parameter
                    ajaxUrl.searchParams.append('page', pageParam);
                    
                    fetch(ajaxUrl, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            console.log('Response status:', response.status);
                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Data received:', data);
                            if (data.error) {
                                throw new Error(data.error);
                            }
                            updateTableContent(data);
                        })
                        .catch(error => {
                            console.error('Error fetching inventory:', error);
                            tableContainer.innerHTML = `<div class="alert alert-danger">Error fetching inventory: ${error.message}</div>`;
                        });
                });
            });
        }
        
        // Function to fetch filtered inventory
        function fetchInventory(resetPage = false) {
            // Show loading state
            tableContainer.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading inventory...</p></div>';
            
            // Prepare URL with filters
            console.log('Route URL:', "{{ route('inventory.data') }}");
            // Use a direct URL for testing
            const url = new URL("/inventory/data", window.location.origin);
            const search = searchInput ? searchInput.value.trim() : '';
            const category = categoryFilter ? categoryFilter.value : '';
            const type = typeFilter ? typeFilter.value : '';
            const department = departmentFilter ? departmentFilter.value : '';
            const owner = ownerFilter ? ownerFilter.value : '';
            const date = dateFilter ? dateFilter.value : '';
            
            console.log('Filter values:', { 
                search, 
                category, 
                type, 
                department, 
                owner, 
                date 
            });
            
            // Check if filters have changed
            const filtersChanged = search !== lastSearch || 
                                  category !== lastCategory ||
                                  type !== lastType ||
                                  department !== lastDepartment ||
                                  owner !== lastOwner ||
                                  date !== lastDate;
            
            // Update last filter values
            lastSearch = search;
            lastCategory = category;
            lastType = type;
            lastDepartment = department;
            lastOwner = owner;
            lastDate = date;
            
            // Reset to page 1 if filters changed
            if (filtersChanged || resetPage) {
                url.searchParams.append('reset_pagination', '1');
            }
            
            if (search) url.searchParams.append('search', search);
            if (category) url.searchParams.append('category', category);
            if (type) url.searchParams.append('type', type);
            if (department) url.searchParams.append('department', department);
            if (owner) url.searchParams.append('owner', owner);
            if (date) url.searchParams.append('date_added', date);
            
            // Fetch data
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    updateTableContent(data);
                })
                .catch(error => {
                    console.error('Error fetching inventory:', error);
                    tableContainer.innerHTML = `<div class="alert alert-danger">Error fetching inventory: ${error.message}</div>`;
                });
        }
        
        // Debounced version of fetchInventory for search input
        const debouncedFetchInventory = debounce(fetchInventory, 300);
        
        // Event listeners - reset pagination when filters change
        if (searchInput) searchInput.addEventListener('input', () => debouncedFetchInventory(true));
        if (categoryFilter) categoryFilter.addEventListener('change', () => fetchInventory(true));
        if (typeFilter) typeFilter.addEventListener('change', () => fetchInventory(true));
        if (departmentFilter) departmentFilter.addEventListener('change', () => fetchInventory(true));
        if (ownerFilter) ownerFilter.addEventListener('change', () => fetchInventory(true));
        if (dateFilter) dateFilter.addEventListener('change', () => fetchInventory(true));
        
        // Initial load of data with AJAX
        fetchInventory();
    });
</script>

@endsection