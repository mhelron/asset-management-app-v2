@extends('layouts.app')
@section('content')
<!-- Add custom styles for assignment buttons -->
<style>
    .assignment-toggle .btn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        border: 1px solid #dee2e6;
        background-color: white;
        color: #333;
    }
    .assignment-toggle .btn.active, 
    .btn-outline-secondary.active {
        background-color: #343a40;
        color: white;
        font-weight: 500;
        box-shadow: inset 0 3px 5px rgba(0,0,0,.125);
    }
    .assignment-section {
        margin-top: 15px;
        margin-bottom: 15px;
    }
    /* Ensure dropdowns are visible and well-styled */
    .assignment-section select {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    /* Add margin to make dropdown more visible */
    #user_assignment, #department_assignment, #location_assignment {
        margin-top: 10px;
    }
    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
        color: #212529;
    }
</style>
<!-- Content Header -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0">Add Asset</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Asset</li>
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
                <!-- Add Inventory Form -->
                <div class="card">
                    <div class="card-body form-container">
                        @if(session('error'))
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <!-- Asset Image (1 column) -->
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label>Asset Image</label>
                                        <input type="file" name="asset_image" class="form-control" accept="image/*">
                                        
                                        @if(old('asset_image_name'))
                                            <div class="text-info mt-1">
                                                Previously selected: {{ old('asset_image_name') }}
                                            </div>
                                        @endif
                                        
                                        @error('asset_image', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Asset Name -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Asset Name<span class="text-danger"> *</span></label>
                                        <input type="text" name="item_name" value="{{ old('item_name') }}" class="form-control" placeholder="Enter asset name">
                                        @error('item_name', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                    
                                <!-- Asset Type -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="asset_type_id">Asset Type<span class="text-danger"> *</span></label>
                                        <select name="asset_type_id" id="asset_type_id" class="form-control" onchange="checkQuantityTracking()">
                                            <option value="" disabled selected>Select an asset type</option>
                                            @foreach ($assetTypes as $assetType)
                                                <option value="{{ $assetType->id }}" 
                                                    data-has-quantity="{{ $assetType->has_quantity }}" 
                                                    data-quantity-unit="{{ $assetType->quantity_unit }}"
                                                    {{ old('asset_type_id') == $assetType->id ? 'selected' : '' }}>
                                                    {{ $assetType->name }} 
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('asset_type_id', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Category -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="categories">Asset Category<span class="text-danger"> *</span></label>
                                        <select name="category_id" id="category_select" class="form-control">
                                            <option value="" disabled selected>Select a category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category }} 
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Serial Number<span class="text-danger"> *</span></label>
                                        <input type="text" name="serial_no" value="{{ old('serial_no') }}" class="form-control" placeholder="Enter serial number">
                                        @error('serial_no', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                
                            <!-- Quantity Fields -->
                            <div id="quantity_fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Maximum Quantity<span class="text-danger"> *</span></label>
                                            <input type="number" name="max_quantity" value="{{ old('max_quantity') }}" class="form-control" placeholder="Enter maximum quantity" min="0">
                                            <small class="form-text text-muted">The total capacity or maximum stock available</small>
                                            @error('max_quantity', 'inventoryForm')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Minimum Quantity<span class="text-danger"> *</span></label>
                                            <input type="number" name="min_quantity" value="{{ old('min_quantity') }}" class="form-control" placeholder="Enter minimum quantity" min="0">
                                            <small class="form-text text-muted">You will be notified when quantity falls below this level</small>
                                            @error('min_quantity', 'inventoryForm')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Model Number<span class="text-danger"> *</span></label>
                                        <input type="text" name="model_no" value="{{ old('model_no') }}" class="form-control" placeholder="Enter model number">
                                        @error('model_no', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Manufacturer<span class="text-danger"> *</span></label>
                                        <input type="text" name="manufacturer" value="{{ old('manufacturer') }}" class="form-control" placeholder="Enter manufacturer name">
                                        @error('manufacturer', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Date Purchased<span class="text-danger"> *</span></label>
                                        <input type="date" name="date_purchased" value="{{ old('date_purchased') }}" class="form-control">
                                        @error('date_purchased', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Purchased From<span class="text-danger"> *</span></label>
                                        <input type="text" name="purchased_from" value="{{ old('purchased_from') }}" class="form-control" placeholder="Enter where purchased">
                                        @error('purchased_from', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Asset Tag<span class="text-danger"> *</span></label>
                                        <input type="text" name="asset_tag" id="asset_tag_input" value="" class="form-control" readonly>
                                        @error('asset_tag', 'inventoryForm')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                
                            <div class="form-group mb-3">
                                <label>Owner</label>
                                <div class="btn-group mb-3" role="group">
                                    <button type="button" class="btn btn-outline-secondary" id="user-btn" onclick="showUserDropdown()">
                                        <i class="bi bi-person"></i> User
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="department-btn" onclick="showDepartmentDropdown()">
                                        <i class="bi bi-building"></i> Department
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="location-btn" onclick="showLocationDropdown()">
                                        <i class="bi bi-geo-alt"></i> Location
                                    </button>
                                </div>
                                
                                <div id="user_assignment" class="assignment-section" style="display: none; margin-top: 10px;">
                                    <select name="users_id" id="user_id" class="form-select">
                                        <option value="">Select a User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" data-department="{{ $user->department_id }}" {{ old('users_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->first_name }} {{$user->last_name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div id="department_assignment" class="assignment-section" style="display: none; margin-top: 10px;">
                                    <select name="department_id" id="department_id" class="form-select">
                                        <option value="">Select a Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div id="location_assignment" class="assignment-section" style="display: none; margin-top: 10px;">
                                    <select name="location_id" id="location_id" class="form-select">
                                        <option value="">Select a Location</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                                                    
                            <!-- Asset Location Field - Only shown when User is selected as owner -->
                            <div class="form-group mb-3" id="asset_location_container" style="display: none;">
                                <label>Asset Location<span class="text-danger"> *</span></label>
                                <select name="asset_location_id" id="asset_location_id" class="form-select">
                                    <option value="">Select Asset Location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('asset_location_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location_id', 'inventoryForm')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Asset-specific Custom Fields -->
                            <div id="asset-fields-container">
                                <div class="row">
                                    @foreach($assetCustomFields as $field)
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>
                                                    {{ $field->name }}
                                                    @if($field->is_required) <span class="text-danger">*</span> @endif
                                                </label>
                                                
                                                @switch($field->type)
                                                    @case('Text')
                                                        <input type="text" 
                                                            name="custom_fields[{{ $field->name }}]" 
                                                            class="form-control @error('custom_fields.'.$field->name, 'inventoryForm') is-invalid @enderror" 
                                                            value="{{ old('custom_fields.'.$field->name, '') }}">
                                                        @break
                                                        
                                                    @case('Number')
                                                        <input type="number" 
                                                            name="custom_fields[{{ $field->name }}]" 
                                                            class="form-control @error('custom_fields.'.$field->name, 'inventoryForm') is-invalid @enderror" 
                                                            value="{{ old('custom_fields.'.$field->name, '') }}">
                                                        @break
                                                        
                                                    @case('Date')
                                                        <input type="date" 
                                                            name="custom_fields[{{ $field->name }}]" 
                                                            class="form-control @error('custom_fields.'.$field->name, 'inventoryForm') is-invalid @enderror" 
                                                            value="{{ old('custom_fields.'.$field->name, '') }}">
                                                        @break
                                                        
                                                    @case('Select')
                                                        <select name="custom_fields[{{ $field->name }}]" 
                                                            class="form-control @error('custom_fields.'.$field->name, 'inventoryForm') is-invalid @enderror">
                                                            <option value="">Select an option</option>
                                                            @foreach(json_decode($field->options) as $option)
                                                                <option value="{{ $option }}" {{ old('custom_fields.'.$field->name) == $option ? 'selected' : '' }}>
                                                                    {{ $option }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @break
                                                        
                                                    @case('Textarea')
                                                        <textarea 
                                                            name="custom_fields[{{ $field->name }}]" 
                                                            class="form-control @error('custom_fields.'.$field->name, 'inventoryForm') is-invalid @enderror">{{ old('custom_fields.'.$field->name, '') }}</textarea>
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
                                                                        {{ is_array(old('custom_fields.'.$field->name)) && in_array($option, old('custom_fields.'.$field->name)) ? 'checked' : '' }}>
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
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Category-specific Custom Fields -->
                            <div id="category-fields-container"></div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="reset" class="btn btn-secondary me-2">Reset</button>
                                <button type="submit" class="btn btn-primary">Save Asset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple function to hide all dropdowns
    function hideAllDropdowns() {
        try {
            document.getElementById('user_assignment').style.display = 'none';
            document.getElementById('department_assignment').style.display = 'none';
            document.getElementById('location_assignment').style.display = 'none';
            
            // Remove active class from all buttons
            document.getElementById('user-btn').classList.remove('active');
            document.getElementById('department-btn').classList.remove('active');
            document.getElementById('location-btn').classList.remove('active');
            
            // Clear values from hidden dropdowns
            document.getElementById('user_id').value = '';
            document.getElementById('department_id').value = '';
            document.getElementById('location_id').value = '';
            
            // Hide asset location container by default
            document.getElementById('asset_location_container').style.display = 'none';
        } catch (e) {
            console.error('Error in hideAllDropdowns:', e);
        }
    }
    
    // Function to check if the selected asset type has quantity tracking
    function checkQuantityTracking() {
        const assetTypeSelect = document.getElementById('asset_type_id');
        const quantityFields = document.getElementById('quantity_fields');
        
        if (assetTypeSelect && quantityFields) {
            const selectedOption = assetTypeSelect.options[assetTypeSelect.selectedIndex];
            
            if (selectedOption && selectedOption.getAttribute('data-has-quantity') === '1') {
                quantityFields.style.display = 'block';
            } else {
                quantityFields.style.display = 'none';
            }
        }
    }
    
    // Functions to show specific dropdowns
    function showUserDropdown() {
        try {
            // Clear all dropdown values first
            document.getElementById('user_id').value = '';
            document.getElementById('department_id').value = '';
            document.getElementById('location_id').value = '';
            
            // Hide all dropdowns
            document.getElementById('user_assignment').style.display = 'none';
            document.getElementById('department_assignment').style.display = 'none';
            document.getElementById('location_assignment').style.display = 'none';
            
            // Remove active class from all buttons
            document.getElementById('user-btn').classList.remove('active');
            document.getElementById('department-btn').classList.remove('active');
            document.getElementById('location-btn').classList.remove('active');
            
            // Show user dropdown and set active class
            document.getElementById('user_assignment').style.display = 'block';
            document.getElementById('user-btn').classList.add('active');
            
            // Show asset location field when user is selected as owner
            document.getElementById('asset_location_container').style.display = 'block';
            console.log('User dropdown shown');
        } catch (e) {
            console.error('Error in showUserDropdown:', e);
        }
    }
    
    function showDepartmentDropdown() {
        try {
            // Clear all dropdown values first
            document.getElementById('user_id').value = '';
            document.getElementById('department_id').value = '';
            document.getElementById('location_id').value = '';
            
            // Hide all dropdowns
            document.getElementById('user_assignment').style.display = 'none';
            document.getElementById('department_assignment').style.display = 'none';
            document.getElementById('location_assignment').style.display = 'none';
            
            // Remove active class from all buttons
            document.getElementById('user-btn').classList.remove('active');
            document.getElementById('department-btn').classList.remove('active');
            document.getElementById('location-btn').classList.remove('active');
            
            // Show department dropdown and set active class
            document.getElementById('department_assignment').style.display = 'block';
            document.getElementById('department-btn').classList.add('active');
            
            // Hide asset location field when department is selected as owner
            document.getElementById('asset_location_container').style.display = 'none';
            console.log('Department dropdown shown');
        } catch (e) {
            console.error('Error in showDepartmentDropdown:', e);
        }
    }
    
    function showLocationDropdown() {
        try {
            // Clear all dropdown values first
            document.getElementById('user_id').value = '';
            document.getElementById('department_id').value = '';
            document.getElementById('location_id').value = '';
            
            // Hide all dropdowns
            document.getElementById('user_assignment').style.display = 'none';
            document.getElementById('department_assignment').style.display = 'none';
            document.getElementById('location_assignment').style.display = 'none';
            
            // Remove active class from all buttons
            document.getElementById('user-btn').classList.remove('active');
            document.getElementById('department-btn').classList.remove('active');
            document.getElementById('location-btn').classList.remove('active');
            
            // Show location dropdown and set active class
            document.getElementById('location_assignment').style.display = 'block';
            document.getElementById('location-btn').classList.add('active');
            
            // Hide asset location field when location is selected as owner
            document.getElementById('asset_location_container').style.display = 'none';
            console.log('Location dropdown shown');
        } catch (e) {
            console.error('Error in showLocationDropdown:', e);
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded');
        
        // Get button and dropdown elements
        const userBtn = document.getElementById('user-btn');
        const departmentBtn = document.getElementById('department-btn');
        const locationBtn = document.getElementById('location-btn');
        
        // Debug element existence
        console.log('Elements found:', {
            userBtn: userBtn,
            departmentBtn: departmentBtn,
            locationBtn: locationBtn
        });
        
        // Set button click handlers
        userBtn.addEventListener('click', function() {
            // Don't clear the values when initializing
            showUserDropdown();
            // Restore the selected user if there was one
            const usersId = {{ old('users_id') ? old('users_id') : 'null' }};
            if (usersId && usersId !== 'null') {
                document.getElementById('user_id').value = usersId;
            }
        });
        
        departmentBtn.addEventListener('click', function() {
            // Don't clear the values when initializing
            showDepartmentDropdown();
            // Restore the selected department if there was one
            const departmentId = {{ old('department_id') ? old('department_id') : 'null' }};
            if (departmentId && departmentId !== 'null') {
                document.getElementById('department_id').value = departmentId;
            }
        });
        
        locationBtn.addEventListener('click', function() {
            // Don't clear the values when initializing
            showLocationDropdown();
            // Restore the selected location if there was one
            const locationId = {{ old('location_id') ? old('location_id') : 'null' }};
            if (locationId && locationId !== 'null') {
                document.getElementById('location_id').value = locationId;
            }
        });
        
        // Set initial active state
        try {
            // Get the current owner values
            const usersId = {{ old('users_id') ? old('users_id') : 'null' }};
            const departmentId = {{ old('department_id') ? old('department_id') : 'null' }};
            const locationId = {{ old('location_id') ? old('location_id') : 'null' }};
            const assetLocationId = {{ old('asset_location_id') ? old('asset_location_id') : 'null' }};
            
            console.log('Owner values:', { usersId, departmentId, locationId, assetLocationId });
            
            // Initialize the form based on the current owner
            if (usersId && usersId !== 'null') {
                // Don't use the click handler as it would clear the values
                document.getElementById('user_assignment').style.display = 'block';
                document.getElementById('user-btn').classList.add('active');
                document.getElementById('asset_location_container').style.display = 'block';
                
                // Set the asset location value
                if (assetLocationId && assetLocationId !== 'null') {
                    document.getElementById('asset_location_id').value = assetLocationId;
                } else if (locationId && locationId !== 'null') {
                    document.getElementById('asset_location_id').value = locationId;
                }
            } else if (departmentId && departmentId !== 'null') {
                document.getElementById('department_assignment').style.display = 'block';
                document.getElementById('department-btn').classList.add('active');
            } else if (locationId && locationId !== 'null') {
                document.getElementById('location_assignment').style.display = 'block';
                document.getElementById('location-btn').classList.add('active');
            } else {
                // Default to user if nothing is selected
                document.getElementById('user_assignment').style.display = 'block';
                document.getElementById('user-btn').classList.add('active');
                document.getElementById('asset_location_container').style.display = 'block';
            }
        } catch (error) {
            console.error('Error setting initial state:', error);
            // Default fallback
            document.getElementById('user_assignment').style.display = 'block';
            document.getElementById('user-btn').classList.add('active');
            document.getElementById('asset_location_container').style.display = 'block';
        }

        // Image preview
        const imageInput = document.querySelector('input[name="asset_image"]');
        const imagePreviewContainer = document.getElementById('image-preview-container');
        const imagePreview = document.getElementById('image-preview');
        
        if (imageInput && imagePreviewContainer && imagePreview) {
            imageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreviewContainer.style.display = 'block';
                    }
                    reader.readAsDataURL(this.files[0]);
                } else {
                    imagePreviewContainer.style.display = 'none';
                }
            });
        }

        // Automatically set department when user is selected
        const userSelect = document.getElementById('user_id');
        const departmentSelect = document.getElementById('department_id');
        const assetLocationSelect = document.getElementById('asset_location_id');
        const locationSelect = document.getElementById('location_id');
        
        if (userSelect && departmentSelect) {
            userSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const departmentId = selectedOption.getAttribute('data-department');
                
                if (departmentId) {
                    departmentSelect.value = departmentId;
                }
            });
        }
        
        // Set location_id when department is selected
        if (departmentSelect) {
            departmentSelect.addEventListener('change', function() {
                if (this.value) {
                    // Fetch the department's location_id
                    fetch(`/departments/get-location/${this.value}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.location_id) {
                                // Set the location_id field
                                locationSelect.value = data.location_id;
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching department location:', error);
                        });
                } else {
                    // Clear the location_id field if no department is selected
                    locationSelect.value = '';
                }
            });
        }
        
        // Set location_id when asset location is selected for user owner
        if (assetLocationSelect) {
            assetLocationSelect.addEventListener('change', function() {
                // Copy the selected location to the hidden location_id field
                locationSelect.value = this.value;
            });
        }
        
        // Handle form submission
        const form = document.querySelector('form[action*="inventory.store"]');
        if (form) {
            form.addEventListener('submit', function(event) {
                const activeOwnerType = document.querySelector('.btn-outline-secondary.active');
                
                // If user is selected as owner, require asset location
                if (activeOwnerType && activeOwnerType.id === 'user-btn') {
                    if (!assetLocationSelect.value) {
                        event.preventDefault();
                        alert('Please select an asset location when assigning to a user.');
                        assetLocationSelect.focus();
                        return false;
                    }
                    
                    // Set the location_id to the selected asset location
                    locationSelect.value = assetLocationSelect.value;
                }
                
                // If location is selected as owner, copy the location_id
                if (activeOwnerType && activeOwnerType.id === 'location-btn') {
                    // location_id is already set by the location dropdown
                }
                
                // If department is selected, we would ideally set the location based on department
                // For now, we'll leave it as is
                
                return true;
            });
        }
        
        // Function to handle validation errors for category fields
        function displayCategoryFieldErrors() {
            // Check if we have validation errors from Laravel
            @if($errors->inventoryForm->any())
                // Get all the errors
                const errors = @json($errors->inventoryForm->messages());
                console.log('Validation errors:', errors);
                
                // Loop through errors and find category custom field errors
                for (const key in errors) {
                    if (key.startsWith('custom_fields.')) {
                        const fieldName = key.replace('custom_fields.', '');
                        console.log('Field with error:', fieldName);
                        
                        // Find the error element in the category fields container
                        const errorElement = document.querySelector(`#category-fields-container #error_${fieldName.replace(/\s+/g, '_')}`);
                        if (errorElement) {
                            errorElement.textContent = errors[key][0];
                            
                            // Also add is-invalid class to the input
                            const inputElement = errorElement.previousElementSibling;
                            if (inputElement) {
                                inputElement.classList.add('is-invalid');
                            }
                        }
                    }
                }
            @endif
        }
        
        // Load category-specific custom fields when category is selected
        const categorySelect = document.getElementById('category_select');
        const categoryFieldsContainer = document.getElementById('category-fields-container');
        
        if (categorySelect && categoryFieldsContainer) {
            categorySelect.addEventListener('change', function() {
                const categoryId = this.value;
                
                if (categoryId) {
                    // Fetch category-specific custom fields
                    fetch(`/inventory/get-category-fields/${categoryId}`)
                        .then(response => response.json())
                        .then(data => {
                            // Clear previous fields
                            categoryFieldsContainer.innerHTML = '';
                            
                            if (data.length > 0) {
                                // Create row for 2-column layout
                                let currentRow = document.createElement('div');
                                currentRow.className = 'row';
                                categoryFieldsContainer.appendChild(currentRow);
                                
                                // Add each custom field
                                data.forEach((field, index) => {
                                    // Create column for each field
                                    const colDiv = document.createElement('div');
                                    colDiv.className = 'col-md-6';
                                    
                                    const fieldDiv = document.createElement('div');
                                    fieldDiv.className = 'form-group mb-3';
                                    
                                    // Create label
                                    const label = document.createElement('label');
                                    label.textContent = field.name;
                                    
                                    if (field.is_required) {
                                        const requiredSpan = document.createElement('span');
                                        requiredSpan.className = 'text-danger';
                                        requiredSpan.textContent = ' *';
                                        label.appendChild(requiredSpan);
                                    }
                                    
                                    fieldDiv.appendChild(label);
                                    
                                    // Create input based on field type
                                    let input;
                                    
                                    switch (field.type) {
                                        case 'Text':
                                            input = document.createElement('input');
                                            input.type = 'text';
                                            input.className = 'form-control';
                                            input.name = `custom_fields[${field.name}]`;
                                            // Remove required attribute, will be validated server-side
                                            break;
                                            
                                        case 'Select':
                                            input = document.createElement('select');
                                            input.className = 'form-control';
                                            input.name = `custom_fields[${field.name}]`;
                                            
                                            // Add default option
                                            const defaultOption = document.createElement('option');
                                            defaultOption.value = '';
                                            defaultOption.textContent = 'Select an option';
                                            input.appendChild(defaultOption);
                                            
                                            // Add options
                                            const fieldOptions = JSON.parse(field.options);
                                            fieldOptions.forEach((option, index) => {
                                                const optionEl = document.createElement('option');
                                                optionEl.value = option;
                                                optionEl.textContent = option;
                                                input.appendChild(optionEl);
                                            });
                                            break;
                                            
                                        case 'Checkbox':
                                            input = document.createElement('div');
                                            
                                            const checkboxOptions = JSON.parse(field.options);
                                            checkboxOptions.forEach((option, index) => {
                                                const checkDiv = document.createElement('div');
                                                checkDiv.className = 'form-check';
                                                
                                                const checkbox = document.createElement('input');
                                                checkbox.type = 'checkbox';
                                                checkbox.className = 'form-check-input';
                                                checkbox.name = `custom_fields[${field.name}][]`;
                                                checkbox.value = option;
                                                checkbox.id = `${field.name}_${index}`;
                                                
                                                const checkLabel = document.createElement('label');
                                                checkLabel.className = 'form-check-label';
                                                checkLabel.htmlFor = `${field.name}_${index}`;
                                                checkLabel.textContent = option;
                                                
                                                checkDiv.appendChild(checkbox);
                                                checkDiv.appendChild(checkLabel);
                                                input.appendChild(checkDiv);
                                            });
                                            break;
                                            
                                        default:
                                            input = document.createElement('input');
                                            input.type = 'text';
                                            input.className = 'form-control';
                                            input.name = `custom_fields[${field.name}]`;
                                    }
                                    
                                    fieldDiv.appendChild(input);
                                    
                                    // Add error message container
                                    const errorSpan = document.createElement('span');
                                    errorSpan.className = 'text-danger error-message';
                                    errorSpan.id = `error_${field.name.replace(/\s+/g, '_')}`;
                                    fieldDiv.appendChild(errorSpan);
                                    
                                    colDiv.appendChild(fieldDiv);
                                    currentRow.appendChild(colDiv);
                                    
                                    // Create a new row after every 2 fields
                                    if (index % 2 === 1 && index < data.length - 1) {
                                        currentRow = document.createElement('div');
                                        currentRow.className = 'row';
                                        categoryFieldsContainer.appendChild(currentRow);
                                    }
                                });
                                
                                // Display any validation errors after fields are created
                                setTimeout(displayCategoryFieldErrors, 100);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching category fields:', error);
                        });
                } else {
                    // Clear the container if no category is selected
                    categoryFieldsContainer.innerHTML = '';
                }
            });
            
            // Trigger the change event if a category is already selected (for page reload with validation errors)
            if (categorySelect.value) {
                categorySelect.dispatchEvent(new Event('change'));
            }
        }
        
        // Asset Tag Generation based on Category, Purchase Date and Name
        const itemNameInput = document.querySelector('input[name="item_name"]');
        const datePurchasedInput = document.querySelector('input[name="date_purchased"]');
        const assetTagInput = document.getElementById('asset_tag_input');
        
        function generateAssetTag() {
            // Only generate if all required fields are filled
            if (!categorySelect || !categorySelect.value || 
                !datePurchasedInput || !datePurchasedInput.value || 
                !itemNameInput || !itemNameInput.value) {
                return;
            }
            
            // Get category text
            const categoryText = categorySelect.options[categorySelect.selectedIndex].text;
            
            // Get first letter of each word in category
            const categoryCode = categoryText
                .split(' ')
                .map(word => word.charAt(0).toUpperCase())
                .join('');
            
            // Format date as MMYYYY
            const purchaseDate = new Date(datePurchasedInput.value);
            const month = String(purchaseDate.getMonth() + 1).padStart(2, '0');
            const year = purchaseDate.getFullYear();
            const dateCode = month + year;
            
            // Format name (remove vowels, with no character limit)
            let nameCode = itemNameInput.value.replace(/[aeiou\s]/gi, '').toUpperCase();
            if (nameCode.length < 2) {
                nameCode = itemNameInput.value.toUpperCase();
            }
            
            // Combine to create tag in format: CATEGORY-MMYYYY-NAME
            const assetTag = `${categoryCode}-${dateCode}-${nameCode}`;
            
            // Update the asset tag input
            assetTagInput.value = assetTag;
        }
        
        // Add event listeners to generate tag when relevant fields change
        if (categorySelect && itemNameInput && datePurchasedInput && assetTagInput) {
            categorySelect.addEventListener('change', generateAssetTag);
            itemNameInput.addEventListener('input', generateAssetTag);
            datePurchasedInput.addEventListener('change', generateAssetTag);
            
            // Generate initially if all fields have values (for page reload)
            if (categorySelect.value && datePurchasedInput.value && itemNameInput.value) {
                generateAssetTag();
            }
        }
    });
</script>
@endsection