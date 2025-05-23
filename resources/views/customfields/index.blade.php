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
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0 fw-bold">Custom Fields</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Custom Fields</li>
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

                <!-- Alert Box for Success -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Alert Box for Errors-->
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-diamond me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-end mb-3 action-buttons">
                    <a href="{{ route('customfields.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg me-2"></i>Add Custom Field</a>
                </div>

                <!-- Custom Fields Table -->
                <div class="card shadow">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold"><i class="bi bi-list-check me-2"></i>Custom Fields List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Field Name</th>
                                        <th scope="col">Where to?</th>
                                        <th scope="col">Field Type</th>
                                        <th scope="col">Field Input Type</th>
                                        <th scope="col">Value/s</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Is Required?</th>
                                        <th scope="col">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($customFields as $field)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong><a href="{{ route('customfields.show', $field->id) }}" class="text-decoration-none">{{ $field->name }}</a></strong></td>
                                            <td>
                                                @foreach($field->applies_to as $applies)
                                                    <span class="badge bg-secondary">{{ $applies }}</span> 
                                                @endforeach
                                            </td>
                                            <td><span class="badge bg-dark">{{ ucfirst($field->type) }}</span></td>
                                            <td>
                                                @if ($field->type === 'Text')
                                                    <span class="badge bg-info">{{ ucfirst($field->text_type) }}</span>
                                                @elseif (in_array($field->type, ['Select', 'Checkbox', 'Radio', 'List']))
                                                    <span class="badge bg-primary">User Input</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($field->type === 'Text')
                                                    <span class="text-muted">User Input</span>
                                                @elseif (in_array($field->type, ['List', 'Checkbox', 'Radio', 'Select']) && !empty($field->options))
                                                    <small>{{ implode(', ', json_decode($field->options, true)) }}</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td><small>{{ Str::limit($field->desc, 30) }}</small></td>
                                            <td>
                                                <span class="badge {{ $field->is_required ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $field->is_required ? 'Required' : 'Optional' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('customfields.show', $field->id) }}" class="btn btn-sm btn-dark me-2" title="View Field"><i class="bi bi-eye"></i></a>
                                                    <a href="{{ route('customfields.edit', $field->id) }}" class="btn btn-sm btn-success me-2" title="Edit Field"><i class="bi bi-pencil-square"></i></a>
                                                    
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#archiveModal" 
                                                        data-id="{{ $field->id }}" data-name="{{ $field->name }}" title="Archive Field">
                                                        <i class="bi bi-archive"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-3">No custom fields found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
                Are you sure you want to archive <strong id="fieldName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="archiveForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Archive</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to Handle Archive Modal -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const archiveButtons = document.querySelectorAll('[data-bs-target="#archiveModal"]');
        const fieldNameElement = document.getElementById('fieldName');
        const archiveForm = document.getElementById('archiveForm');

        archiveButtons.forEach(button => {
            button.addEventListener('click', function() {
                const fieldName = this.getAttribute('data-name');
                const fieldId = this.getAttribute('data-id');
                fieldNameElement.textContent = fieldName;

                // Fix URL construction
                archiveForm.action = "{{ url('custom-fields/archive-custom-field/') }}/" + fieldId;
            });
        });
    });
</script>

@endsection