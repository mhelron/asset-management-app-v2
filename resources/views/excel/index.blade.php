@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Import/Export Inventory</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Import/Export</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Display import errors if any -->
    @if(session('import_errors'))
        <div class="alert alert-warning">
            <h5>Import Errors:</h5>
            <ul>
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Template Generation Card -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-file-excel me-1"></i>
                    Generate Import Template
                </div>
                <div class="card-body">
                    <p>Generate an Excel template based on the selected category. The template will include all custom fields configured for that category.</p>
                    
                    <form action="{{ route('excel.generate-template') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Generate Template</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Import Card -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-file-import me-1"></i>
                    Import Inventory Items
                </div>
                <div class="card-body">
                    <p>Import inventory items from an Excel file. Make sure to use a template generated from this system to ensure compatibility.</p>
                    
                    <form action="{{ route('excel.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="import_file" class="form-label">Select Excel File</label>
                            <input class="form-control" type="file" id="import_file" name="import_file" accept=".xlsx" required>
                            <div class="form-text">Only .xlsx files are supported</div>
                        </div>
                        <button type="submit" class="btn btn-success">Import Data</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Export Card -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-file-export me-1"></i>
                    Export Inventory Items
                </div>
                <div class="card-body">
                    <p>Export inventory items from a specific category to an Excel file. The export will include all custom fields.</p>
                    
                    <form action="{{ route('excel.export') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="export_category_id" class="form-label">Category</label>
                            <select class="form-select" id="export_category_id" name="export_category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info">Export Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i>
            Import/Export Guidelines
        </div>
        <div class="card-body">
            <h5>How to use the import/export functionality:</h5>
            <ol>
                <li>First, generate a template for the specific category you want to import items for.</li>
                <li>Fill in the template with your data. Fields marked with an asterisk (*) are required.</li>
                <li>For dropdown fields (like departments, locations, and users), use only the values that appear in the dropdown lists.</li>
                <li>When importing, use only templates generated from this system to ensure compatibility.</li>
                <li>If you encounter errors during import, check the error messages displayed at the top of this page.</li>
            </ol>
            
            <h5>Notes on Custom Fields:</h5>
            <ul>
                <li>Custom fields specific to the selected category will be included in the template.</li>
                <li>Required custom fields must be filled in the Excel file.</li>
                <li>For checkbox-type custom fields, use comma-separated values when multiple options are selected.</li>
                <li>Date fields should be entered in YYYY-MM-DD format.</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Add any JavaScript needed for the import/export page
    $(document).ready(function() {
        // Example: Hide success alert after 5 seconds
        setTimeout(function() {
            $('.alert-success').fadeOut('slow');
        }, 5000);
    });
</script>
@endsection 