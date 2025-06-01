@extends('layouts.app')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Request an Asset</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('asset-requests.my-requests') }}">My Requests</a></li>
                    <li class="breadcrumb-item active">New Request</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Alert Box for Errors-->
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-diamond me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow">
                    <div class="card-header bg-white">
                        <h5 class="card-title m-0 fw-bold">New Asset Request</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('asset-requests.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="inventory_id" class="form-label">Asset <span class="text-danger">*</span></label>
                                <select class="form-select @error('inventory_id') is-invalid @enderror" id="inventory_id" name="inventory_id" required>
                                    <option value="">Select an asset</option>
                                    @foreach($requestableAssets as $asset)
                                        <option value="{{ $asset->id }}" {{ old('inventory_id') == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->item_name }} 
                                            @if($asset->has_quantity)
                                                (Available: {{ $asset->quantity }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('inventory_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Select the asset you would like to request</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="date_needed" class="form-label">Date Needed <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_needed') is-invalid @enderror" id="date_needed" name="date_needed" value="{{ old('date_needed') }}" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                @error('date_needed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">When do you need this asset? (Must be a future date)</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="4" required>{{ old('reason') }}</textarea>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Please explain why you need this asset</div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('asset-requests.my-requests') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i> Submit Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 