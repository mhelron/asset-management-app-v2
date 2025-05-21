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
    .form-control:focus, .form-select:focus {
        border-color: #bac8f3;
        box-shadow: 0 0 0 0.15rem rgba(78, 115, 223, 0.15);
    }
    .required-label::after {
        content: " *";
        color: #e74a3b;
    }
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-bold">Add User</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add User</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('users.index') }}" class="btn btn-danger"><i class="bi bi-arrow-return-left me-2"></i>Back</a>
                </div>

                <!-- Add User Form -->
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <h5 class="m-0 fw-bold"><i class="bi bi-person-plus me-2"></i>Add New User</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf

                            @if($departments->count() == 0)
                            <div class="alert alert-warning alert-dismissible fade show mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                                    <div>
                                        <strong>Warning:</strong> You need to add a department before adding users. 
                                        <a href="{{ route('departments.create') }}" class="alert-link">Click here</a> to add a department.
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif
                            
                            <!-- Start of row -->
                            <div class="row">

                                <!-- First Name -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required-label">First Name</label>
                                        <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control @error('first_name') is-invalid @enderror" placeholder="Enter first name">
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required-label">Last Name</label>
                                        <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control @error('last_name') is-invalid @enderror" placeholder="Enter last name">
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required-label">Email</label>
                                        <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- User Role -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required-label">User Role</label>
                                        <select name="user_role" class="form-select @error('user_role') is-invalid @enderror">
                                            <option value="" disabled selected>Select a role</option>
                                            @if(session('user_role') != 'Admin')
                                                <option value="Super Admin" {{ old('user_role') == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                                            @endif
                                            <option value="Admin" {{ old('user_role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="Manager" {{ old('user_role') == 'Manager' ? 'selected' : '' }}>Manager</option>
                                            <option value="Staff" {{ old('user_role') == 'Staff' ? 'selected' : '' }}>Staff</option>
                                        </select>
                                        @error('user_role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Department -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required-label">Department</label>
                                        <select name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                                            <option value="">Select a Department</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required-label">Password</label>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password">
                                        <small class="form-text text-muted">Must be at least 8 characters with uppercase, number and special character</small>
                                        @if ($errors->has('password') && $errors->first('password') !== 'The password confirmation does not match.')
                                            <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required-label">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm password">
                                        @if (!$errors->has('password') && $errors->has('password_confirmation'))
                                            <div class="invalid-feedback">{{ $errors->first('password_confirmation') }}</div>
                                        @elseif ($errors->has('password') && $errors->first('password') === 'The password confirmation does not match.')
                                            <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            <!-- End of row -->

                            <!-- Submit button -->
                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-dark"><i class="bi bi-plus-lg me-2"></i>Add User</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection