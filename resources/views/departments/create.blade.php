@extends('layouts.app')

@section('content')

<!-- Content Header -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Add Department</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departments</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Department</li>
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
                    <a href="{{ route('departments.index') }}" class="btn btn-danger"><i class="bi bi-arrow-return-left me-2"></i>Back</a>
                </div>

                <!-- Add Department Form -->
                <div class="card">
                    <div class="card-body form-container">
                        <form action="{{ route('departments.store') }}" method="POST">
                            @csrf

                            <!-- Department Name -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Department Name <span class="text-danger"> *</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Enter department name">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Location <span class="text-danger"> *</span></label>
                                    <input type="text" name="location" value="{{ old('location') }}" class="form-control" placeholder="Enter department location">
                                    @error('location')
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

                            <!-- Submit button -->
                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-dark float-end"><i class="bi bi-plus-lg me-2"></i>Add Department</button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
