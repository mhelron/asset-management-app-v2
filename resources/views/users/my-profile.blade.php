@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp
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
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.35rem;
        background-color: white;
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
    .stat-card-icon {
        border-radius: 50%;
        padding: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
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
    .profile-info {
        padding: 1.25rem;
    }
    .profile-info .border-bottom {
        border-color: #e3e6f0 !important;
    }
</style>

<!-- Import jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-bold">My Profile</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container">
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

        <div class="row">
            <!-- Profile Information Card -->
            <div class="col-md-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i>User Information</h5>
                    </div>
                    <div class="card-body profile-info">
                        <div class="text-center mb-4">
                            <img src="{{ asset($user->profile_picture ?? 'images/default-user.jpg') }}" alt="Profile Picture" class="img-fluid rounded-circle shadow mb-3" width="120">
                            <h4 class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</h4>
                            <span class="badge bg-dark px-3 py-2">{{ $user->user_role }}</span>
                        </div>
                        <div class="d-flex flex-column">
                            <div class="mb-3 pb-3 border-bottom">
                                <span class="text-muted d-block">Email Address</span>
                                <span class="fw-bold">{{ $user->email }}</span>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <span class="text-muted d-block">Department</span>
                                <span class="fw-bold">{{ $user->department->name ?? 'N/A' }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted d-block">Member Since</span>
                                <span class="fw-bold">{{ $user->created_at->format('F d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asset Statistics Card -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2"></i>Asset Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                // Define a list of colors to use for the stats
                                $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                            @endphp
                            
                            @foreach($assetTypes as $index => $type)
                                @php
                                    $colorIndex = $index % count($colors);
                                    $color = $colors[$colorIndex];
                                    
                                    // Count assets of this type assigned to the user
                                    $typeCount = $user->assets->filter(function($asset) use ($type) {
                                        return $asset->asset_type_id == $type->id;
                                    })->count();
                                    
                                    // For Consumables type, add the count of distributed items
                                    if (strtolower($type->name) === 'consumables') {
                                        $typeCount = $typeCount + $user->activeItemDistributions->count();
                                    }
                                    
                                    // Get the first letter of the type name for the icon
                                    $firstLetter = strtoupper(substr($type->name, 0, 1));
                                @endphp
                                
                                <div class="col-md-6 mb-3">
                                    <div class="p-3 rounded-3 bg-light text-center border">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <div class="me-2 letter-icon bg-{{ $color }} bg-opacity-10 text-{{ $color }}" style="width: 40px; height: 40px; font-size: 1.3rem;">
                                                {{ $firstLetter }}
                                            </div>
                                            <h5 class="mb-0">{{ $type->name }}</h5>
                                        </div>
                                        <h2 class="mb-1">{{ $typeCount }}</h2>
                                        <p class="text-muted mb-0">Assigned to You</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Assigned Assets Tab Section -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-list-check me-2"></i>My Inventory</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <!-- Dynamic tabs based on asset types -->
                            @foreach($assetTypes as $index => $type)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3 {{ ($tab == $type->name || ($index == 0 && $tab == 'assets')) ? 'active' : '' }}" 
                                    id="{{ Str::slug($type->name) }}-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#{{ Str::slug($type->name) }}-tab-pane" 
                                    type="button" 
                                    role="tab" 
                                    aria-controls="{{ Str::slug($type->name) }}-tab-pane" 
                                    aria-selected="{{ $index == 0 ? 'true' : 'false' }}">{{ $type->name }}</button>
                            </li>
                            @endforeach
                        </ul>
                        <div class="tab-content mt-4" id="myTabContent">
                            <!-- Dynamic content tabs for each asset type -->
                            @foreach($assetTypes as $index => $type)
                            <div class="tab-pane fade {{ ($tab == $type->name || ($index == 0 && $tab == 'assets')) ? 'show active' : '' }}" 
                                id="{{ Str::slug($type->name) }}-tab-pane" 
                                role="tabpanel" 
                                aria-labelledby="{{ Str::slug($type->name) }}-tab" 
                                tabindex="0">
                                
                                @if(strtolower($type->name) === 'consumables')
                                    <div class="alert alert-info mb-3">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Consumable items</strong> are tracked with quantity and can be marked as used when consumed.
                                    </div>
                                    
                                    <!-- Distributed Items Table -->
                                    <h5 class="mb-3">Consumable Items</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Category</th>
                                                    <th>Type</th>
                                                    <th>Assigned Quantity</th>
                                                    <th>Remaining Quantity</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($user->activeItemDistributions as $distribution)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('inventory.show', $distribution->inventory->id) }}" class="text-decoration-none">
                                                            {{ $distribution->inventory->item_name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $distribution->inventory->category->category ?? 'N/A' }}</td>
                                                    <td>{{ $distribution->inventory->assetType->name ?? 'N/A' }}</td>
                                                    <td>{{ $distribution->quantity_assigned }}</td>
                                                    <td>
                                                        @if($distribution->isFullyUsed())
                                                            <span class="badge bg-secondary">Used (0)</span>
                                                        @elseif($distribution->isPartiallyUsed())
                                                            <span class="badge bg-warning">{{ $distribution->quantity_remaining }}</span>
                                                        @else
                                                            <span class="badge bg-success">{{ $distribution->quantity_remaining }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($distribution->quantity_remaining > 0)
                                                            <button type="button" class="btn btn-sm btn-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#useItemModal"
                                                                data-id="{{ $distribution->id }}"
                                                                data-name="{{ $distribution->inventory->item_name }}"
                                                                data-remaining="{{ $distribution->quantity_remaining }}">
                                                                <i class="bi bi-check-circle me-1"></i>Mark as Consumed
                                                            </button>
                                                        @else
                                                            <span class="badge bg-secondary px-3 py-2">Fully Used</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-3">No consumable items assigned yet</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                @elseif(strtolower($type->name) !== 'consumables')
                                    <!-- Fixed Assets Table -->
                                    <h5 class="mb-3">{{ $type->name }} Inventory</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Asset</th>
                                                    <th>Category</th>
                                                    <th>Serial Number</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $filteredAssets = $user->assets->filter(function($asset) use ($type) {
                                                        return $asset->asset_type_id == $type->id;
                                                    });
                                                @endphp
                                                
                                                @forelse($filteredAssets as $asset)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('inventory.show', $asset->id) }}" class="text-decoration-none">{{ $asset->item_name }}</a>
                                                    </td>
                                                    <td>{{ $asset->category->category ?? 'N/A' }}</td>
                                                    <td>{{ $asset->serial_no }}</td>
                                                    <td>
                                                        <span class="badge bg-success">Assigned</span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-3">No {{ $type->name }} assigned yet</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.update-my-profile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}">
                    </div>
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                        <small class="text-muted">Upload a square image for best results (JPG, PNG - max 2MB)</small>
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" class="form-control" value="{{ $user->department->name ?? 'N/A' }}" readonly>
                        <small class="text-muted">Department can only be changed by an administrator.</small>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                        <small class="text-muted">Password must contain at least 8 characters, including uppercase, number and special character.</small>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Use Item Modal -->
<div class="modal fade" id="useItemModal" tabindex="-1" aria-labelledby="useItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="useItemModalLabel">Consume Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="useItemForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p>Mark <strong id="itemName"></strong> as consumed/used:</p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        When you mark items as consumed, they are permanently removed from inventory and will not be returned to available stock.
                    </div>
                    
                    <div class="mb-3">
                        <label for="quantity_used" class="form-label">Quantity to Consume <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity_used" name="quantity_used" min="1" value="1" required>
                        <small class="text-muted">Maximum available: <span id="maxQuantity"></span></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Consumption</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fix for edit profile modal
        const editProfileBtn = document.querySelector('[data-bs-target="#editProfileModal"]');
        if (editProfileBtn) {
            editProfileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const modal = document.getElementById('editProfileModal');
                if (modal) {
                    // Using jQuery to ensure the modal works
                    $('#editProfileModal').modal('show');
                }
            });
        }
        
        // Handle use item modal
        const useItemModal = document.getElementById('useItemModal');
        if (useItemModal) {
            useItemModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const remaining = button.getAttribute('data-remaining');
                
                const itemNameElement = document.getElementById('itemName');
                const maxQuantityElement = document.getElementById('maxQuantity');
                const quantityInput = document.getElementById('quantity_used');
                const form = document.getElementById('useItemForm');
                
                itemNameElement.textContent = name;
                maxQuantityElement.textContent = remaining;
                quantityInput.max = remaining;
                quantityInput.value = 1;
                
                form.action = `/distributions/use-items/${id}`;
            });
        }
        
        // Check for tab parameter in URL
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        
        // Activate the Consumables tab if previously on 'items' tab
        if (tabParam === 'items') {
            const consumablesTab = document.querySelector('button[id$="consumables-tab"]');
            if (consumablesTab) {
                consumablesTab.click();
            }
        }
    });
</script>
@endsection 