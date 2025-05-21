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
    
    /* Custom Pagination Styles */
    .custom-pagination {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-top: 1.5rem;
        gap: 8px;
    }
    .page-item {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border-radius: 4px;
        font-weight: 500;
        text-decoration: none;
        background-color: #fff;
        color: #333;
        border: 1px solid #e3e6f0;
        transition: all 0.2s ease;
    }
    .page-link:hover {
        background-color: #f8f9fc;
        color: #000;
        z-index: 2;
    }
    .page-item.active .page-link {
        background-color: #212529;
        color: white;
        border-color: #212529;
    }
    .page-item.disabled .page-link {
        color: #c2c7d0;
        pointer-events: none;
        background-color: #fff;
        border-color: #e9ecef;
    }
    .page-ellipsis {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
    }
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-bold">Users</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
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
                
                <!-- Search and Filters Row -->
                <div class="card shadow mb-3">
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Search Input -->
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white"><i class="bi bi-search"></i></span>
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search by name, email or role..." value="{{ $search ?? '' }}">
                                </div>
                            </div>
                            
                            <!-- Role Filter -->
                            <div class="col-md-2">
                                <select id="roleFilter" class="form-select">
                                    <option value="">All Roles</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ isset($role_filter) && $role_filter == $role ? 'selected' : '' }}>{{ $role }}</option>
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
                            
                            <!-- Date Added Filter -->
                            <div class="col-md-2">
                                <select id="dateFilter" class="form-select">
                                    <option value="">All Dates</option>
                                    <option value="today" {{ isset($date_filter) && $date_filter == 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="this_week" {{ isset($date_filter) && $date_filter == 'this_week' ? 'selected' : '' }}>This Week</option>
                                    <option value="this_month" {{ isset($date_filter) && $date_filter == 'this_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="this_year" {{ isset($date_filter) && $date_filter == 'this_year' ? 'selected' : '' }}>This Year</option>
                                </select>
                            </div>
                            
                            <!-- Add User Button -->
                            <div class="col-md-2 d-flex justify-content-end">
                                <a href="{{ route('users.create') }}" class="btn btn-dark w-100"><i class="bi bi-plus-lg me-2"></i>Add User</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Table Card -->
                <div class="card shadow">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold">Users List</h5>
                        <div id="resultsSummary" class="text-muted">
                            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
                        </div>
                    </div>
                    <div class="card-body" id="usersTableContainer">
                        <!-- Initial content will be replaced by AJAX -->
                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="usersTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Department</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $index => $user)
                                    <tr>
                                        <td>{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
                                        <td>{{ $user->first_name }}</td>
                                        <td>{{ $user->last_name }}</td>
                                        <td>{{ $user->department->name ?? 'N/A' }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><span class="badge bg-dark">{{ $user->user_role }}</span></td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('users.view', ['id' => $user->id]) }}" class="btn btn-sm btn-dark me-2"><i class="bi bi-eye"></i></a>

                                                <a href="{{ route('users.edit', ['id' => $user->id]) }}" class="btn btn-sm btn-success me-2"><i class="bi bi-pencil-square"></i></a>
  
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#archiveModal" 
                                                    data-id="{{ $user->id }}" data-name="{{ $user->first_name }} {{ $user->last_name }}">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No user found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Custom Pagination -->
                        <div class="custom-pagination" id="paginationLinks">
                            <!-- Previous Page Link -->
                            @if ($users->onFirstPage())
                                <div class="page-item disabled">
                                    <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                </div>
                            @else
                                <div class="page-item">
                                    <a class="page-link" href="{{ $users->previousPageUrl() }}" rel="prev"><i class="bi bi-chevron-left"></i></a>
                                </div>
                            @endif

                            <!-- Pagination Elements -->
                            @php
                                $currentPage = $users->currentPage();
                                $lastPage = $users->lastPage();
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $startPage + 4);
                                
                                if ($endPage - $startPage < 4 && $startPage > 1) {
                                    $startPage = max(1, $endPage - 4);
                                }
                            @endphp

                            <!-- First Page + Ellipsis -->
                            @if ($startPage > 1)
                                <div class="page-item">
                                    <a class="page-link" href="{{ $users->url(1) }}">1</a>
                                </div>
                                @if ($startPage > 2)
                                    <div class="page-ellipsis">...</div>
                                @endif
                            @endif

                            <!-- Page Range -->
                            @for ($i = $startPage; $i <= $endPage; $i++)
                                <div class="page-item {{ $i == $users->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $users->url($i) }}">{{ $i }}</a>
                                </div>
                            @endfor

                            <!-- Last Page + Ellipsis -->
                            @if ($endPage < $lastPage)
                                @if ($endPage < $lastPage - 1)
                                    <div class="page-ellipsis">...</div>
                                @endif
                                <div class="page-item">
                                    <a class="page-link" href="{{ $users->url($lastPage) }}">{{ $lastPage }}</a>
                                </div>
                            @endif

                            <!-- Next Page Link -->
                            @if ($users->hasMorePages())
                                <div class="page-item">
                                    <a class="page-link" href="{{ $users->nextPageUrl() }}" rel="next"><i class="bi bi-chevron-right"></i></a>
                                </div>
                            @else
                                <div class="page-item disabled">
                                    <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                                </div>
                            @endif
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
                Are you sure you want to archive <strong id="userName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="archiveForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Archive</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Live Search and Filters -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Archive Modal Handlers
        const archiveButtons = document.querySelectorAll('[data-bs-target="#archiveModal"]');
        const userNameField = document.getElementById('userName');
        const archiveForm = document.getElementById('archiveForm');

        archiveButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userName = this.getAttribute('data-name');
                const userId = this.getAttribute('data-id');
                userNameField.textContent = userName;

                // Fix URL construction
                archiveForm.action = "{{ url('users/archive-user') }}/" + userId;
            });
        });
        
        // Search & Filter Elements
        const searchInput = document.getElementById('searchInput');
        const roleFilter = document.getElementById('roleFilter');
        const departmentFilter = document.getElementById('departmentFilter');
        const dateFilter = document.getElementById('dateFilter');
        const tableContainer = document.getElementById('usersTableContainer');
        const paginationLinks = document.getElementById('paginationLinks');
        
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
        let lastSearch = '';
        let lastRole = '';
        let lastDepartment = '';
        let lastDate = '';
        
        // Function to create custom pagination
        function createCustomPagination(data) {
            const currentPage = data.users.current_page;
            const lastPage = data.users.last_page;
            
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
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="usersTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Department</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>`;
            
            if (data.users.data.length > 0) {
                data.users.data.forEach((user, index) => {
                    const startIndex = (data.users.current_page - 1) * data.users.per_page;
                    tableHtml += `
                        <tr>
                            <td>${startIndex + index + 1}</td>
                            <td>${user.first_name}</td>
                            <td>${user.last_name}</td>
                            <td>${user.department ? user.department.name : 'N/A'}</td>
                            <td>${user.email}</td>
                            <td><span class="badge bg-dark">${user.user_role}</span></td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ url('users/profile') }}/${user.id}" class="btn btn-sm btn-dark me-2"><i class="bi bi-eye"></i></a>
                                    <a href="{{ url('users/edit-user') }}/${user.id}" class="btn btn-sm btn-success me-2"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-sm btn-secondary archive-btn" data-bs-toggle="modal" data-bs-target="#archiveModal" 
                                        data-id="${user.id}" data-name="${user.first_name} ${user.last_name}">
                                        <i class="bi bi-archive"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                });
            } else {
                tableHtml += `<tr><td colspan="7" class="text-center">No user found</td></tr>`;
            }
            
            tableHtml += `</tbody></table></div>`;
                    
            // Create custom pagination
            const paginationHtml = createCustomPagination(data);
                
            // Create results summary
            const resultsSummaryHtml = `
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold">Users List</h5>
                    <div id="resultsSummary" class="text-muted">
                        Showing ${data.users.from || 0} to ${data.users.to || 0} of ${data.users.total} results
                    </div>
                </div>`;
                    
            // Update DOM
            tableContainer.innerHTML = tableHtml + paginationHtml;
            // Add header before the table container element
            const existingHeader = tableContainer.parentNode.querySelector('.card-header');
            if (existingHeader) {
                existingHeader.remove();
            }
            tableContainer.parentNode.insertBefore(document.createRange().createContextualFragment(resultsSummaryHtml), tableContainer);
                    
            // Reattach event listeners for archive buttons
            document.querySelectorAll('.archive-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userName = this.getAttribute('data-name');
                    const userId = this.getAttribute('data-id');
                    userNameField.textContent = userName;
                    archiveForm.action = "{{ url('users/archive-user') }}/" + userId;
                });
            });
                    
            // Reattach event listeners for pagination buttons
            document.querySelectorAll('.custom-pagination .page-link[data-page]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const pageParam = this.getAttribute('data-page');
                    if (!pageParam) return;
                    
                    // Create a new URL for the AJAX request
                    const ajaxUrl = new URL("{{ route('users.data') }}");
                    
                    // Add all current filters
                    if (lastSearch) ajaxUrl.searchParams.append('search', lastSearch);
                    if (lastRole) ajaxUrl.searchParams.append('role', lastRole);
                    if (lastDepartment) ajaxUrl.searchParams.append('department', lastDepartment);
                    if (lastDate) ajaxUrl.searchParams.append('date_added', lastDate);
                    
                    // Add page parameter
                    ajaxUrl.searchParams.append('page', pageParam);
                    
                    fetch(ajaxUrl)
                        .then(response => response.json())
                        .then(data => {
                            // Update table and pagination
                            updateTableContent(data);
                            
                            // Update history state without changing URL
                            const currentUrl = new URL(window.location.href);
                            const stateObj = { page: pageParam };
                            window.history.pushState(stateObj, '', currentUrl.pathname);
                        })
                        .catch(error => {
                            console.error('Error fetching page:', error);
                            tableContainer.innerHTML = '<div class="alert alert-danger">An error occurred while fetching users. Please try again.</div>';
                        });
                });
            });
        }
        
        // Function to fetch filtered users
        function fetchUsers(resetPage = false) {
            // Show loading state
            tableContainer.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading users...</p></div>';
            
            // Prepare URL with filters
            const url = new URL("{{ route('users.data') }}");
            const search = searchInput.value.trim();
            const role = roleFilter.value;
            const department = departmentFilter.value;
            const date = dateFilter.value;
            
            // Check if filters have changed
            const filtersChanged = search !== lastSearch || 
                                  role !== lastRole || 
                                  department !== lastDepartment || 
                                  date !== lastDate;
            
            // Update last filter values
            lastSearch = search;
            lastRole = role;
            lastDepartment = department;
            lastDate = date;
            
            // Reset to page 1 if filters changed
            if (filtersChanged || resetPage) {
                url.searchParams.append('reset_pagination', '1');
            }
            
            if (search) url.searchParams.append('search', search);
            if (role) url.searchParams.append('role', role);
            if (department) url.searchParams.append('department', department);
            if (date) url.searchParams.append('date_added', date);
            
            // Fetch data
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    updateTableContent(data);
                    
                    // When filters change, update URL to reflect page 1
                    if (filtersChanged || resetPage) {
                        const currentUrl = new URL(window.location.href);
                        const stateObj = { page: 1 };
                        window.history.pushState(stateObj, '', currentUrl.pathname);
                    }
                })
                .catch(error => {
                    console.error('Error fetching users:', error);
                    tableContainer.innerHTML = '<div class="alert alert-danger">An error occurred while fetching users. Please try again.</div>';
                });
        }
        
        // Debounced version of fetchUsers for search input
        const debouncedFetchUsers = debounce(fetchUsers, 300);
        
        // Event listeners - reset pagination when filters change
        searchInput.addEventListener('input', () => debouncedFetchUsers(true));
        roleFilter.addEventListener('change', () => fetchUsers(true));
        departmentFilter.addEventListener('change', () => fetchUsers(true));
        dateFilter.addEventListener('change', () => fetchUsers(true));
        
        // Load initial values to ensure everything is in sync
        lastSearch = searchInput.value.trim();
        lastRole = roleFilter.value;
        lastDepartment = departmentFilter.value;
        lastDate = dateFilter.value;
        
        // Initial pagination event listeners
        document.querySelectorAll('#paginationLinks a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                // Extract page number from URL
                const url = new URL(this.href);
                const pageParam = url.searchParams.get('page');
                if (!pageParam) return;
                
                // Create AJAX request with filters
                const ajaxUrl = new URL("{{ route('users.data') }}");
                if (lastSearch) ajaxUrl.searchParams.append('search', lastSearch);
                if (lastRole) ajaxUrl.searchParams.append('role', lastRole);
                if (lastDepartment) ajaxUrl.searchParams.append('department', lastDepartment);
                if (lastDate) ajaxUrl.searchParams.append('date_added', lastDate);
                ajaxUrl.searchParams.append('page', pageParam);
                
                // Fetch data
                fetch(ajaxUrl)
                    .then(response => response.json())
                    .then(data => {
                        updateTableContent(data);
                        
                        // Update URL without reload
                        const currentUrl = new URL(window.location.href);
                        const stateObj = { page: pageParam };
                        window.history.pushState(stateObj, '', currentUrl.pathname);
                    })
                    .catch(error => {
                        console.error('Error fetching page:', error);
                        tableContainer.innerHTML = '<div class="alert alert-danger">An error occurred while fetching users. Please try again.</div>';
                    });
            });
        });
    });
</script>

@endsection