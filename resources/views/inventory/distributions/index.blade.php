@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0">Distribute Item: {{ $item->item_name }}</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.show', $item->id) }}">{{ $item->item_name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Distributions</li>
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

                <div class="d-flex justify-content-end mb-2">
                    <a href="{{ route('inventory.show', $item->id) }}" class="btn btn-danger"><i class="bi bi-arrow-return-left me-2"></i>Back to Item</a>
                </div>

                <!-- Item Distribution Info Card -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-white">
                        <h5 class="m-0 fw-bold">Item Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <table class="table">
                                    <tr>
                                        <th>Item Name:</th>
                                        <td>{{ $item->item_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Category:</th>
                                        <td>{{ $item->category->category ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Type:</th>
                                        <td>{{ $item->assetType->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Quantity:</th>
                                        <td>{{ $item->quantity ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <th>Available Quantity:</th>
                                        <td>{{ $item->available_quantity }}</td>
                                    </tr>
                                    <tr>
                                        <th>Maximum Quantity:</th>
                                        <td>{{ $item->max_quantity ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Minimum Quantity:</th>
                                        <td>{{ $item->min_quantity ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4 text-center">
                                @if($item->image_path)
                                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="Item Image" class="img-fluid rounded" style="max-height: 200px;">
                                @else
                                    <div class="text-muted">
                                        <i class="bi bi-image" style="font-size: 5rem;"></i>
                                        <p>No image available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribute Item Form -->
                @if($item->available_quantity > 0)
                <div class="card shadow mb-4">
                    <div class="card-header bg-white">
                        <h5 class="m-0 fw-bold">Distribute Item</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('distributions.store', $item->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="user_id">Assign To <span class="text-danger">*</span></label>
                                        <select name="user_id" id="user_id" class="form-control" required>
                                            <option value="">Select a User</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" name="quantity" id="quantity" class="form-control" 
                                            min="1" max="{{ $item->available_quantity }}" value="1" required>
                                        <small class="text-muted">Maximum available: {{ $item->available_quantity }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="notes">Notes</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="1" placeholder="Optional notes about this distribution"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-box-arrow-right me-2"></i>Distribute Item
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    No quantity available for distribution.
                </div>
                @endif

                <!-- Distribution History -->
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <h5 class="m-0 fw-bold">Distribution History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Assigned To</th>
                                        <th>Assigned By</th>
                                        <th>Quantity Assigned</th>
                                        <th>Quantity Remaining</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($distributions as $distribution)
                                    <tr>
                                        <td>{{ $distribution->created_at->format('M d, Y h:i A') }}</td>
                                        <td>{{ $distribution->user->first_name }} {{ $distribution->user->last_name }}</td>
                                        <td>{{ $distribution->assigner->first_name }} {{ $distribution->assigner->last_name }}</td>
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
                                        <td>{{ $distribution->notes ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No distributions found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 