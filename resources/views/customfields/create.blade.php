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

<!-- Content Header -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0 fw-bold">Add Custom Field</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customfields.index') }}">Custom Fields</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Custom Field</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('customfields.index') }}" class="btn btn-danger">
                        <i class="bi bi-arrow-return-left me-2"></i>Back
                    </a>
                </div>

                <!-- Add Custom Field Form -->
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <h5 class="m-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Add New Custom Field</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('customfields.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <!-- Field Name -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label required-label">Field Name</label>
                                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Enter field name">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Required Select -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label required-label">Required or Optional?</label>
                                        <select name="is_required" class="form-select @error('is_required') is-invalid @enderror">
                                            <option value="" disabled {{ old('is_required') ? '' : 'selected' }}>Select an answer</option>
                                            <option value="0" {{ old('is_required') == '0' ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('is_required') == '1' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                        @error('is_required')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Where to apply the custom field -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label required-label">Apply to</label>
                                    <div class="d-flex flex-wrap mt-2">
                                        <div class="form-check me-4 mb-2">
                                            <input class="form-check-input @error('applies_to') is-invalid @enderror" type="checkbox" name="applies_to[]" value="Category" id="category-check" {{ is_array(old('applies_to')) && in_array('Category', old('applies_to')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="category-check">
                                                Category
                                            </label>
                                        </div>
                                        <div class="form-check me-4 mb-2">
                                            <input class="form-check-input @error('applies_to') is-invalid @enderror" type="checkbox" name="applies_to[]" value="Asset" id="asset-check" {{ is_array(old('applies_to')) && in_array('Asset', old('applies_to')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="asset-check">
                                                Asset
                                            </label>
                                        </div>
                                    </div>
                                    @error('applies_to')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Description -->
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label required-label">Description</label>
                                        <textarea type="text" name="desc" class="form-control @error('desc') is-invalid @enderror" placeholder="Enter description">{{ old('desc') }}</textarea>
                                        @error('desc')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Field Type -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label required-label">Field Type</label>
                                        <select name="type" id="field_type" class="form-select @error('type') is-invalid @enderror">
                                            <option value="" disabled {{ old('type') ? '' : 'selected' }}>Select a field type</option>
                                            <option value="Text" {{ old('type') == 'Text' ? 'selected' : '' }}>Text</option>
                                            <option value="Checkbox" {{ old('type') == 'Checkbox' ? 'selected' : '' }}>Checkbox</option>
                                            <option value="Radio" {{ old('type') == 'Radio' ? 'selected' : '' }}>Radio Button</option>
                                            <option value="Select" {{ old('type') == 'Select' ? 'selected' : '' }}>Select Dropdown</option>
                                        </select>
                                        @error('type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div id="text-type-container" class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label required-label">Text Format</label>
                                        <select name="text_type" class="form-select @error('text_type') is-invalid @enderror" id="text-type-select">
                                            <option value="" disabled {{ old('text_type') ? '' : 'selected' }}>Select a format</option>
                                            <option value="Any" {{ old('text_type') == 'Any' ? 'selected' : '' }}>Any (No Restrictions)</option>
                                            <option value="Email" {{ old('text_type') == 'Email' ? 'selected' : '' }}>Email</option>
                                            <option value="Image" {{ old('text_type') == 'Image' ? 'selected' : '' }}>Image File Path</option>
                                            <option value="Date" {{ old('text_type') == 'Date' ? 'selected' : '' }}>Date</option>
                                            <option value="Alpha-Dash" {{ old('text_type') == 'Alpha-Dash' ? 'selected' : '' }}>Alpha-Dash</option>
                                            <option value="Numeric" {{ old('text_type') == 'Numeric' ? 'selected' : '' }}>Numeric</option>
                                            <option value="Custom" {{ old('text_type') == 'Custom' ? 'selected' : '' }}>Custom Format</option>
                                        </select>
                                        
                                        <div id="text-type-hint" class="text-muted mt-2 small">
                                            <!-- Hints will be dynamically populated here -->
                                        </div>
                                        
                                        @error('text_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Regex Container -->
                            <div id="custom-regex-container" class="col-md-6 mb-3" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label required-label">Custom Regex Pattern</label>
                                    <input type="text" name="custom_regex" class="form-control @error('custom_regex') is-invalid @enderror" 
                                        placeholder="Enter your custom regex pattern" 
                                        value="{{ old('custom_regex') }}"
                                        id="custom-regex-input">
                                    <small class="text-muted">
                                        Example patterns:
                                        <ul class="mb-0">
                                            <li><code>^\d{3}-\d{2}-\d{4}$</code> (SSN format: 123-45-6789)</li>
                                            <li><code>^\d{15}$</code> (IMEI Code: 123456789012345)</li>
                                        </ul>
                                    </small>
                                    @error('custom_regex')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div id="options-container" class="mb-3" style="display: none;">
                                <label class="form-label required-label">Options</label>
                                <div id="options-list">
                                    @php
                                        $oldOptions = old('options', []);
                                    @endphp
                            
                                    @if ($errors->has('options'))
                                        <div class="text-danger small mb-2">{{ $errors->first('options') }}</div>
                                    @endif
                            
                                    @foreach ($oldOptions as $index => $option)
                                        <div class="d-flex mb-2">
                                            <input type="text" name="options[]" class="form-control me-2 @if($errors->has("options.$index")) is-invalid @endif" value="{{ $option }}" placeholder="Enter option">
                                            <button type="button" class="btn btn-danger remove-option d-flex align-items-center">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                        @if ($errors->has("options.$index"))
                                            <div class="text-danger small mb-2">{{ $errors->first("options.$index") }}</div>
                                        @endif
                                    @endforeach
                            
                                    <div class="d-flex mb-2">
                                        <input type="text" name="options[]" class="form-control me-2" placeholder="Enter option">
                                        <button type="button" class="btn btn-success d-flex align-items-center" id="add-option">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit button -->
                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-dark">
                                    <i class="bi bi-plus-lg me-2"></i>Add Custom Field
                                </button>
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
    const fieldTypeSelect = document.getElementById('field_type');
    const textTypeContainer = document.getElementById('text-type-container');
    const textTypeSelect = document.querySelector('select[name="text_type"]');
    const optionsContainer = document.getElementById('options-container');
    const optionsList = document.getElementById('options-list');

    // Retrieve old values and errors
    const oldOptions = @json(old('options', []));
    const optionsErrors = @json($errors->get('options.*'));

    function toggleContainers() {
        const selectedType = fieldTypeSelect.value;

        // Reset text type dropdown if not Text
        if (selectedType !== 'Text') {
            textTypeSelect.selectedIndex = 0;
        }

        // Show text type container only if "Text" is selected
        textTypeContainer.style.display = selectedType === 'Text' ? 'block' : 'none';

        // Show/hide options container based on field type
        if (['Checkbox', 'Radio', 'Select'].includes(selectedType)) {
            optionsContainer.style.display = 'block';
            renderOptions();
        } else {
            optionsContainer.style.display = 'none';
            optionsList.innerHTML = ''; // Clear options completely
        }
    }

    function renderOptions() {
        // Clear existing options
        optionsList.innerHTML = '';

        // If current type is List, Checkbox, Radio, or Select, always add at least one option
        if (['Checkbox', 'Radio', 'Select'].includes(fieldTypeSelect.value)) {
            // If there are old options, render them with error handling
            if (oldOptions.length > 0) {
                oldOptions.forEach((option, index) => {
                    addOption(option || '', optionsErrors[`options.${index}`] || '');
                });
            } else {
                // If no old options exist, add one blank input field
                addOption('', '');
            }

            // Add the "Add" button at the end
            addAddButton();
        }
    }

    function addOption(value = '', error = '') {
        const optionWrapper = document.createElement('div');
        optionWrapper.className = 'option-wrapper d-flex flex-column mb-2';
        optionWrapper.innerHTML = `
            <div class="d-flex align-items-center">
                <input type="text" name="options[]" class="form-control me-2" placeholder="Enter option" value="${value}">
                <button type="button" class="btn btn-danger remove-option d-flex align-items-center">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            ${error ? `<div class="text-danger small">${error}</div>` : ''}
        `;

        // Always insert before the add button (if it exists)
        const addButton = optionsList.querySelector('#add-option-wrapper');
        if (addButton) {
            optionsList.insertBefore(optionWrapper, addButton);
        } else {
            optionsList.appendChild(optionWrapper);
        }
    }

    function addAddButton() {
        // Remove existing add button if any
        const existingAddButton = document.getElementById('add-option-wrapper');
        if (existingAddButton) existingAddButton.remove();

        // Create new add button wrapper
        const addButtonWrapper = document.createElement('div');
        addButtonWrapper.id = 'add-option-wrapper';
        addButtonWrapper.className = 'd-flex justify-content-end mt-2';
        addButtonWrapper.innerHTML = `
            <button type="button" class="btn btn-success d-flex align-items-center" id="add-option">
                <i class="bi bi-plus-lg"></i>
            </button>
        `;
        optionsList.appendChild(addButtonWrapper);

        // Attach event listener
        document.getElementById('add-option').addEventListener('click', function() {
            addOption('', '');
        });
    }

    // Handle option removal
    optionsList.addEventListener('click', function(event) {
        if (event.target.closest('.remove-option')) {
            const optionWrapper = event.target.closest('.option-wrapper');
            optionWrapper.remove();

            // Ensure at least one option input remains
            if (optionsList.querySelectorAll('.option-wrapper').length === 0) {
                addOption('', '');
            }
        }
    });

    // Ensure proper container visibility on load
    toggleContainers();

    // Add change event listener for field type selection
    fieldTypeSelect.addEventListener('change', function() {
        // Reset options when changing field type
        oldOptions.length = 0; // Clear old options
        toggleContainers();
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textTypeSelect = document.getElementById('text-type-select');
        const textTypeHint = document.getElementById('text-type-hint');
        const customRegexContainer = document.getElementById('custom-regex-container');
        const customRegexInput = document.getElementById('custom-regex-input');
    
        // Hint dictionary
        const hints = {
            'Any': 'No input restrictions. Example: "Hello World 123"',
            'Email': 'Must be a valid email address. Example: "user@example.com"',
            'Image': 'File path for images. Example: "/uploads/profile.jpg"',
            'Date': 'Date in YYYY-MM-DD format. Example: "2024-03-27"',
            'Alpha-Dash': 'Allows letters, numbers, underscores, and hyphens. Example: "user-profile_123"',
            'Numeric': 'Only numbers (integer or decimal). Example: "12345" or "3.14"',
            'Custom': 'Define your own validation pattern using regex. Provide a custom validation rule.'
        };
    
        function toggleCustomRegexContainer() {
            const selectedType = textTypeSelect.value;
            
            // Update hint text
            textTypeHint.textContent = hints[selectedType] || '';
            
            // Show/hide custom regex container
            customRegexContainer.style.display = 
                selectedType === 'Custom' ? 'block' : 'none';
        }
    
        // Update hint and show/hide custom regex
        textTypeSelect.addEventListener('change', toggleCustomRegexContainer);
    
        // Check for validation errors or old values
        const hasCustomRegexError = @json($errors->has('custom_regex'));
        const oldTextType = @json(old('text_type'));
        const oldCustomRegex = @json(old('custom_regex'));
    
        // If there's a custom regex error or old custom regex value, show the container
        if (hasCustomRegexError || (oldTextType === 'Custom' && oldCustomRegex)) {
            textTypeSelect.value = 'Custom';
            customRegexContainer.style.display = 'block';
            textTypeHint.textContent = hints['Custom'];
        }
    
        // Trigger initial hint setup
        if (textTypeSelect.value) {
            textTypeHint.textContent = hints[textTypeSelect.value] || '';
        }
    });
</script>

@endsection
