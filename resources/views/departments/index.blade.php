@extends('layouts.app')

@section('content')

<!-- Debug Information - Remove in production -->
@if(config('app.debug'))
<div class="container mb-4">
    <div class="card bg-light">
        <div class="card-header">Debug Information</div>
        <div class="card-body">
            <h6>Department Data:</h6>
            <ul>
                @foreach($departments as $dept)
                <li>
                    Department: {{ $dept->name }} (ID: {{ $dept->id }})<br>
                    Location ID: {{ $dept->location_id ?? 'null' }}<br>
                    Location Object: {{ $dept->location ? 'Yes - '.$dept->location->name : 'No' }}
                </li>
                @endforeach
            </ul>
            
            <hr>
            
            <h6>Available Locations:</h6>
            <ul>
                @foreach(\App\Models\Location::all() as $loc)
                <li>
                    Location: {{ $loc->name }} (ID: {{ $loc->id }})
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

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
                <h1 class="m-0 fw-bold">Departments</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Departments</li>
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
                    <a href="{{ route('departments.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg me-2"></i>Add Department</a>
                </div>

                <!-- Departments Table -->
                <div class="card shadow">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold">Departments List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Department</th>
                                        <th>Location</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($departments as $index => $department)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $department->name }}</td>
                                        <td>
                                            @php
                                                $locationName = null;
                                                if ($department->location_id) {
                                                    $locationObj = \App\Models\Location::find($department->location_id);
                                                    if ($locationObj) {
                                                        $locationName = $locationObj->name;
                                                    }
                                                }
                                            @endphp
                                            
                                            @if($locationName)
                                                {{ $locationName }}
                                            @else
                                                <span class="text-muted">Not assigned</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($department->desc, 30) }}</td>
                                        <td><span class="badge {{ $department->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $department->status }}</span></td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-sm btn-success me-2"><i class="bi bi-pencil-square"></i></a>
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#archiveModal" 
                                                    data-id="{{ $department->id }}" data-name="{{ $department->name }}">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No department found</td>
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
<!-- /.Main content -->

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
                archiveForm.action = "{{ url('departments/archive-department/') }}/" + fieldId;
            });
        });
    });
</script>

@endsection