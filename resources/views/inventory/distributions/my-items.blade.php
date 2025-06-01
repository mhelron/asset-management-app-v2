@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0">My Items</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Items</li>
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

                <!-- My Items List -->
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <h5 class="m-0 fw-bold">My Items</h5>
                    </div>
                    <div class="card-body">
                        @if($distributions->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <h4 class="mt-3">No Items Found</h4>
                                <p class="text-muted">You don't have any items assigned to you yet.</p>
                            </div>
                        @else
                            <div class="row">
                                @foreach($distributions as $distribution)
                                    <div class="col-md-4 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">{{ $distribution->inventory->item_name }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3 text-center">
                                                    @if($distribution->inventory->image_path)
                                                        <img src="{{ asset('storage/' . $distribution->inventory->image_path) }}" 
                                                            alt="Item Image" class="img-fluid rounded" style="max-height: 120px;">
                                                    @else
                                                        <i class="bi bi-box" style="font-size: 3rem;"></i>
                                                    @endif
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <p><strong>Category:</strong> {{ $distribution->inventory->category->category ?? 'N/A' }}</p>
                                                    <p><strong>Type:</strong> {{ $distribution->inventory->assetType->name ?? 'N/A' }}</p>
                                                    <p><strong>Assigned Quantity:</strong> {{ $distribution->quantity_assigned }}</p>
                                                    <p><strong>Remaining Quantity:</strong> {{ $distribution->quantity_remaining }}</p>
                                                    <p><strong>Assigned Date:</strong> {{ $distribution->created_at->format('M d, Y') }}</p>
                                                </div>

                                                @if($distribution->quantity_remaining > 0)
                                                    <button type="button" class="btn btn-primary w-100" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#useItemModal"
                                                        data-id="{{ $distribution->id }}"
                                                        data-name="{{ $distribution->inventory->item_name }}"
                                                        data-remaining="{{ $distribution->quantity_remaining }}">
                                                        <i class="bi bi-check-circle me-2"></i>Mark as Consumed
                                                    </button>
                                                @else
                                                    <button class="btn btn-secondary w-100" disabled>
                                                        <i class="bi bi-check-circle-fill me-2"></i>Fully Used
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
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
    });
</script>
@endsection 