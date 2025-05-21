@extends('layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Asset</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Assets</a></li>
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
                                <!-- Item Name -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Asset Name<span class="text-danger"> *</span></label>
                                        <input type="text" name="item_name" value="{{ old('item_name', $inventoryItem->item_name) }}" class="form-control" placeholder="Enter item name">
                                        @error('item_name', 'inventoryForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                    
                                <!-- Category -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="categories">Category<span class="text-danger"> *</span></label>
                                        <select name="category_id" id="category_select" class="form-control">
                                            <option value="" disabled>Select a category</option>
                                            @foreach ($categories as $id => $category)
                                                <option value="{{ $id }}" {{ old('category_id', $inventoryItem->category_id) == $id ? 'selected' : '' }}>
                                                    {{ $category }} 
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id', 'inventoryForm')
                                            <small class="text-danger">{{ $message }}</small>
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
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Serial Number<span class="text-danger"> *</span></label>
                                        <input type="text" name="serial_no" value="{{ old('serial_no', $inventoryItem->serial_no) }}" class="form-control" placeholder="Enter serial number">
                                        @error('serial_no', 'inventoryForm')
                                            <small class="text-danger">{{ $message }}</small>
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
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Manufacturer<span class="text-danger"> *</span></label>
                                        <input type="text" name="manufacturer" value="{{ old('manufacturer', $inventoryItem->manufacturer) }}" class="form-control" placeholder="Enter manufacturer">
                                        @error('manufacturer', 'inventoryForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Date Purchased<span class="text-danger"> *</span></label>
                                        <input type="date" name="date_purchased" value="{{ old('date_purchased', $inventoryItem->date_purchased) }}" class="form-control">
                                        @error('date_purchased', 'inventoryForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Purchased From<span class="text-danger"> *</span></label>
                                        <input type="text" name="purchased_from" value="{{ old('purchased_from', $inventoryItem->purchased_from) }}" class="form-control" placeholder="Enter where purchased">
                                        @error('purchased_from', 'inventoryForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                
                            <!-- Log Note -->
                            <div class="form-group mb-3">
                                <label>Log Note</label>
                                <textarea name="log_note" class="form-control" placeholder="Enter any log notes">{{ old('log_note', $inventoryItem->log_note) }}</textarea>
                                @error('log_note', 'inventoryForm')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
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
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6" id="image-preview-container" style="{{ $inventoryItem->image_path ? 'display: block;' : 'display: none;' }}">
                                    <div class="form-group mb-3">
                                        <label>Image Preview</label>
                                        <div class="mt-2">
                                            <img id="image-preview" src="{{ $inventoryItem->image_path ? asset('storage/' . $inventoryItem->image_path) : '#' }}" alt="Asset Image Preview" style="max-width: 100%; max-height: 200px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                                                    
                            <!-- Asset-specific Custom Fields -->
                            <div id="asset-fields-container"></div>
                            
                            <!-- Category-specific Custom Fields -->
                            <div id="dynamic-fields-container"></div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Assigned to</label>
                                        <select name="users_id" id="user_id" class="form-control">
                                            <option value="" disabled>Select a User</option>
                                            @foreach($users ?? [] as $user)
                                                <option value="{{ $user->id }}" data-department="{{ $user->department_id }}" {{ old('users_id', $inventoryItem->users_id) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->first_name }} {{$user->last_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('users_id', 'inventoryForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Department</label>
                                        <select name="department_id" id="department_id" class="form-control">
                                            <option value="" disabled>Select a Department</option>
                                            @foreach($departments ?? [] as $department)
                                                <option value="{{ $department->id }}" {{ old('department_id', $inventoryItem->department_id) == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department_id', 'inventoryForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit button -->
                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-dark float-end"><i class="bi bi-save me-2"></i>Update Asset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to validate email format
    function validateEmail(input, fieldName) {
        const emailPopup = document.getElementById(`email-popup-${fieldName}`);
        
        // Remove existing popup if it exists
        if (emailPopup) {
            emailPopup.remove();
        }
        
        // Check if the input has a value
        if (!input.value.trim()) {
            return;
        }
        
        // Simple email validation
        const emailRegex = /\S+@\S+\.\S+/;
        if (!emailRegex.test(input.value)) {
            // Create popup
            const popup = document.createElement('div');
            popup.id = `email-popup-${fieldName}`;
            popup.className = 'alert alert-warning position-relative';
            popup.style.marginTop = '5px';
            popup.style.padding = '10px';
            popup.style.borderLeft = '4px solid #f0ad4e';
            
            // Add exclamation icon
            const icon = document.createElement('span');
            icon.className = 'bi bi-exclamation-triangle-fill me-2';
            popup.appendChild(icon);
            
            // Add message
            popup.appendChild(document.createTextNode(`Please include an '@' in the email address. '${input.value}' is missing an '@'.`));
            
            // Insert popup after input
            input.parentNode.insertBefore(popup, input.nextSibling);
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const assetCustomFields = {!! json_encode($assetCustomFields) !!};
        const validationErrors = {!! $errors->inventoryForm ? $errors->inventoryForm->toJson() : '{}' !!};
        const oldValues = {!! json_encode(old()) !!};
        const inventoryItemCustomFields = {!! json_encode(is_string($inventoryItem->custom_fields) ? json_decode($inventoryItem->custom_fields, true) : $inventoryItem->custom_fields) !!};

        function renderAssetCustomFields() {
            const container = document.getElementById('asset-fields-container');
            
            if (assetCustomFields.length === 0) {
                return;
            }
    
            const rowDiv = document.createElement('div');
            rowDiv.className = 'row';
            container.appendChild(rowDiv);
            
            assetCustomFields.forEach(field => {
                const colDiv = document.createElement('div');
                colDiv.className = 'col-md-6';
                rowDiv.appendChild(colDiv);
                
                const fieldGroup = document.createElement('div');
                fieldGroup.className = 'form-group mb-3';
                colDiv.appendChild(fieldGroup);
                
                const label = document.createElement('label');
                label.innerHTML = field.name + (field.is_required ? '<span class="text-danger"> *</span>' : '');
                fieldGroup.appendChild(label);
                
                let input;
                const errorKey = `custom_fields.${field.name}`;
                const fileErrorKey = `custom_fields_files.${field.name}`;

                // Get value from old data first, then from inventory item's custom fields
                const oldValue = oldValues.custom_fields && oldValues.custom_fields[field.name] ? 
                    oldValues.custom_fields[field.name] : 
                    (inventoryItemCustomFields && inventoryItemCustomFields[field.name] ? 
                        inventoryItemCustomFields[field.name] : null);
                
                switch(field.type) {
                    case 'Text':
                        input = document.createElement('input');
                        
                        // Special handling for Email type
                        if (field.text_type === 'Email') {
                            input.type = 'text'; // Use text instead of email to prevent browser validation
                            
                            // Add event listener to validate email format on input
                            input.addEventListener('input', function() {
                                validateEmail(this, field.name);
                            });
                            
                            input.name = `custom_fields[${field.name}]`;
                            input.placeholder = `Enter ${field.name}`;
                            
                            // Set old value for email inputs
                            if (oldValue !== null) {
                                // Handle both string and object values
                                input.value = typeof oldValue === 'object' ? 
                                    (oldValue.original_name || JSON.stringify(oldValue)) : 
                                    oldValue;
                                
                                // Trigger validation for pre-filled values
                                setTimeout(() => validateEmail(input, field.name), 100);
                            }
                        } else if (field.text_type === 'Image') {
                            input.type = 'file';
                            input.name = `custom_fields_files[${field.name}]`;
                            input.accept = 'image/*';
                            input.placeholder = `Enter ${field.name}`;
                            
                            // If there's an existing file, show its name
                            if (oldValue && typeof oldValue === 'object' && oldValue.original_name) {
                                const fileInfo = document.createElement('div');
                                fileInfo.className = 'text-info mt-1';
                                fileInfo.textContent = `Current file: ${oldValue.original_name}`;
                                fieldGroup.appendChild(fileInfo);
                            }
                        } else {
                            input.type = field.text_type ? field.text_type.toLowerCase() : 'text';
                            input.name = `custom_fields[${field.name}]`;
                            input.placeholder = `Enter ${field.name}`;
                            
                            // Set old value for text inputs
                            if (oldValue !== null) {
                                // Handle both string and object values
                                input.value = typeof oldValue === 'object' ? 
                                    (oldValue.original_name || JSON.stringify(oldValue)) : 
                                    oldValue;
                            }
                        }
                        break;
                    
                    case 'Select':
                        input = document.createElement('select');
                        input.name = `custom_fields[${field.name}]`;
                        
                        const defaultOption = document.createElement('option');
                        defaultOption.value = '';
                        defaultOption.textContent = 'Select an option';
                        defaultOption.disabled = true;
                        input.appendChild(defaultOption);
                        
                        let selectOptions = typeof field.options === 'string' 
                            ? JSON.parse(field.options) 
                            : field.options;
                        
                        if (selectOptions && Array.isArray(selectOptions)) {
                            selectOptions.forEach(option => {
                                if (option) {
                                    const optionElement = document.createElement('option');
                                    optionElement.value = option;
                                    optionElement.textContent = option;
                                    // Set selected if matches old value
                                    if (oldValue === option) {
                                        optionElement.selected = true;
                                    }
                                    input.appendChild(optionElement);
                                }
                            });
                        }
                        break;
                    
                    case 'Checkbox':
                        input = document.createElement('div');
                        input.className = 'checkbox-container';
                        
                        let checkboxOptions = typeof field.options === 'string' 
                            ? JSON.parse(field.options) 
                            : field.options;
                        
                        if (checkboxOptions && Array.isArray(checkboxOptions)) {
                            checkboxOptions.forEach(option => {
                                if (option) {
                                    const checkDiv = document.createElement('div');
                                    checkDiv.className = 'form-check';
                                    
                                    const checkbox = document.createElement('input');
                                    checkbox.type = 'checkbox';
                                    checkbox.className = 'form-check-input';
                                    checkbox.name = `custom_fields[${field.name}][]`;
                                    checkbox.value = option;
                                    
                                    // Check if this option was previously selected
                                    if (Array.isArray(oldValue) && oldValue.includes(option)) {
                                        checkbox.checked = true;
                                    }
                                    
                                    const checkLabel = document.createElement('label');
                                    checkLabel.className = 'form-check-label';
                                    checkLabel.textContent = option;
                                    
                                    checkDiv.appendChild(checkbox);
                                    checkDiv.appendChild(checkLabel);
                                    input.appendChild(checkDiv);
                                }
                            });
                        }
                        break;
                    
                    case 'Radio':
                        input = document.createElement('div');
                        input.className = 'radio-container';
                        
                        let radioOptions = typeof field.options === 'string' 
                            ? JSON.parse(field.options) 
                            : field.options;
                        
                        if (radioOptions && Array.isArray(radioOptions)) {
                            radioOptions.forEach(option => {
                                if (option) {
                                    const radioDiv = document.createElement('div');
                                    radioDiv.className = 'form-check';
                                    
                                    const radio = document.createElement('input');
                                    radio.type = 'radio';
                                    radio.className = 'form-check-input';
                                    radio.name = `custom_fields[${field.name}]`;
                                    radio.value = option;
                                    
                                    // Set checked if matches old value
                                    if (oldValue === option) {
                                        radio.checked = true;
                                    }
                                    
                                    const radioLabel = document.createElement('label');
                                    radioLabel.className = 'form-check-label';
                                    radioLabel.textContent = option;
                                    
                                    radioDiv.appendChild(radio);
                                    radioDiv.appendChild(radioLabel);
                                    input.appendChild(radioDiv);
                                }
                            });
                        }
                        break;
                    
                    default:
                        input = document.createElement('input');
                        input.type = 'text';
                        input.name = `custom_fields[${field.name}]`;
                        input.placeholder = `Enter ${field.name}`;
                        // Set old value for default inputs
                        if (oldValue !== null) {
                            input.value = oldValue;
                        }
                }
                
                // Apply form control class and handle validation
                if (input && input.tagName && input.tagName !== 'DIV') {
                    input.className = 'form-control mb-3';
                    input.required = false;
                }
                
                // Append input to field group
                fieldGroup.appendChild(input);
                
                // Error handling
                let errorMessages = [];
                if (validationErrors[errorKey]) {
                    errorMessages = errorMessages.concat(validationErrors[errorKey]);
                }
                if (validationErrors[fileErrorKey]) {
                    errorMessages = errorMessages.concat(validationErrors[fileErrorKey]);
                }
                
                if (errorMessages.length > 0) {
                    const errorSpan = document.createElement('small');
                    errorSpan.className = 'text-danger';
                    errorSpan.textContent = errorMessages[0];
                    fieldGroup.appendChild(errorSpan);
                }
            });
        }
    
        // Call the function to render fields
        renderAssetCustomFields();
    
        // Load category-specific custom fields based on the selected category
        loadCategoryCustomFields(document.getElementById('category_select').value);
    });
</script>

<script>
    // Add this to your edit.blade.php file
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category_select');
        const dynamicFieldsContainer = document.getElementById('dynamic-fields-container');
        
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            if (categoryId) {
                loadCategoryCustomFields(categoryId);
            } else {
                // Clear the container if no category is selected
                dynamicFieldsContainer.innerHTML = '';
            }
        });
        
        function loadCategoryCustomFields(categoryId) {
            if (!categoryId) return;
            
            // Use your existing route
            fetch(`/inventory/get-category-fields/${categoryId}`)
                .then(response => response.json())
                .then(customFields => {
                    renderCategoryCustomFields(customFields);
                })
                .catch(error => {
                    console.error('Error fetching category fields:', error);
                });
        }
        
        function renderCategoryCustomFields(customFields) {
            dynamicFieldsContainer.innerHTML = '';
            
            // Check if customFields is an error or empty
            if (!customFields || customFields.error || customFields.length === 0) {
                console.log('No custom fields to render or error occurred');
                return;
            }
            
            console.log('Rendering custom fields:', customFields);  
            
            const rowDiv = document.createElement('div');
            rowDiv.className = 'row';
            dynamicFieldsContainer.appendChild(rowDiv);
            
            // Get validation errors from the page (if any)
            const validationErrors = {!! $errors->inventoryForm ? $errors->inventoryForm->toJson() : '{}' !!};
            
            // Get old values and inventory item's custom fields
            const oldValues = {!! json_encode(old()) !!};
            const inventoryItemCustomFields = {!! json_encode(is_string($inventoryItem->custom_fields) ? json_decode($inventoryItem->custom_fields, true) : $inventoryItem->custom_fields) !!};
            
            // Handle array of custom field objects
            customFields.forEach(field => {
                const colDiv = document.createElement('div');
                colDiv.className = 'col-md-6';
                rowDiv.appendChild(colDiv);
                
                const fieldGroup = document.createElement('div');
                fieldGroup.className = 'form-group mb-3';
                colDiv.appendChild(fieldGroup);
                
                const label = document.createElement('label');
                label.textContent = field.name;
                if (field.is_required) {
                    const requiredSpan = document.createElement('span');
                    requiredSpan.className = 'text-danger';
                    requiredSpan.textContent = ' *';
                    label.appendChild(requiredSpan);
                }
                fieldGroup.appendChild(label);
                
                // Create input based on field type
                let input;
                
                // Get value from old data first, then from inventory item's custom fields
                const oldValue = oldValues.custom_fields && oldValues.custom_fields[field.name] ? 
                    oldValues.custom_fields[field.name] : 
                    (inventoryItemCustomFields && inventoryItemCustomFields[field.name] ? 
                        inventoryItemCustomFields[field.name] : null);
                
                switch(field.type) {
                    case 'Text':
                        input = document.createElement('input');
                        
                        // Check if this is an email field and handle special validation
                        if (field.text_type === 'Email') {
                            input.type = 'text'; // Use text instead of email to prevent browser validation
                            
                            // Add event listener to validate email format on input
                            input.addEventListener('input', function() {
                                validateEmail(this, field.name);
                            });
                        } else {
                            input.type = field.text_type ? field.text_type.toLowerCase() : 'text';
                        }
                        
                        input.name = `custom_fields[${field.name}]`;
                        input.className = 'form-control';
                        input.placeholder = `Enter ${field.name}`;
                        
                        // Set old value if exists
                        if (oldValue !== null) {
                            // Handle both string and object values
                            input.value = typeof oldValue === 'object' ? 
                                (oldValue.original_name || JSON.stringify(oldValue)) : 
                                oldValue;
                            
                            // Trigger validation for pre-filled email values
                            if (field.text_type === 'Email') {
                                setTimeout(() => validateEmail(input, field.name), 100);
                            }
                        }
                        break;
                        
                    case 'Select':
                        input = document.createElement('select');
                        input.name = `custom_fields[${field.name}]`;
                        input.className = 'form-control';
                        
                        const defaultOption = document.createElement('option');
                        defaultOption.value = '';
                        defaultOption.textContent = `Select ${field.name}`;
                        defaultOption.disabled = true;
                        input.appendChild(defaultOption);
                        
                        // Parse options from JSON string if needed
                        let options;
                        try {
                            options = typeof field.options === 'string' ? 
                                JSON.parse(field.options) : field.options;
                        } catch (e) {
                            console.error('Error parsing options:', e);
                            options = [];
                        }
                        
                        if (options && Array.isArray(options)) {
                            options.forEach(option => {
                                const optElement = document.createElement('option');
                                optElement.value = option;
                                optElement.textContent = option;
                                
                                // Set selected if matches old value
                                if (oldValue === option) {
                                    optElement.selected = true;
                                }
                                
                                input.appendChild(optElement);
                            });
                        }
                        break;
                        
                    case 'Checkbox':
                    case 'Radio':
                        input = document.createElement('div');
                        
                        // Parse options from JSON string if needed
                        let inputOptions;
                        try {
                            inputOptions = typeof field.options === 'string' ? 
                                JSON.parse(field.options) : field.options;
                        } catch (e) {
                            console.error('Error parsing options:', e);
                            inputOptions = [];
                        }
                        
                        if (inputOptions && Array.isArray(inputOptions)) {
                            inputOptions.forEach(option => {
                                const wrapper = document.createElement('div');
                                wrapper.className = 'form-check';
                                
                                const optInput = document.createElement('input');
                                optInput.type = field.type.toLowerCase();
                                optInput.className = 'form-check-input';
                                optInput.name = field.type === 'Checkbox' ? 
                                    `custom_fields[${field.name}][]` : 
                                    `custom_fields[${field.name}]`;
                                optInput.value = option;
                                
                                // Check if this option was previously selected
                                if (field.type === 'Checkbox') {
                                    if (Array.isArray(oldValue) && oldValue.includes(option)) {
                                        optInput.checked = true;
                                    }
                                } else if (oldValue === option) {
                                    optInput.checked = true;
                                }
                                
                                const optLabel = document.createElement('label');
                                optLabel.className = 'form-check-label';
                                optLabel.textContent = option;
                                
                                wrapper.appendChild(optInput);
                                wrapper.appendChild(optLabel);
                                input.appendChild(wrapper);
                            });
                        }
                        break;
                        
                    default:
                        // Default to text input
                        input = document.createElement('input');
                        input.type = 'text';
                        input.name = `custom_fields[${field.name}]`;
                        input.className = 'form-control';
                        input.placeholder = `Enter ${field.name}`;
                        
                        // Set old value if exists
                        if (oldValue !== null) {
                            input.value = oldValue;
                        }
                }
                
                // Remove required attribute - we'll handle validation server-side
                if (input.tagName !== 'DIV') {
                    input.required = false;
                }
                
                fieldGroup.appendChild(input);
                
                // Add error message display if needed
                const errorKey = `custom_fields.${field.name}`;
                if (validationErrors[errorKey]) {
                    const errorSpan = document.createElement('small');
                    errorSpan.className = 'text-danger';
                    errorSpan.textContent = validationErrors[errorKey][0];
                    fieldGroup.appendChild(errorSpan);
                }
            });
        }
        
        // Trigger change event if category is preselected
        if (categorySelect.value) {
            categorySelect.dispatchEvent(new Event('change'));
        }
    });
</script>

<script>
    document.getElementById('user_id').addEventListener('change', function() {
        var userSelect = this;
        var departmentSelect = document.getElementById('department_id');
        
        // Clear the department select if no user is selected
        if (userSelect.value === '') {
            departmentSelect.selectedIndex = 0; // Reset to "Select a Department"
            departmentSelect.disabled = false; // Enable the department select
            return;
        }
        
        // Get the selected option
        var selectedOption = userSelect.options[userSelect.selectedIndex];
        var departmentId = selectedOption.getAttribute('data-department');

        // Set the department select to the user's department
        if (departmentId) {
            departmentSelect.value = departmentId;
            departmentSelect.disabled = true; // Disable the department select
        } else {
            departmentSelect.selectedIndex = 0; // Reset to "Select a Department"
            departmentSelect.disabled = false; // Enable the department select
        }
    });
</script>

<script>
    // Image preview functionality
    document.querySelector('input[name="asset_image"]').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview-container').style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
</script>

@endsection