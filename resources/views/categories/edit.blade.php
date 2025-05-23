@extends('layouts.app')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0">Edit Category</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Category</li>
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
                    <a href="{{ route('categories.index') }}" class="btn btn-danger"><i class="bi bi-arrow-return-left me-2"></i>Back</a>
                </div>
                <!-- Edit Category Form -->
                <div class="card">
                    <div class="card-body form-container">
                        <form action="{{ url('categories/update-category/'.$editdata->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                    
                            <!-- Category Name -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Category Name<span class="text-danger"> *</span></label>
                                    <input type="text" name="category" value="{{ old('category', $editdata->category) }}" class="form-control" placeholder="Enter category name">
                                    @error('category')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Category Type -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Category Type<span class="text-danger"> *</span></label>
                                    <select name="type" class="form-control">
                                        <option value="">Select Type</option>
                                        <option value="Asset" {{ old('type', $editdata->type) == 'Asset' ? 'selected' : '' }}>Asset</option>
                                        <option value="Accessory" {{ old('type', $editdata->type) == 'Accessory' ? 'selected' : '' }}>Accessory</option>
                                        <option value="Component" {{ old('type', $editdata->type) == 'Component' ? 'selected' : '' }}>Component</option>
                                        <option value="Consumable" {{ old('type', $editdata->type) == 'Consumable' ? 'selected' : '' }}>Consumable</option>
                                        <option value="License" {{ old('type', $editdata->type) == 'License' ? 'selected' : '' }}>License</option>
                                    </select>
                                    @error('type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Description <span class="text-danger"> *</span></label>
                                    <textarea name="desc" class="form-control" placeholder="Enter description">{{ old('desc', $editdata->desc) }}</textarea>
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
                                        <option value="Active" {{ old('status', $editdata->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $editdata->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Custom Field Option -->
                            @php
                                $selectedCustomFieldIds = is_array($editdata->custom_fields) ? $editdata->custom_fields : json_decode($editdata->custom_fields, true) ?? [];
                                $hasCustomFields = !empty($selectedCustomFieldIds);
                            @endphp
                            
                            <div class="col-md-12 mt-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="toggleCustomFields" {{ $hasCustomFields ? 'checked' : '' }}>
                                    <label class="form-check-label" for="toggleCustomFields">Does this category have custom fields?</label>
                                </div>
                            </div>
                            
                            <!-- Custom Fields Section -->
                            <div class="col-md-12 mt-4" id="customFieldsSection" style="{{ $hasCustomFields ? 'display: block;' : 'display: none;' }}">

                                <div class="alert alert-info alert-dismissible fade show mt-2">
                                    <i class="bi bi-info-circle me-2"></i>
                                    You can add more custom fields <a href="{{ route('customfields.index') }}">here.</a>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>

                                <h4 class="mt-2">Custom Fields</h4>
                                <p class="text-muted">Select the custom fields to be associated with this category.</p>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Select</th>
                                            <th>Field Name</th>
                                            <th>Field Type</th>
                                            <th>Field Values/s</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($customFields as $customField)
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" name="custom_fields[]" value="{{ $customField->id }}" 
                                                    {{ in_array($customField->id, old('custom_fields', $selectedCustomFieldIds)) ? 'checked' : '' }}>
                                                </td>
                                                <td>{{ $customField->name }}</td>
                                                <td>{{ ucfirst($customField->type) }}</td>
                                                <td>
                                                    @if ($customField->type === 'text')
                                                        User Input
                                                    @elseif (in_array($customField->type, ['List', 'Checkbox', 'Radio', 'Select']) && !empty($customField->options))
                                                        {{ implode(', ', json_decode($customField->options, true)) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $customField->desc }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Submit button -->
                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-dark float-end"><i class="bi bi-pencil-square me-2"></i>Update Category</button>
                            </div>
                                
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let toggleCheckbox = document.getElementById('toggleCustomFields');
        let customFieldsSection = document.getElementById('customFieldsSection');
        
        if (toggleCheckbox.checked) {
            customFieldsSection.style.display = 'block';
        }
        toggleCheckbox.addEventListener('change', function() {
            customFieldsSection.style.display = this.checked ? 'block' : 'none';
        });
    });
</script>
@endsection