@extends('layouts.app')
@section('content')
<style>
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    .card-header {
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.35rem;
        background-color: #fff;
    }
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }
    .form-group label {
        font-weight: 600;
        font-size: 0.9rem;
    }
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Add Accessory</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('accessory.index') }}">Accessories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Accessory</li>
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
                    <a href="{{ route('accessory.index') }}" class="btn btn-danger"><i class="bi bi-arrow-return-left me-2"></i>Back</a>
                </div>
                <!-- Add Accessory Form -->
                <div class="card">
                    <div class="card-body form-container">
                        <form action="{{ route('accessory.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <!-- First Column -->
                                <div class="col-md-6">
                                    <!-- Accessory Name -->
                                    <div class="form-group mb-3">
                                        <label>Accessory Name<span class="text-danger"> *</span></label>
                                        <input type="text" name="accessory_name" value="{{ old('accessory_name') }}" class="form-control" placeholder="Enter accessory name" required>
                                        @error('accessory_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Category -->
                                    <div class="form-group mb-3">
                                        <label>Category<span class="text-danger"> *</span></label>
                                        <select name="category" class="form-select" required>
                                            <option value="" selected disabled>Select Category</option>
                                            <option value="Keyboard" {{ old('category') == 'Keyboard' ? 'selected' : '' }}>Keyboard</option>
                                            <option value="Mouse" {{ old('category') == 'Mouse' ? 'selected' : '' }}>Mouse</option>
                                            <option value="Monitor" {{ old('category') == 'Monitor' ? 'selected' : '' }}>Monitor</option>
                                            <option value="Headset" {{ old('category') == 'Headset' ? 'selected' : '' }}>Headset</option>
                                            <option value="Printer" {{ old('category') == 'Printer' ? 'selected' : '' }}>Printer</option>
                                            <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('category')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Department -->
                                    <div class="form-group mb-3">
                                        <label>Department<span class="text-danger"> *</span></label>
                                        <select name="department_id" class="form-select" required>
                                            <option value="" selected disabled>Select Department</option>
                                            @foreach(\App\Models\Department::all() as $department)
                                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Serial No -->
                                    <div class="form-group mb-3">
                                        <label>Serial No<span class="text-danger"> *</span></label>
                                        <input type="text" name="serial_no" value="{{ old('serial_no') }}" class="form-control" placeholder="Enter serial number" required>
                                        @error('serial_no')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Model No -->
                                    <div class="form-group mb-3">
                                        <label>Model No<span class="text-danger"> *</span></label>
                                        <input type="text" name="model_no" value="{{ old('model_no') }}" class="form-control" placeholder="Enter model number" required>
                                        @error('model_no')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Second Column -->
                                <div class="col-md-6">
                                    <!-- Manufacturer -->
                                    <div class="form-group mb-3">
                                        <label>Manufacturer<span class="text-danger"> *</span></label>
                                        <input type="text" name="manufacturer" value="{{ old('manufacturer') }}" class="form-control" placeholder="Enter manufacturer" required>
                                        @error('manufacturer')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Assigned To -->
                                    <div class="form-group mb-3">
                                        <label>Assigned To</label>
                                        <select name="users_id" class="form-select">
                                            <option value="">Not Assigned</option>
                                            @foreach(\App\Models\User::all() as $user)
                                                <option value="{{ $user->id }}" {{ old('users_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->first_name }} {{ $user->last_name }} ({{ $user->department->name ?? 'No Department' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('users_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Date Purchased -->
                                    <div class="form-group mb-3">
                                        <label>Date Purchased<span class="text-danger"> *</span></label>
                                        <input type="date" name="date_purchased" value="{{ old('date_purchased') }}" class="form-control" required>
                                        @error('date_purchased')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Purchased From -->
                                    <div class="form-group mb-3">
                                        <label>Purchased From<span class="text-danger"> *</span></label>
                                        <input type="text" name="purchased_from" value="{{ old('purchased_from') }}" class="form-control" placeholder="Enter purchased from" required>
                                        @error('purchased_from')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Log Note -->
                                    <div class="form-group mb-3">
                                        <label>Log Note</label>
                                        <textarea name="log_note" class="form-control" rows="5" placeholder="Enter log note">{{ old('log_note') }}</textarea>
                                        @error('log_note')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submit button -->
                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-dark float-end"><i class="bi bi-plus-lg me-2"></i>Add Accessory</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection