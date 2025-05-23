@extends('layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0">Edit Asset</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Asset</li>
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
                    <a href="{{ route('inventory.index') }}" class="btn btn-danger"><i class="bi bi-arrow-return-left me-2"></i>Back</a>
                </div>

                <!-- Edit Inventory Form -->
                <div class="card">
                    <div class="card-body form-container">
                        <form action="{{ route('inventory.update', $inventoryItem->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <!-- Asset Name -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Asset Name<span class="text-danger"> *</span></label>
                                        <input type="text" name="item_name" value="{{ old('item_name', $inventoryItem->item_name) }}" class="form-control" placeholder="Enter asset name">
                                        @error('item_name', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                    
                                <!-- Category -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="categories">Asset Category<span class="text-danger"> *</span></label>
                                        <select name="category_id" id="category_select" class="form-control">
                                            <option value="" disabled>Select a category</option>
                                            @foreach ($categories as $id => $category)
                                                <option value="{{ $id }}" {{ old('category_id', $inventoryItem->category_id) == $id ? 'selected' : '' }}>
                                                    {{ $category }} 
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Asset Tag<span class="text-danger"> *</span></label>
                                        <input type="text" name="asset_tag" value="{{ old('asset_tag', $inventoryItem->asset_tag) }}" class="form-control" readonly>
                                        @error('asset_tag', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Serial Number<span class="text-danger"> *</span></label>
                                        <input type="text" name="serial_no" value="{{ old('serial_no', $inventoryItem->serial_no) }}" class="form-control" placeholder="Enter serial number">
                                        @error('serial_no', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Model Number<span class="text-danger"> *</span></label>
                                        <input type="text" name="model_no" value="{{ old('model_no', $inventoryItem->model_no) }}" class="form-control" placeholder="Enter model number">
                                        @error('model_no', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Date Purchased<span class="text-danger"> *</span></label>
                                        <input type="date" name="date_purchased" value="{{ old('date_purchased', $inventoryItem->date_purchased) }}" class="form-control">
                                        @error('date_purchased', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Manufacturer<span class="text-danger"> *</span></label>
                                        <input type="text" name="manufacturer" value="{{ old('manufacturer', $inventoryItem->manufacturer) }}" class="form-control" placeholder="Enter manufacturer name">
                                        @error('manufacturer', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Purchased From<span class="text-danger"> *</span></label>
                                        <input type="text" name="purchased_from" value="{{ old('purchased_from', $inventoryItem->purchased_from) }}" class="form-control" placeholder="Enter where purchased">
                                        @error('purchased_from', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Asset Image</label>
                                        <input type="file" name="asset_image" class="form-control" accept="image/*">
                                        
                                        @if($inventoryItem->image_path)
                                            <div class="text-muted mt-1">
                                                Current image: {{ basename($inventoryItem->image_path) }}
                                            </div>
                                        @endif
                                        
                                        @error('asset_image', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                
                            <!-- Asset Note -->
                            <div class="form-group mb-3">
                                <label>Asset Note</label>
                                <textarea name="log_note" class="form-control" placeholder="Enter any notes about this asset">{{ old('log_note', $inventoryItem->log_note) }}</textarea>
                                @error('log_note', 'inventoryForm')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Asset Owner</label>
                                        <select name="users_id" id="user_id" class="form-control">
                                            <option value="">Select a User</option>
                                            @foreach($users ?? [] as $user)
                                                <option value="{{ $user->id }}" data-department="{{ $user->department_id }}" {{ old('users_id', $inventoryItem->users_id) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->first_name }} {{$user->last_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('users_id', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Asset Location</label>
                                        <select name="department_id" id="department_id" class="form-control">
                                            <option value="">Select a Department</option>
                                            @foreach($departments ?? [] as $department)
                                                <option value="{{ $department->id }}" {{ old('department_id', $inventoryItem->department_id) == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department_id', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                                                    
                            <!-- Asset-specific Custom Fields -->
                            <div id="asset-fields-container">
                                <h5 class="mt-4 mb-3">Custom Fields</h5>
                                @foreach($assetCustomFields as $field)
                                    <div class="form-group mb-3">
                                        <label>
                                            {{ $field->name }}
                                            @if($field->is_required) <span class="text-danger">*</span> @endif
                                        </label>
                                        
                                        @php
                                            $fieldValue = '';
                                            if (old('custom_fields.'.$field->name)) {
                                                $fieldValue = old('custom_fields.'.$field->name);
                                            } elseif (isset($inventoryItem->custom_fields[$field->name])) {
                                                $fieldValue = $inventoryItem->custom_fields[$field->name];
                                            }
                                        @endphp
                                        
                                        @switch($field->type)
                                            @case('Text')
                                                <input type="text" 
                                                    name="custom_fields[{{ $field->name }}]" 
                                                    class="form-control @error('custom_fields.'.$field->name, 'inventoryForm') is-invalid @enderror" 
                                                    value="{{ $fieldValue }}">
                                                @break
                                                
                                            @case('Number')
                                                <input type="number" 
                                                    name="custom_fields[{{ $field->name }}]" 
                                                    class="form-control @error('custom_fields.'.$field->name, 'inventoryForm') is-invalid @enderror" 
                                                    value="{{ $fieldValue }}">
                                                @break
                                                
                                            @case('Date')
                                                <input type="date" 
                                                    name="custom_fields[{{ $field->name }}]" 
                                                    class="form-control @error('custom_fields.'.$field->name, 'inventoryForm') is-invalid @enderror" 
                                                    value="{{ $fieldValue }}">
                                                @break
                                                
                                            @case('Select')
                                                <select name="custom_fields[{{ $field->name }}]" 
                                                    class="form-control @error('custom_fields.'.$field->name, 'inventoryForm') is-invalid @enderror">
                                                    <option value="">Select an option</option>
                                                    @foreach(json_decode($field->options) as $option)
                                                        <option value="{{ $option }}" {{ $fieldValue == $option ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @break
                                                
                                            @case('Textarea')
                                                <textarea 
                                                    name="custom_fields[{{ $field->name }}]" 
                                                    class="form-control @error('custom_fields.'.$field->name, 'inventoryForm') is-invalid @enderror">{{ $fieldValue }}</textarea>
                                                @break
                                                
                                            @case('Checkbox')
                                                <div>
                                                    @foreach(json_decode($field->options) as $option)
                                                        <div class="form-check">
                                                            <input type="checkbox" 
                                                                class="form-check-input @error('custom_fields.'.$field->name, 'inventoryForm') is-invalid @enderror" 
                                                                name="custom_fields[{{ $field->name }}][]" 
                                                                value="{{ $option }}"
                                                                id="{{ $field->name }}_{{ $loop->index }}"
                                                                {{ is_array($fieldValue) && in_array($option, $fieldValue) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="{{ $field->name }}_{{ $loop->index }}">
                                                                {{ $option }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @break
                                        @endswitch
                                        
                                        @error('custom_fields.' . $field->name, 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Category-specific Custom Fields -->
                            @if(isset($categoryCustomFields) && count($categoryCustomFields) > 0)
                                <div id="category-fields-container">
                                    <h5 class="mt-2 mb-3">Category-specific Fields</h5>
                                    @foreach($categoryCustomFields as $field)
                                        <div class="form-group mb-3">
                                            <label>
                                                {{ $field->name }}
                                                @if($field->is_required) <span class="text-danger">*</span> @endif
                                            </label>
                                            
                                            @php
                                                $fieldValue = '';
                                                if (old('custom_fields.'.$field->name)) {
                                                    $fieldValue = old('custom_fields.'.$field->name);
                                                } elseif (isset($inventoryItem->custom_fields[$field->name])) {
                                                    $fieldValue = $inventoryItem->custom_fields[$field->name];
                                                }
                                            @endphp
                                            
                                            @switch($field->type)
                                                @case('Text')
                                                    <input type="text" 
                                                        name="custom_fields[{{ $field->name }}]" 
                                                        class="form-control" 
                                                        value="{{ $fieldValue }}"
                                                        @if($field->is_required) required @endif>
                                                    @break
                                                    
                                                @case('Number')
                                                    <input type="number" 
                                                        name="custom_fields[{{ $field->name }}]" 
                                                        class="form-control" 
                                                        value="{{ $fieldValue }}"
                                                        @if($field->is_required) required @endif>
                                                    @break
                                                    
                                                @case('Date')
                                                    <input type="date" 
                                                        name="custom_fields[{{ $field->name }}]" 
                                                        class="form-control" 
                                                        value="{{ $fieldValue }}"
                                                        @if($field->is_required) required @endif>
                                                    @break
                                                    
                                                @case('Select')
                                                    <select name="custom_fields[{{ $field->name }}]" 
                                                        class="form-control"
                                                        @if($field->is_required) required @endif>
                                                        <option value="">Select an option</option>
                                                        @foreach(json_decode($field->options) as $option)
                                                            <option value="{{ $option }}" {{ $fieldValue == $option ? 'selected' : '' }}>
                                                                {{ $option }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @break
                                                    
                                                @case('Textarea')
                                                    <textarea 
                                                        name="custom_fields[{{ $field->name }}]" 
                                                        class="form-control"
                                                        @if($field->is_required) required @endif>{{ $fieldValue }}</textarea>
                                                    @break
                                                    
                                                @case('Checkbox')
                                                    <div>
                                                        @foreach(json_decode($field->options) as $option)
                                                            <div class="form-check">
                                                                <input type="checkbox" 
                                                                    class="form-check-input" 
                                                                    name="custom_fields[{{ $field->name }}][]" 
                                                                    value="{{ $option }}"
                                                                    id="{{ $field->name }}_{{ $loop->index }}"
                                                                    {{ is_array($fieldValue) && in_array($option, $fieldValue) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="{{ $field->name }}_{{ $loop->index }}">
                                                                    {{ $option }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @break
                                            @endswitch
                                            
                                            @error('custom_fields.' . $field->name, 'inventoryForm')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="d-flex justify-content-end mt-4">
                                <button type="reset" class="btn btn-secondary me-2">Reset</button>
                                <button type="submit" class="btn btn-primary">Update Asset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image preview
        const imageInput = document.querySelector('input[name="asset_image"]');
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        let imagePreview = document.getElementById('image-preview');
                        if (!imagePreview) {
                            // Create preview container if it doesn't exist
                            const previewContainer = document.createElement('div');
                            previewContainer.className = 'mt-2';
                            previewContainer.id = 'image-preview-container';
                            
                            imagePreview = document.createElement('img');
                            imagePreview.id = 'image-preview';
                            imagePreview.style.maxWidth = '100%';
                            imagePreview.style.maxHeight = '200px';
                            imagePreview.alt = 'Asset Image Preview';
                            
                            previewContainer.appendChild(imagePreview);
                            imageInput.parentNode.appendChild(previewContainer);
                        }
                        
                        imagePreview.src = e.target.result;
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        // Automatically set department when user is selected
        const userSelect = document.getElementById('user_id');
        const departmentSelect = document.getElementById('department_id');
        
        if (userSelect && departmentSelect) {
            userSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const departmentId = selectedOption.getAttribute('data-department');
                    if (departmentId) {
                        departmentSelect.value = departmentId;
                    }
                }
            });
        }
    });
</script>
@endpush

@endsection