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
			<div class="col-md-6">
				<h1 class="m-0 fw-bold">Edit User</h1>
			</div>
			<div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit User</li>
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

				<!-- Edit User Form -->
				<div class="card shadow">
                    <div class="card-header bg-white">
                        <h5 class="m-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit User Information</h5>
                    </div>
					<div class="card-body p-4">
						<form action="{{ route('users.update', $user->id) }}" method="POST">
							@csrf
							@method('PUT')

							<!-- Start of row -->
							<div class="row">
								
								<!-- First Name -->
								<div class="col-md-6">
									<div class="mb-3">
										<label class="form-label required-label">First Name</label>
										<input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="form-control @error('first_name') is-invalid @enderror" placeholder="Enter first name">
										@error('first_name')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>

								<!-- Last Name -->
								<div class="col-md-6">
									<div class="mb-3">
										<label class="form-label required-label">Last Name</label>
										<input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="form-control @error('last_name') is-invalid @enderror" placeholder="Enter last name">
										@error('last_name')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>

								<!-- Email -->
								<div class="col-md-6">
									<div class="mb-3">
										<label class="form-label required-label">Email</label>
										<input type="text" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email address">
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
                                                <option value="Super Admin" {{ old('user_role', $user->user_role) == 'Super Admin'? 'selected' : '' }}>Super Admin</option>
											@endif
											<option value="Admin" {{ old('user_role', $user->user_role) == 'Admin'? 'selected' : '' }}>Admin</option>
											<option value="Manager" {{ old('user_role', $user->user_role) == 'Manager' ? 'selected' : '' }}>Manager</option>
											<option value="Staff" {{ old('user_role', $user->user_role) == 'Staff' ? 'selected' : '' }}>Staff</option>
										</select>
										@error('user_role')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>

								<!-- Department -->
								<div class="col-md-6">
									<div class="mb-3">
										<label for="department_id" class="form-label required-label">Department</label>
										<select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
											<option value="">Select Department</option>
											@foreach($departments as $department)
												<option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
													{{ $department->name }}
												</option>
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
										<label class="form-label">New Password</label>
										<input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter new password (leave blank to keep current)">
										<small class="form-text text-muted">Must be at least 8 characters with uppercase, number and special character</small>
										@error('password')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>

								<!-- Confirm Password -->
								<div class="col-md-6">
									<div class="mb-3">
										<label class="form-label">Confirm New Password</label>
										<input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm new password">
										@error('password_confirmation')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>

							</div>
							<!-- End of row -->

							<div class="mt-4 text-end">
								<button type="submit" class="btn btn-dark"><i class="bi bi-pencil-square me-2"></i>Update User</button>
							</div>

						</form>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>

@endsection