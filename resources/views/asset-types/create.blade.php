@extends('layouts.app')

@section('content')

<!-- Content Header -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0">Add Asset Type</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('asset-types.index') }}">Asset Types</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Asset Type</li>
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

                <!-- Add Asset Type Form -->
                <div class="card">
                    <div class="card-body form-container">
                        <form action="{{ route('asset-types.store') }}" method="POST">
                            @csrf

                            <!-- Asset Type Name -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Asset Type Name <span class="text-danger"> *</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Enter asset type name">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Description <span class="text-danger"> *</span></label>
                                    <textarea name="desc" class="form-control" placeholder="Enter description">{{ old('desc') }}</textarea>
                                    @error('desc')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Requires QR Code -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="requires_qr_code" id="requires_qr_code" value="1" {{ old('requires_qr_code') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="requires_qr_code">
                                            Requires QR Code
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Check this option if assets of this type should have QR codes (e.g., fixed assets). Leave unchecked for consumables or other items that don't need tracking via QR codes.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Is Requestable -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_requestable" id="is_requestable" value="1" {{ old('is_requestable') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_requestable">
                                            Is Requestable
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Check this option if assets of this type can be requested by users. This will allow users to submit requests for these assets.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit button -->
                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-dark float-end"><i class="bi bi-plus-lg me-2"></i>Add Asset Type</button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection 