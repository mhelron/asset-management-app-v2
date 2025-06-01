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
    .table thead th {
        border-bottom: 2px solid #e3e6f0;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }
    .nav-tabs .nav-link {
        color: #5a5c69;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #212529;
        font-weight: 600;
        border-bottom: 3px solid #212529;
    }
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0 fw-bold">Custom Field Details</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customfields.index') }}">Custom Fields</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Field Details</li>
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
                    <a href="{{ route('customfields.index') }}" class="btn btn-danger"><i class="bi bi-arrow-return-left me-2"></i>Back</a>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active px-4 py-3" id="field_details" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="false"><i class="bi bi-info-circle me-2"></i>Field Details</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="field_options" data-bs-toggle="tab" data-bs-target="#options" type="button" role="tab" aria-controls="options" aria-selected="false"><i class="bi bi-list-ul me-2"></i>Options</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="field_usage" data-bs-toggle="tab" data-bs-target="#usage" type="button" role="tab" aria-controls="usage" aria-selected="false"><i class="bi bi-diagram-3 me-2"></i>Usage</button>
                            </li>
                        </ul>

                        <div class="tab-content mt-4" id="myTabContent">
                            <!-- Basic Info Tab -->
                            <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="field_details">
                                <div class="row align-items-center">
                                    <!-- Field Icon (Left) -->
                                    <div class="col-md-3 text-center">
                                        <div class="mb-3">
                                            @if($customField->type == 'Text')
                                                <i class="bi bi-input-cursor-text display-1 text-secondary"></i>
                                            @elseif($customField->type == 'Checkbox')
                                                <i class="bi bi-check-square display-1 text-secondary"></i>
                                            @elseif($customField->type == 'Radio')
                                                <i class="bi bi-record-circle display-1 text-secondary"></i>
                                            @elseif($customField->type == 'Select')
                                                <i class="bi bi-menu-down display-1 text-secondary"></i>
                                            @else
                                                <i class="bi bi-card-text display-1 text-secondary"></i>
                                            @endif
                                        </div>
                                        <h4 class="mt-3 fw-bold">{{ $customField->name }}</h4>
                                        <span class="badge bg-dark px-3 py-2 mb-3">{{ $customField->type }}</span>
                                        @if($customField->type == 'Text')
                                            <span class="badge bg-info px-3 py-2 mb-3 ms-2">{{ $customField->text_type }}</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Field Information (Right) -->
                                    <div class="col-md-9">
                                        <table class="table table-striped table-hover">
                                            <tr>
                                                <th width="200">Field Name</th>
                                                <td>{{ $customField->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Description</th>
                                                <td>{{ $customField->desc }}</td>
                                            </tr>
                                            <tr>
                                                <th>Field Type</th>
                                                <td>{{ $customField->type }}</td>
                                            </tr>
                                            @if($customField->type == 'Text')
                                            <tr>
                                                <th>Text Format</th>
                                                <td>{{ $customField->text_type }}</td>
                                            </tr>
                                            @endif
                                            @if($customField->text_type == 'Custom')
                                            <tr>
                                                <th>Custom Regex</th>
                                                <td><code>{{ $customField->custom_regex }}</code></td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th>Required</th>
                                                <td>
                                                    <span class="badge {{ $customField->is_required ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $customField->is_required ? 'Yes' : 'No' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Applies To</th>
                                                <td>
                                                    @foreach($customField->applies_to as $applies)
                                                        <span class="badge bg-secondary me-1">{{ $applies }}</span>
                                                    @endforeach
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Created</th>
                                                <td>{{ $customField->created_at->format('d M Y, h:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Last Updated</th>
                                                <td>{{ $customField->updated_at->format('d M Y, h:i A') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Options Tab -->
                            <div class="tab-pane fade" id="options" role="tabpanel" aria-labelledby="field_options">
                                @if(in_array($customField->type, ['Select', 'Radio', 'Checkbox']) && !empty($customField->options))
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50">#</th>
                                                    <th>Option Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($customField->options as $index => $option)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $option }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        This field type does not have options.
                                    </div>
                                @endif
                            </div>

                            <!-- Usage Tab -->
                            <div class="tab-pane fade" id="usage" role="tabpanel" aria-labelledby="field_usage">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    This custom field can be used with the following:
                                </div>
                                
                                <ul class="list-group">
                                    @foreach($customField->applies_to as $applies)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                @if($applies == 'Asset')
                                                    <i class="bi bi-box-seam me-2"></i>
                                                @elseif($applies == 'Category')
                                                    <i class="bi bi-bookmark me-2"></i>
                                                @elseif($applies == 'Consumable')
                                                    <i class="bi bi-box2-heart me-2"></i>
                                                @elseif($applies == 'Accessory')
                                                    <i class="bi bi-headphones me-2"></i>
                                                @else
                                                    <i class="bi bi-tag me-2"></i>
                                                @endif
                                                {{ $applies }}
                                            </div>
                                            <span class="badge bg-primary rounded-pill">Type</span>
                                        </li>
                                    @endforeach
                                </ul>
                                
                                <div class="mt-4">
                                    <h5 class="mb-3">Input Format</h5>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            @if($customField->type == 'Text')
                                                <div class="mb-3">
                                                    <label class="form-label">{{ $customField->name }}</label>
                                                    <input type="text" class="form-control" placeholder="Enter {{ strtolower($customField->name) }}" 
                                                        {{ $customField->is_required ? 'required' : '' }}>
                                                    @if($customField->text_type == 'Email')
                                                        <small class="text-muted">Must be a valid email address.</small>
                                                    @elseif($customField->text_type == 'Numeric')
                                                        <small class="text-muted">Must be a number.</small>
                                                    @elseif($customField->text_type == 'Date')
                                                        <small class="text-muted">Must be a valid date.</small>
                                                    @elseif($customField->text_type == 'Custom' && $customField->custom_regex)
                                                        <small class="text-muted">Must match pattern: <code>{{ $customField->custom_regex }}</code></small>
                                                    @endif
                                                </div>
                                            @elseif($customField->type == 'Select')
                                                <div class="mb-3">
                                                    <label class="form-label">{{ $customField->name }}</label>
                                                    <select class="form-select" {{ $customField->is_required ? 'required' : '' }}>
                                                        <option value="" selected disabled>Select {{ strtolower($customField->name) }}</option>
                                                        @if(!empty($customField->options))
                                                            @foreach($customField->options as $option)
                                                                <option value="{{ $option }}">{{ $option }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            @elseif($customField->type == 'Checkbox')
                                                <div class="mb-3">
                                                    <label class="form-label d-block">{{ $customField->name }}</label>
                                                    @if(!empty($customField->options))
                                                        @foreach($customField->options as $option)
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="{{ $option }}">
                                                                <label class="form-check-label">{{ $option }}</label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @elseif($customField->type == 'Radio')
                                                <div class="mb-3">
                                                    <label class="form-label d-block">{{ $customField->name }}</label>
                                                    @if(!empty($customField->options))
                                                        @foreach($customField->options as $option)
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="{{ $customField->name }}" value="{{ $option }}" 
                                                                    {{ $customField->is_required ? 'required' : '' }}>
                                                                <label class="form-check-label">{{ $option }}</label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                       
                    </div>
                </div>
                
                <!-- Actions Card -->
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <h5 class="m-0 fw-bold"><i class="bi bi-gear me-2"></i>Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-start gap-2">
                            <a href="{{ route('customfields.edit', $customField->id) }}" class="btn btn-success">
                                <i class="bi bi-pencil-square me-2"></i>Edit Custom Field
                            </a>
                            
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#archiveModal">
                                <i class="bi bi-archive me-2"></i>Archive Custom Field
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="archiveModalLabel">Confirm Archive</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to archive <strong>{{ $customField->name }}</strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('customfields.archive', $customField->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Archive</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection 