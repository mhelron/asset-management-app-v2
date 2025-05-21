@extends('layouts.app')
@section('content')
<style>
    body {
        background-color: #f2f5f9;
    }
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
                <h1 class="m-0 fw-bold">Edit Component</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('components.index') }}">Components</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Component</li>
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
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('components.index') }}" class="btn btn-danger"><i class="bi bi-arrow-return-left me-2"></i>Back</a>
                </div>
                <!-- Edit Component Form -->
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <h5 class="m-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Component</h5>
                    </div>
                    <div class="card-body form-container p-4">
                        <form action="{{ route('components.update', $component->id) }}" method="POST" enctype="multipart/form-data" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- First Column -->
                                <div class="col-md-6">
                                    <!-- Component Name -->
                                    <div class="form-group mb-3">
                                        <label>Component Name<span class="text-danger"> *</span></label>
                                        <input type="text" name="component_name" value="{{ old('component_name', $component->component_name) }}" class="form-control" placeholder="Enter component name">
                                        @error('component_name', 'componentForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Category Dropdown -->
                                    <div class="form-group mb-3">
                                        <label>Category<span class="text-danger"> *</span></label>
                                        <select name="category_id" id="category_id" class="form-select" onchange="loadCategoryFields(this.value)">
                                            <option value="" disabled>Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $component->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id', 'componentForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Serial No -->
                                    <div class="form-group mb-3">
                                        <label>Serial No<span class="text-danger"> *</span></label>
                                        <input type="text" name="serial_no" value="{{ old('serial_no', $component->serial_no) }}" class="form-control" placeholder="Enter serial number">
                                        @error('serial_no', 'componentForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Model No -->
                                    <div class="form-group mb-3">
                                        <label>Model No<span class="text-danger"> *</span></label>
                                        <input type="text" name="model_no" value="{{ old('model_no', $component->model_no) }}" class="form-control" placeholder="Enter model number">
                                        @error('model_no', 'componentForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Associated Asset -->
                                    <div class="form-group mb-3">
                                        <label>Associated Asset</label>
                                        <select name="inventory_id" class="form-select">
                                            <option value="">None (Independent Component)</option>
                                            @foreach($assets as $asset)
                                                <option value="{{ $asset->id }}" {{ (old('inventory_id', $component->inventory_id) == $asset->id) ? 'selected' : '' }}>
                                                    {{ $asset->item_name }} ({{ $asset->asset_tag }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('inventory_id', 'componentForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Second Column -->
                                <div class="col-md-6">
                                    <!-- Manufacturer -->
                                    <div class="form-group mb-3">
                                        <label>Manufacturer<span class="text-danger"> *</span></label>
                                        <input type="text" name="manufacturer" value="{{ old('manufacturer', $component->manufacturer) }}" class="form-control" placeholder="Enter manufacturer">
                                        @error('manufacturer', 'componentForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Assigned User Dropdown -->
                                    <div class="form-group mb-3">
                                        <label>Assigned To</label>
                                        <select name="users_id" class="form-select">
                                            <option value="">Not Assigned</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('users_id', $component->users_id) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->first_name }} {{ $user->last_name }} ({{ $user->department->name ?? 'No Department' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('users_id', 'componentForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Date Purchased -->
                                    <div class="form-group mb-3">
                                        <label>Date Purchased<span class="text-danger"> *</span></label>
                                        <input type="date" name="date_purchased" value="{{ old('date_purchased', $component->date_purchased) }}" class="form-control">
                                        @error('date_purchased', 'componentForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Purchased From -->
                                    <div class="form-group mb-3">
                                        <label>Purchased From<span class="text-danger"> *</span></label>
                                        <input type="text" name="purchased_from" value="{{ old('purchased_from', $component->purchased_from) }}" class="form-control" placeholder="Enter purchased from">
                                        @error('purchased_from', 'componentForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Log Note -->
                                    <div class="form-group mb-3">
                                        <label>Log Note</label>
                                        <textarea name="log_note" class="form-control" rows="5" placeholder="Enter log note">{{ old('log_note', $component->log_note) }}</textarea>
                                        @error('log_note', 'componentForm')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Fields Section -->
                            <!-- Component-specific custom fields -->
                            <div id="component-fields-container"></div>
                            
                            <!-- Category-specific custom fields -->
                            <div id="category-custom-fields"></div>

                            <!-- Submit button -->
                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-dark"><i class="bi bi-save me-2"></i>Update Component</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const componentCustomFields = {!! json_encode($componentCustomFields) !!};
    const validationErrors = {!! $errors->componentForm ? $errors->componentForm->toJson() : '{}' !!};
    // Get old values directly from Laravel's old() helper
    const oldValues = {!! json_encode(old()) !!};
    // Get component custom fields from PHP
    const componentFieldValues = @json($component->custom_fields ?? []);

    function renderComponentCustomFields() {
        const container = document.getElementById('component-fields-container');
        
        if (componentCustomFields.length === 0) {
            return;
        }

        // Create a row div to contain all fields
        const rowDiv = document.createElement('div');
        rowDiv.className = 'row';
        container.appendChild(rowDiv);
        
        componentCustomFields.forEach(field => {
            // Create column div for each field
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

            // Determine field value priority: old input > existing component value > null
            const oldValue = oldValues.custom_fields && oldValues.custom_fields[field.name] 
                ? oldValues.custom_fields[field.name] 
                : (componentFieldValues[field.name] || null);
            
            const oldFileValue = oldValues.custom_fields_files && oldValues.custom_fields_files[field.name]
                ? oldValues.custom_fields_files[field.name] : null;

            switch(field.type) {
                case 'Text':
                    input = document.createElement('input');
                    
                    // Special handling for Email type
                    if (field.text_type === 'Email') {
                        input.type = 'email';
                    } else if (field.text_type === 'Image') {
                        input.type = 'file';
                        input.name = `custom_fields_files[${field.name}]`;
                        input.accept = 'image/*';
                    } else {
                        input.type = field.text_type ? field.text_type.toLowerCase() : 'text';
                        input.name = `custom_fields[${field.name}]`;
                    }
                    
                    input.placeholder = `Enter ${field.name}`;
                    
                    // Set name for non-image fields
                    if (field.text_type !== 'Image') {
                        input.name = `custom_fields[${field.name}]`;
                        // Set value for text inputs
                        if (oldValue !== null) {
                            input.value = oldValue;
                        }
                    }

                    // If there's an existing file for this field, show file info
                    if (typeof oldValue === 'object' && oldValue && oldValue.path && oldValue.original_name) {
                        const fileInfo = document.createElement('div');
                        fileInfo.className = 'mt-2';
                        fileInfo.innerHTML = `<small class="text-muted">Current file: 
                            <a href="/storage/${oldValue.path}" target="_blank">${oldValue.original_name}</a>
                        </small>`;
                        fieldGroup.appendChild(input);
                        fieldGroup.appendChild(fileInfo);
                        input = null; // So we don't append again below
                    }
                    break;
                
                case 'Number':
                    input = document.createElement('input');
                    input.type = 'number';
                    input.name = `custom_fields[${field.name}]`;
                    input.placeholder = `Enter ${field.name}`;
                    
                    // Set value
                    if (oldValue !== null) {
                        input.value = oldValue;
                    }
                    break;
                
                case 'Select':
                    input = document.createElement('select');
                    input.name = `custom_fields[${field.name}]`;
                    
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Select an option';
                    defaultOption.selected = true;
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
                                // Set selected if matches value
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
                
                case 'File':
                    const fileContainer = document.createElement('div');
                    
                    input = document.createElement('input');
                    input.type = 'file';
                    input.name = `custom_fields_files[${field.name}]`;
                    input.className = 'form-control';
                    
                    fileContainer.appendChild(input);
                    
                    // If there's an existing file, show it
                    if (typeof oldValue === 'object' && oldValue && oldValue.path && oldValue.original_name) {
                        const fileInfo = document.createElement('div');
                        fileInfo.className = 'mt-2';
                        
                        const fileLink = document.createElement('a');
                        fileLink.href = `/storage/${oldValue.path}`;
                        fileLink.target = '_blank';
                        fileLink.textContent = oldValue.original_name;
                        
                        const fileText = document.createElement('small');
                        fileText.className = 'text-muted';
                        fileText.textContent = 'Current file: ';
                        fileText.appendChild(fileLink);
                        
                        fileInfo.appendChild(fileText);
                        fileContainer.appendChild(fileInfo);
                    }
                    
                    fieldGroup.appendChild(fileContainer);
                    input = null; // Don't append again below
                    break;
                
                default:
                    input = document.createElement('input');
                    input.type = 'text';
                    input.name = `custom_fields[${field.name}]`;
                    input.placeholder = `Enter ${field.name}`;
                    // Set value for default inputs
                    if (oldValue !== null) {
                        input.value = oldValue;
                    }
            }
            
            // Apply form control class but NEVER set required attribute
            if (input && input.tagName && input.tagName !== 'DIV') {
                input.className = 'form-control';
                // Explicitly set required to false for server-side validation
                input.required = false;
            }
            
            // Append input to field group if it's not already appended
            if (input) fieldGroup.appendChild(input);
            
            // Check for errors
            const errorMessages = [];
            if (validationErrors[errorKey]) {
                errorMessages.push(validationErrors[errorKey]);
            }
            if (validationErrors[fileErrorKey]) {
                errorMessages.push(validationErrors[fileErrorKey]);
            }
            
            // Add error message if exists
            if (errorMessages.length > 0) {
                const errorSpan = document.createElement('small');
                errorSpan.className = 'text-danger';
                // If error message is an array, take first item
                const errorText = Array.isArray(errorMessages[0]) ? errorMessages[0][0] : errorMessages[0];
                errorSpan.textContent = errorText;
                fieldGroup.appendChild(errorSpan);
            }
        });
    }

    // Call the function to render fields
    renderComponentCustomFields();
});

function loadCategoryFields(categoryId) {
    if (categoryId) {
        fetch(`/components/get-category-fields/${categoryId}`)
            .then(response => response.json())
            .then(customFields => {
                const container = document.getElementById('category-custom-fields');
                
                // Clear previous fields
                container.innerHTML = '';
                
                if (!customFields || customFields.error || customFields.length === 0) {
                    return;
                }
                
                // Get validation errors and old values
                const validationErrors = {!! $errors->componentForm ? $errors->componentForm->toJson() : '{}' !!};
                const oldValues = {!! json_encode(old()) !!};
                // Get component custom fields from PHP
                const componentFieldValues = @json($component->custom_fields ?? []);
                
                // Create a row div to contain all fields
                const rowDiv = document.createElement('div');
                rowDiv.className = 'row';
                container.appendChild(rowDiv);
                
                // Handle array of custom field objects
                customFields.forEach(field => {
                    // Create column div for each field
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
                    
                    // Determine field value priority: old input > existing component value > null
                    const oldValue = oldValues.custom_fields && oldValues.custom_fields[field.name] 
                        ? oldValues.custom_fields[field.name] 
                        : (componentFieldValues[field.name] || null);
                    
                    // Create error key
                    const errorKey = `custom_fields.${field.name}`;
                    const fileErrorKey = `custom_fields_files.${field.name}`;
                    
                    switch(field.type) {
                        case 'Text':
                            input = document.createElement('input');
                            input.type = field.text_type ? field.text_type.toLowerCase() : 'text';
                            input.name = `custom_fields[${field.name}]`;
                            input.className = 'form-control';
                            input.placeholder = `Enter ${field.name}`;
                            
                            // Set value if exists
                            if (oldValue !== null) {
                                input.value = oldValue;
                            }
                            break;
                            
                        case 'Number':
                            input = document.createElement('input');
                            input.type = 'number';
                            input.name = `custom_fields[${field.name}]`;
                            input.className = 'form-control';
                            input.placeholder = `Enter ${field.name}`;
                            
                            // Set value
                            if (oldValue !== null) {
                                input.value = oldValue;
                            }
                            break;
                            
                        case 'Select':
                            input = document.createElement('select');
                            input.name = `custom_fields[${field.name}]`;
                            input.className = 'form-control';
                            
                            const defaultOption = document.createElement('option');
                            defaultOption.value = '';
                            defaultOption.textContent = `Select ${field.name}`;
                            defaultOption.selected = true;
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
                                    
                                    // Set selected if matches value
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
                            
                        case 'File':
                            const fileContainer = document.createElement('div');
                            
                            input = document.createElement('input');
                            input.type = 'file';
                            input.name = `custom_fields_files[${field.name}]`;
                            input.className = 'form-control';
                            
                            fileContainer.appendChild(input);
                            
                            // If there's an existing file, show it
                            if (typeof oldValue === 'object' && oldValue && oldValue.path && oldValue.original_name) {
                                const fileInfo = document.createElement('div');
                                fileInfo.className = 'mt-2';
                                
                                const fileLink = document.createElement('a');
                                fileLink.href = `/storage/${oldValue.path}`;
                                fileLink.target = '_blank';
                                fileLink.textContent = oldValue.original_name;
                                
                                const fileText = document.createElement('small');
                                fileText.className = 'text-muted';
                                fileText.textContent = 'Current file: ';
                                fileText.appendChild(fileLink);
                                
                                fileInfo.appendChild(fileText);
                                fileContainer.appendChild(fileInfo);
                            }
                            
                            fieldGroup.appendChild(fileContainer);
                            input = null; // Don't append again below
                            break;
                            
                        default:
                            // Default to text input
                            input = document.createElement('input');
                            input.type = 'text';
                            input.name = `custom_fields[${field.name}]`;
                            input.className = 'form-control';
                            input.placeholder = `Enter ${field.name}`;
                            
                            // Set value if exists
                            if (oldValue !== null) {
                                input.value = oldValue;
                            }
                    }
                    
                    // Never set required attribute - we'll handle validation server-side
                    if (input && input.tagName !== 'DIV') {
                        input.className = 'form-control';
                        input.required = false; // Explicitly set to false
                    }
                    
                    if (input) fieldGroup.appendChild(input);
                    
                    // Add error message display if needed
                    const errorMessages = [];
                    if (validationErrors[errorKey]) {
                        errorMessages.push(validationErrors[errorKey]);
                    }
                    if (validationErrors[fileErrorKey]) {
                        errorMessages.push(validationErrors[fileErrorKey]);
                    }
                    
                    if (errorMessages.length > 0) {
                        const errorSpan = document.createElement('small');
                        errorSpan.className = 'text-danger';
                        // If error message is an array, take first item
                        const errorText = Array.isArray(errorMessages[0]) ? errorMessages[0][0] : errorMessages[0];
                        errorSpan.textContent = errorText;
                        fieldGroup.appendChild(errorSpan);
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching custom fields:', error);
            });
    } else {
        document.getElementById('category-custom-fields').innerHTML = '';
    }
}

// Load category fields if a category is already selected
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    if (categorySelect.value) {
        loadCategoryFields(categorySelect.value);
    }
});
</script>
@endsection