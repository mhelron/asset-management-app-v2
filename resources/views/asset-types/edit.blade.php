@extends('layouts.app')

@section('content')

<!-- Content Header -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0">Edit Asset Type</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('asset-types.index') }}">Asset Types</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Asset Type</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-lg-12">

                <div class="d-flex justify-content-end mb-2">
                    <a href="{{ route('asset-types.index') }}" class="btn btn-danger"><i class="bi bi-arrow-return-left me-2"></i>Back</a>
                </div>

                <!-- Edit Asset Type Form -->
                <div class="card">
                    <div class="card-body form-container">
                        <form action="{{ route('asset-types.update', $assetType->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Asset Type Name -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Asset Type Name <span class="text-danger"> *</span></label>
                                    <input type="text" name="name" value="{{ old('name', $assetType->name) }}" class="form-control" placeholder="Enter asset type name">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Description <span class="text-danger"> *</span></label>
                                    <textarea name="desc" class="form-control" placeholder="Enter description">{{ old('desc', $assetType->desc) }}</textarea>
                                    @error('desc')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Status <span class="text-danger"> *</span></label>
                                    <select name="status" class="form-control">
                                        <option value="Active" {{ old('status', $assetType->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $assetType->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Requires QR Code -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="requires_qr_code" id="requires_qr_code" value="1" {{ old('requires_qr_code', $assetType->requires_qr_code) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="requires_qr_code">
                                            Requires QR Code
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Check this option if assets of this type should have QR codes (e.g., fixed assets). Leave unchecked for consumables or other items that don't need tracking via QR codes.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit button -->
                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-dark float-end"><i class="bi bi-pencil-square me-2"></i>Update Asset Type</button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection 